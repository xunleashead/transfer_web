<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => getenv('S3_REGION'),
    'credentials' => [
        'key'    => getenv('S3_KEY'),
        'secret' => getenv('S3_SECRET'),
    ],
]);

$bucket = getenv('S3_BUCKET');
