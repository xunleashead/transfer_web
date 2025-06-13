<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';
require_once 's3config.php'; // must initialize $s3Client here

use Aws\S3\Exception\S3Exception;

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$plan = $_SESSION["plan"];
$message = "";

// Handle deletion
if (isset($_GET["delete"]) && is_numeric($_GET["delete"])) {
    $file_id = $_GET["delete"];
    $stmt = $pdo->prepare("SELECT s3_key FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch();

    if ($file) {
        try {
            $s3Client->deleteObject([
                'Bucket' => 'my-file-hosting-bucket',
                'Key' => $file['s3_key']
            ]);
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
            $stmt->execute([$file_id, $user_id]);
            $message = "✅ File deleted successfully!";
        } catch (Exception $e) {
            $message = "❌ Error deleting file: " . $e->getMessage();
        }
    }
}

// Handle upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $maxFileSize = 50 * 1024 * 1024;
    if ($_FILES["file"]["size"] > $maxFileSize) {
        $message = "❌ File is too large. Maximum size is 50MB.";
    } elseif ($plan === "free") {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM files WHERE user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() >= 2) {
            $message = "❌ Free plan allows max 2 uploads.";
        }
    }

    if (empty($message)) {
        $file = $_FILES["file"];
        $filename = basename($file["name"]);
        $s3Key = time() . "_" . $filename;

        try {
            $result = $s3Client->putObject([
                'Bucket' => 'my-file-hosting-bucket',
                'Key'    => $s3Key,
                'SourceFile' => $file["tmp_name"],
                'ContentType' => $file["type"]
                
            ]);

            $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, s3_key) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $filename, $result['ObjectURL'], $s3Key]);

            $message = "✅ File uploaded successfully!";
        } catch (S3Exception $e) {
            $message = "❌ Error uploading: " . $e->getMessage();
        }
    }
}

// Load files
$stmt = $pdo->prepare("SELECT id, filename, filepath, upload_time FROM files WHERE user_id = ?");
$stmt->execute([$user_id]);
$files = $stmt->fetchAll();
?>

<!-- UI section is unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Files</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        background: #f4f7fa;
    }

    header {
        background: #007bff;
        color: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

    nav a {
        margin-left: 20px;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }

    .upload-section {
        padding: 30px 20px;
        max-width: 900px;
        margin: auto;
    }

    .message {
        background: #eaf7ea;
        border: 1px solid #b2dfb2;
        color: #2e7d32;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    .upload-form {
        background: white;
        padding: 20px;
        border: 1px solid #ddd;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        border-radius: 8px;
        margin-bottom: 40px;
    }

    .upload-form input[type="file"] {
        border: 2px dashed #ccc;
        padding: 15px;
        width: 100%;
        max-width: 400px;
        text-align: center;
        cursor: pointer;
        background: #f9f9f9;
    }

    .upload-form button {
        background: #28a745;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }

    .upload-form button:hover {
        background: #218838;
    }

    .file-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .file-card {
        background: white;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        text-align: center;
    }

    .file-card a {
        display: inline-block;
        margin: 10px 5px;
        padding: 6px 12px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
    }

    .file-card a:hover {
        background: #0056b3;
    }

    .file-card a[style*="background:#dc3545"] {
        background: #dc3545;
    }

    .timestamp {
        font-size: 13px;
        color: #666;
        margin-top: 10px;
    }
</style>

<body>
<header>
    <h1>📁 Upload Center</h1>
    <nav>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="logout.php">🚪 Logout</a>
    </nav>
</header>

<main class="upload-section">
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="MAX_FILE_SIZE" value="52428800">
        <input type="file" name="file" required>
        <button type="submit">📤 Upload File</button>
    </form>

    <h2 style="text-align:center;">Your Uploaded Files</h2>
    <div class="file-gallery">
        <?php if (count($files) === 0): ?>
            <p style="text-align:center;">No files uploaded yet.</p>
        <?php else: ?>
            <?php foreach ($files as $file): ?>
                <div class="file-card">
                    <p><strong><?php echo htmlspecialchars($file['filename']); ?></strong></p>
                    <a href="<?php echo $file['filepath']; ?>" download>⬇️ Download</a>
                    <p class="timestamp">Uploaded: <?php echo date('Y-m-d H:i', strtotime($file['upload_time'])); ?></p>
                    <a href="?delete=<?php echo $file['id']; ?>" onclick="return confirm('Delete this file?')" style="background:#dc3545;">🗑 Delete</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
</body>
