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
    $stmt = $pdo->prepare("SELECT filepath FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch();

    if ($file) {
        try {
            $s3Client->deleteObject([
                'Bucket' => 'my-file-hosting-bucket',
                'Key' => $file['filepath']
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
        /* Same styles from your original code */
        /* ... */
    </style>
</head>
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
</html>
