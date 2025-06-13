<!-- everything above remains unchanged (PHP logic) -->

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
                    <a href="<?php echo $file
