<?php
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$instructions_dir = 'uploads/instructions/';
$images = [];

if (is_dir($instructions_dir)) {
    $files = scandir($instructions_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = $instructions_dir . $file;
        }
    }
}

echo json_encode(['images' => $images]);
?>
