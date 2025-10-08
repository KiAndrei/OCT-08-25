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
    <title>Document Generation - Opiña Law Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?= time() ?>">
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
                <h1>Document Generation</h1>
                <p>Generate document storage and forms</p>
            </div>
            <div class="user-info">
                <img src="<?= htmlspecialchars($profile_image) ?>" alt="Admin" style="object-fit:cover;width:60px;height:60px;border-radius:50%;border:2px solid #1976d2;">
                <div class="user-details">
                    <h3><?php echo $_SESSION['admin_name']; ?></h3>
                    <p>System Administrator</p>
                </div>
            </div>
        </div>

        <!-- Document Generation Grid -->
        <div class="document-grid">
            <!-- Row 1 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Affidavit of Loss</h3>
                <p>Generate affidavit of loss document</p>
                <button class="btn btn-primary generate-btn" onclick="openModal('affidavitLossModal')">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Deed of Sale</h3>
                <p>Generate deed of sale document</p>
                <button class="btn btn-primary generate-btn">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Sworn Affidavit of Solo Parent</h3>
                <p>Generate sworn affidavit of solo parent</p>
                <button class="btn btn-primary generate-btn" onclick="openModal('soloParentModal')">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <!-- Row 2 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-female"></i>
                </div>
                <h3>Sworn Affidavit of Mother</h3>
                <p>Generate sworn affidavit of mother</p>
                <button class="btn btn-primary generate-btn">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-male"></i>
                </div>
                <h3>Sworn Affidavit of Father</h3>
                <p>Generate sworn affidavit of father</p>
                <button class="btn btn-primary generate-btn">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-female"></i>
                </div>
                <h3>Sworn Statement of Mother</h3>
                <p>Generate sworn statement of mother</p>
                <button class="btn btn-primary generate-btn" onclick="openModal('swornMotherModal')">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <!-- Row 3 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-male"></i>
                </div>
                <h3>Sworn Statement of Father</h3>
                <p>Generate sworn statement of father</p>
                <button class="btn btn-primary generate-btn">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Joint Affidavit of Two Disinterested Persons</h3>
                <p>Generate joint affidavit of two disinterested persons</p>
                <button class="btn btn-primary generate-btn" onclick="openModal('jointAffidavitModal')">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3>Agreement</h3>
                <p>Generate agreement document</p>
                <button class="btn btn-primary generate-btn">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>
        </div>
    </div>

    <!-- Affidavit of Loss Modal -->
    <div id="affidavitLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Affidavit of Loss</h2>
                <span class="close" onclick="closeModal('affidavitLossModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="affidavitLossForm" method="GET" action="files-generation/generate_affidavit_of_loss.php" target="_blank">
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sworn Affidavit of Solo Parent Modal -->
    <div id="soloParentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user"></i> Sworn Affidavit of Solo Parent</h2>
                <span class="close" onclick="closeModal('soloParentModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="soloParentForm" method="GET" action="files-generation/generate_affidavit_of_solo_parent.php" target="_blank">
                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter full name">
                    </div>

                    <div class="form-group">
                        <label for="completeAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="completeAddress" name="completeAddress" required placeholder="Enter complete address"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="childrenNames">Children Names <span class="required">*</span></label>
                        <textarea id="childrenNames" name="childrenNames" required placeholder="List all children names"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="yearsUnderCase">Years as Solo Parent <span class="required">*</span></label>
                        <input type="number" id="yearsUnderCase" name="yearsUnderCase" required placeholder="Enter number of years" min="1">
                    </div>

                    <div class="form-group">
                        <label for="reasonSection">Reason for Being Solo Parent <span class="required">*</span></label>
                        <select id="reasonSection" name="reasonSection" required onchange="toggleOtherReason()">
                            <option value="">Select reason</option>
                            <option value="Death of spouse">Death of spouse</option>
                            <option value="Abandonment by spouse">Abandonment by spouse</option>
                            <option value="Separation from spouse">Separation from spouse</option>
                            <option value="Divorce">Divorce</option>
                            <option value="Other reason, please state">Other reason, please state</option>
                        </select>
                    </div>

                    <div class="form-group conditional-field" id="otherReasonField">
                        <label for="otherReason">Please specify other reason</label>
                        <input type="text" id="otherReason" name="otherReason" placeholder="Enter other reason">
                    </div>

                    <div class="form-group">
                        <label for="employmentStatus">Employment Status <span class="required">*</span></label>
                        <select id="employmentStatus" name="employmentStatus" required onchange="toggleEmploymentFields()">
                            <option value="">Select employment status</option>
                            <option value="Employee and earning">Employee and earning</option>
                            <option value="Self-employed and earning">Self-employed and earning</option>
                            <option value="Un-employed and dependent upon">Un-employed and dependent upon</option>
                        </select>
                    </div>

                    <div class="form-group conditional-field" id="employeeAmountField">
                        <label for="employeeAmount">Monthly Income (Employee)</label>
                        <input type="text" id="employeeAmount" name="employeeAmount" placeholder="Enter monthly income">
                    </div>

                    <div class="form-group conditional-field" id="selfEmployedAmountField">
                        <label for="selfEmployedAmount">Monthly Income (Self-employed)</label>
                        <input type="text" id="selfEmployedAmount" name="selfEmployedAmount" placeholder="Enter monthly income">
                    </div>

                    <div class="form-group conditional-field" id="unemployedDependentField">
                        <label for="unemployedDependent">Dependent upon</label>
                        <input type="text" id="unemployedDependent" name="unemployedDependent" placeholder="Enter who you are dependent upon">
                    </div>

                    <div class="form-group">
                        <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="dateOfNotary" name="dateOfNotary" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sworn Statement of Mother Modal -->
    <div id="swornMotherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-female"></i> Sworn Statement of Mother</h2>
                <span class="close" onclick="closeModal('swornMotherModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="swornMotherForm" method="GET" action="files-generation/generate_sworn_statement_of_mother.php" target="_blank">
                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter full name">
                    </div>

                    <div class="form-group">
                        <label for="completeAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="completeAddress" name="completeAddress" required placeholder="Enter complete address"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="childName">Child's Name <span class="required">*</span></label>
                        <input type="text" id="childName" name="childName" required placeholder="Enter child's full name">
                    </div>

                    <div class="form-group">
                        <label for="birthDate">Birth Date <span class="required">*</span></label>
                        <input type="date" id="birthDate" name="birthDate" required>
                    </div>

                    <div class="form-group">
                        <label for="birthPlace">Birth Place <span class="required">*</span></label>
                        <input type="text" id="birthPlace" name="birthPlace" required placeholder="Enter place of birth">
                    </div>

                    <div class="form-group">
                        <label for="fatherName">Biological Father's Name <span class="required">*</span></label>
                        <input type="text" id="fatherName" name="fatherName" required placeholder="Enter biological father's name">
                    </div>

                    <div class="form-group">
                        <label for="cityRegistry">City Registry <span class="required">*</span></label>
                        <input type="text" id="cityRegistry" name="cityRegistry" required placeholder="Enter city where birth should be registered">
                    </div>

                    <div class="form-group">
                        <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="dateOfNotary" name="dateOfNotary" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Joint Affidavit Modal -->
    <div id="jointAffidavitModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-users"></i> Joint Affidavit of Two Disinterested Persons</h2>
                <span class="close" onclick="closeModal('jointAffidavitModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="jointAffidavitForm" method="GET" action="files-generation/generate_joint_affidavit_two-disinterested-person.php" target="_blank">
                    <div class="form-section">
                        <h3>Affiant Information</h3>
                        <div class="form-group">
                            <label for="affiant1Name">First Affiant Name <span class="required">*</span></label>
                            <input type="text" id="affiant1Name" name="affiant1Name" required placeholder="Enter first affiant's full name">
                        </div>

                        <div class="form-group">
                            <label for="affiant2Name">Second Affiant Name <span class="required">*</span></label>
                            <input type="text" id="affiant2Name" name="affiant2Name" required placeholder="Enter second affiant's full name">
                        </div>

                        <div class="form-group">
                            <label for="affiantAddress">Affiants' Address <span class="required">*</span></label>
                            <textarea id="affiantAddress" name="affiantAddress" required placeholder="Enter affiants' address"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Child Information</h3>
                        <div class="form-group">
                            <label for="childName">Child's Name <span class="required">*</span></label>
                            <input type="text" id="childName" name="childName" required placeholder="Enter child's full name">
                        </div>

                        <div class="form-group">
                            <label for="fatherName">Father's Name <span class="required">*</span></label>
                            <input type="text" id="fatherName" name="fatherName" required placeholder="Enter father's full name">
                        </div>

                        <div class="form-group">
                            <label for="motherName">Mother's Name <span class="required">*</span></label>
                            <input type="text" id="motherName" name="motherName" required placeholder="Enter mother's full name">
                        </div>

                        <div class="form-group">
                            <label for="birthDate">Birth Date <span class="required">*</span></label>
                            <input type="date" id="birthDate" name="birthDate" required>
                        </div>

                        <div class="form-group">
                            <label for="birthPlace">Birth Place <span class="required">*</span></label>
                            <input type="text" id="birthPlace" name="birthPlace" required placeholder="Enter place of birth">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="dateOfNotary" name="dateOfNotary" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
         .document-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .document-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 15px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .document-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .document-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
        }

        .document-info h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .document-info p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 1024px) {
            .document-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .document-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translate(-50%, -100%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: slideDown 0.4s ease-out;
        }

        .modal-header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            opacity: 0.7;
        }

        .modal-body {
            padding: 30px 30px 50px 30px;
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
            box-sizing: border-box;
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
            margin-top: 40px;
            margin-bottom: 20px;
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

        .conditional-field {
            display: none;
            margin-top: 10px;
        }

        .conditional-field.show {
            display: block;
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 16px;
        }
    </style>

    <script>
        function generateDocument(filePath) {
            // Open the document generation file in a new window
            window.open(filePath, '_blank');
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            // Set default date to today for all date fields in the modal
            const modal = document.getElementById(modalId);
            const dateFields = modal.querySelectorAll('input[type="date"]');
            dateFields.forEach(field => {
                if (!field.value) {
                    field.valueAsDate = new Date();
                }
            });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function previewDocument(formId, actionUrl) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }
            
            params.append('view_only', '1');
            
            const previewUrl = `${actionUrl}?${params.toString()}`;
            window.open(previewUrl, '_blank');
        }

        function toggleOtherReason() {
            const reasonSelect = document.getElementById('reasonSection');
            const otherReasonField = document.getElementById('otherReasonField');
            
            if (reasonSelect && otherReasonField) {
                if (reasonSelect.value === 'Other reason, please state') {
                    otherReasonField.classList.add('show');
                } else {
                    otherReasonField.classList.remove('show');
                    const otherReasonInput = document.getElementById('otherReason');
                    if (otherReasonInput) {
                        otherReasonInput.value = '';
                    }
                }
            }
        }

        function toggleEmploymentFields() {
            const employmentSelect = document.getElementById('employmentStatus');
            const employeeAmountField = document.getElementById('employeeAmountField');
            const selfEmployedAmountField = document.getElementById('selfEmployedAmountField');
            const unemployedDependentField = document.getElementById('unemployedDependentField');
            
            if (!employmentSelect) return;
            
            // Hide all conditional fields first
            [employeeAmountField, selfEmployedAmountField, unemployedDependentField].forEach(field => {
                if (field) {
                    field.classList.remove('show');
                }
            });
            
            // Clear all conditional field values
            ['employeeAmount', 'selfEmployedAmount', 'unemployedDependent'].forEach(id => {
                const field = document.getElementById(id);
                if (field) {
                    field.value = '';
                }
            });
            
            // Show relevant field based on selection
            if (employmentSelect.value === 'Employee and earning' && employeeAmountField) {
                employeeAmountField.classList.add('show');
            } else if (employmentSelect.value === 'Self-employed and earning' && selfEmployedAmountField) {
                selfEmployedAmountField.classList.add('show');
            } else if (employmentSelect.value === 'Un-employed and dependent upon' && unemployedDependentField) {
                unemployedDependentField.classList.add('show');
            }
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (modal.style.display === 'block') {
                        modal.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html> 