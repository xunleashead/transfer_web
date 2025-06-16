<?php
session_start();
require 'config.php';
require 's3config.php';

use Aws\S3\Exception\S3Exception;

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized.");
}

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$user_id = $_SESSION["user_id"];
$file_id = $_GET['id'];

// Fetch file info from DB
$stmt = $pdo->prepare("SELECT filename, s3_key FROM files WHERE id = ? AND user_id = ?");
$stmt->execute([$file_id, $user_id]);
$file = $stmt->fetch();

if (!$file) {
    die("File not found.");
}

$bucket = getenv('S3_BUCKET');
$s3Key = $file['s3_key'];
$filename = $file['filename'];

try {
    // Get file stream from S3
    $result = $s3Client->getObject([
        'Bucket' => $bucket,
        'Key' => $s3Key
    ]);

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $result['ContentType']);
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . $result['ContentLength']);
    echo $result['Body'];

} catch (S3Exception $e) {
    die("❌ Error downloading: " . $e->getMessage());
}
