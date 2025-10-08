<?php
require_once 'session_manager.php';
validateUserAccess('admin');
require_once 'config.php';
require_once 'audit_logger.php';
require_once 'action_logger_helper.php';

$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_image FROM user_form WHERE id=?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$profile_image = '';
if ($res && $row = $res->fetch_assoc()) {
    $profile_image = $row['profile_image'];
}
if (!$profile_image || !file_exists($profile_image)) {
    $profile_image = 'images/default-avatar.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affidavit of Loss Form - Opiña Law Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?= time() ?>">
    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form-header h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="images/logo.png" alt="Logo">
            <h2>Opiña Law Office</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="admin_documents.php"><i class="fas fa-file-alt"></i><span>Document Storage</span></a></li>
            <li><a href="admin_document_generation.php" class="active"><i class="fas fa-file-alt"></i><span>Document Generations</span></a></li>
            <li><a href="admin_schedule.php"><i class="fas fa-calendar-alt"></i><span>Schedule</span></a></li>
            <li><a href="admin_usermanagement.php"><i class="fas fa-users-cog"></i><span>User Management</span></a></li>
            <li><a href="admin_managecases.php"><i class="fas fa-gavel"></i><span>Case Management</span></a></li>
            <li><a href="admin_clients.php"><i class="fas fa-users"></i><span>My Clients</span></a></li>
            <li><a href="admin_messages.php"><i class="fas fa-comments"></i><span>Messages</span></a></li>
            <li><a href="admin_audit.php"><i class="fas fa-history"></i><span>Audit Trail</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>Affidavit of Loss Form</h1>
                <p>Fill out the form to generate the document</p>
            </div>
            <div class="user-info">
                <img src="<?= htmlspecialchars($profile_image) ?>" alt="Admin" style="object-fit:cover;width:60px;height:60px;border-radius:50%;border:2px solid #1976d2;">
                <div class="user-details">
                    <h3><?php echo $_SESSION['admin_name']; ?></h3>
                    <p>System Administrator</p>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Affidavit of Loss</h2>
                <p>Please fill out all required fields to generate the document</p>
            </div>

            <form id="affidavitForm" method="GET" action="files-generation/generate_affidavit_of_loss.php" target="_blank">
                <div class="form-group">
                    <label for="fullName">Full Name <span class="required">*</span></label>
                    <input type="text" id="fullName" name="fullName" required placeholder="Enter full name">
                </div>

                <div class="form-group">
                    <label for="completeAddress">Complete Address <span class="required">*</span></label>
                    <textarea id="completeAddress" name="completeAddress" required placeholder="Enter complete address"></textarea>
                </div>

                <div class="form-group">
                    <label for="specifyItemLost">Specify Item Lost <span class="required">*</span></label>
                    <input type="text" id="specifyItemLost" name="specifyItemLost" required placeholder="e.g., Driver's License, Passport, etc.">
                </div>

                <div class="form-group">
                    <label for="itemLost">Item Lost <span class="required">*</span></label>
                    <input type="text" id="itemLost" name="itemLost" required placeholder="e.g., Driver's License, Passport, etc.">
                </div>

                <div class="form-group">
                    <label for="itemDetails">Circumstances of Loss <span class="required">*</span></label>
                    <textarea id="itemDetails" name="itemDetails" required placeholder="Describe how the item was lost"></textarea>
                </div>

                <div class="form-group">
                    <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                    <input type="date" id="dateOfNotary" name="dateOfNotary" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="previewDocument()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewDocument() {
            const form = document.getElementById('affidavitForm');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            params.append('view_only', '1');
            
            const previewUrl = `files-generation/generate_affidavit_of_loss.php?${params.toString()}`;
            window.open(previewUrl, '_blank');
        }

        // Set default date to today
        document.getElementById('dateOfNotary').valueAsDate = new Date();
    </script>
</body>
</html>
