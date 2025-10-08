<?php
session_start();
if (!isset($_SESSION['admin_name']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$image_path = $input['image'] ?? '';

if (empty($image_path)) {
    echo json_encode(['success' => false, 'message' => 'No image specified']);
    exit();
}

// Security check - ensure the file is in the instructions directory
if (strpos($image_path, 'uploads/instructions/') !== 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid image path']);
    exit();
}

// Check if file exists
if (!file_exists($image_path)) {
    echo json_encode(['success' => false, 'message' => 'Image not found']);
    exit();
}

// Delete the file
if (unlink($image_path)) {
    echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
}
?>
