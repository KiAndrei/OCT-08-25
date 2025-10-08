<?php
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['instructions_image']) || $_FILES['instructions_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['instructions_image'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF files are allowed.']);
    exit();
}

// Validate file size (5MB max)
$max_size = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit();
}

// Create uploads/instructions directory if it doesn't exist
$upload_dir = 'uploads/instructions/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Check current image count
$existing_files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
if (count($existing_files) >= 5) {
    echo json_encode(['success' => false, 'message' => 'Maximum of 5 images allowed. Please delete an existing image first.']);
    exit();
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'instruction_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
$filepath = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => true, 'message' => 'Instructions image uploaded successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save the uploaded file.']);
}
?>
