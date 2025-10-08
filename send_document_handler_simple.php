<?php
require_once 'session_manager.php';
require_once 'config.php';
require_once 'audit_logger.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Validate user access
validateUserAccess('client');
$client_id = $_SESSION['user_id'];

// Get client name for logging
$stmt = $conn->prepare("SELECT name FROM user_form WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$res = $stmt->get_result();
$client_name = '';
if ($res && $row = $res->fetch_assoc()) {
    $client_name = $row['name'];
}

// Get form data
$form_type = $_POST['form_type'] ?? '';
$form_data_json = $_POST['form_data'] ?? '';

// Debug logging
error_log("Document Handler Debug - Form Type: " . $form_type);
error_log("Document Handler Debug - Form Data JSON: " . $form_data_json);

// Parse JSON form data
$form_data = json_decode($form_data_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Document Handler Debug - JSON Parse Error: " . json_last_error_msg());
    echo json_encode(['status' => 'error', 'message' => 'Invalid form data format: ' . json_last_error_msg()]);
    exit;
}

error_log("Document Handler Debug - Parsed Form Data: " . print_r($form_data, true));

if (empty($form_type) || empty($form_data)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// Generate unique request ID with timestamp to prevent duplicates
$request_id = 'DOC_' . date('YmdHis') . '_' . str_pad($client_id, 4, '0', STR_PAD_LEFT) . '_' . rand(1000, 9999);

// Prepare data for database insertion
$full_name = $form_data['fullName'] ?? '';
$address = $form_data['completeAddress'] ?? $form_data['fullAddress'] ?? '';
$gender = $form_data['gender'] ?? '';

// First, add the new columns if they don't exist
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS document_type VARCHAR(100) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS document_data TEXT DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS pdf_file_path VARCHAR(500) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS pdf_filename VARCHAR(255) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

// Check for duplicate submission within the last 5 minutes (but allow resubmission of rejected documents)
$check_stmt = $conn->prepare("
    SELECT id, status FROM client_request_form 
    WHERE client_id = ? AND document_type = ? AND submitted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY submitted_at DESC LIMIT 1
");
$check_stmt->bind_param("is", $client_id, $form_type);
$check_stmt->execute();
$duplicate_check = $check_stmt->get_result();

if ($duplicate_check->num_rows > 0) {
    $duplicate_row = $duplicate_check->fetch_assoc();
    // Allow resubmission if the previous document was rejected
    if ($duplicate_row['status'] !== 'Rejected') {
        echo json_encode(['status' => 'error', 'message' => 'Duplicate submission detected. Please wait before submitting again.']);
        exit;
    }
}

// Insert document request into database (without PDF for now)
$stmt = $conn->prepare("
    INSERT INTO client_request_form 
    (request_id, client_id, full_name, address, gender, valid_id_front_path, valid_id_front_filename, valid_id_back_path, valid_id_back_filename, privacy_consent, status, document_type, document_data, submitted_at) 
    VALUES (?, ?, ?, ?, ?, '', '', '', '', 1, 'Pending', ?, ?, NOW())
");

$document_type = $form_type;
$document_data = json_encode($form_data);

$stmt->bind_param("sisssss", 
    $request_id, 
    $client_id, 
    $full_name, 
    $address, 
    $gender, 
    $document_type,
    $document_data
);

if ($stmt->execute()) {
    // Debug logging
    error_log("Document Handler Debug - Database insertion successful for request ID: " . $request_id);
    
    // Log to audit trail
    global $auditLogger;
    $auditLogger->logAction(
        $client_id,
        $client_name,
        'client',
        'Document Submission',
        'Document Generation',
        "Submitted $form_type document with request ID: $request_id",
        'success',
        'medium'
    );
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Document sent successfully!',
        'request_id' => $request_id,
        'debug_info' => [
            'client_id' => $client_id,
            'form_type' => $form_type,
            'document_type' => $document_type
        ]
    ]);
} else {
    // Debug logging
    error_log("Document Handler Debug - Database insertion failed: " . $conn->error);
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to send document. Please try again.',
        'debug_info' => [
            'error' => $conn->error,
            'client_id' => $client_id,
            'form_type' => $form_type
        ]
    ]);
}
?>


