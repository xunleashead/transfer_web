<!-- everything above remains unchanged (PHP logic) -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Files</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: #f4f6f9;
      padding: 20px;
    }

    header {
      background: #4a90e2;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      color: white;
      margin-bottom: 30px;
    }

    header h1 {
      margin-bottom: 10px;
    }

    nav a {
      color: white;
      margin: 0 10px;
      text-decoration: none;
      font-weight: bold;
    }

    .upload-section {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .upload-form {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      margin-bottom: 30px;
    }

    .upload-form input[type="file"] {
      padding: 8px;
    }

    .upload-form button {
      background: #28a745;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .upload-form button:hover {
      background: #218838;
    }

    .file-gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 20px;
    }

    .file-card {
      background: #fefefe;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      text-align: center;
    }

    .file-card p {
      margin-bottom: 10px;
    }

    .file-card a {
      display: inline-block;
      padding: 6px 12px;
      margin: 5px 0;
      background: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }

    .file-card a:hover {
      background: #0056b3;
    }

    .file-card a[style*="background:#dc3545;"] {
      background: #dc3545;
    }

    .file-card .timestamp {
      font-size: 13px;
      color: #666;
    }

    .message {
      text-align: center;
      margin-bottom: 20px;
      padding: 10px;
      background: #e9f6ec;
      color: #2e7d32;
      border-left: 5px solid #2e7d32;
      border-radius: 5px;
    }

    .message:empty {
      display: none;
    }

    @media (max-width: 600px) {
      .upload-section {
        padding: 20px;
      }
    }
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
            <a href="<?php echo $file['filepath']; ?>" download>⬇️ Download</a><br>
            <p class="timestamp">Uploaded: <?php echo date('Y-m-d H:i', strtotime($file['upload_time'])); ?></p>
            <a href="?delete=<?php echo $file['id']; ?>" onclick="return confirm('Delete this file?')" style="background:#dc3545;">🗑 Delete</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
