<?php
require_once 'session_manager.php';
validateUserAccess('client');
require_once 'config.php';
require_once 'audit_logger.php';
require_once 'action_logger_helper.php';
$client_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_image FROM user_form WHERE id=?");
$stmt->bind_param("i", $client_id);
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
    <link rel="stylesheet" href="assets/css/document-styles.css?v=<?= time() ?>">
    <style>
        /* Data Preview Styles */
        .data-preview {
            display: none;
        }
        
        .data-preview-header {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .data-preview-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .data-preview-content {
            display: grid;
            gap: 15px;
        }
        
        .data-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .data-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .data-value {
            color: #333;
            font-size: 0.95rem;
            line-height: 1.4;
            word-wrap: break-word;
            background: white;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        
        .data-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar client-sidebar">
        <div class="sidebar-header">
        <img src="images/logo.png" alt="Logo">  
            <h2>Opiña Law Office</h2>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="client_dashboard.php" title="View your case overview, statistics, and recent activities">
                    <div class="button-content">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </div>
                    <small>Overview & Statistics</small>
                </a>
            </li>
            <li>
                <a href="client_documents.php" title="Generate legal documents like affidavits and sworn statements">
                    <div class="button-content">
                        <i class="fas fa-file-alt"></i>
                        <span>Document Generation</span>
                    </div>
                    <small>Create Legal Documents</small>
                </a>
            </li>
            <li>
                <a href="client_cases.php" title="Track your legal cases, view case details, and upload documents">
                    <div class="button-content">
                        <i class="fas fa-gavel"></i>
                        <span>My Cases</span>
                    </div>
                    <small>Track Legal Cases</small>
                </a>
            </li>
            <li>
                <a href="client_schedule.php" title="View your upcoming appointments, hearings, and court schedules">
                    <div class="button-content">
                        <i class="fas fa-calendar-alt"></i>
                        <span>My Schedule</span>
                    </div>
                    <small>Appointments & Hearings</small>
                </a>
            </li>
            <li>
                <a href="client_messages.php" title="Communicate with your attorney and legal team">
                    <div class="button-content">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </div>
                    <small>Chat with Attorney</small>
                </a>
            </li>

        </ul>
    </div>

    <!-- Sworn Affidavit (Solo Parent) Modal -->
    <div id="swornAffidavitSoloParentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-signature"></i> Sworn Affidavit (Solo Parent)</h2>
                <span class="close" onclick="closeSwornAffidavitSoloParentModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="swornAffidavitSoloParentForm" class="modal-form">
                    <div class="form-group">
                        <label for="saSoloFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="saSoloFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    <div class="form-group">
                        <label for="saSoloCompleteAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="saSoloCompleteAddress" name="completeAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Child/Children <span class="required">*</span></label>
                        <div id="saChildrenContainer" style="display: grid; gap: 8px;"></div>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-secondary" onclick="saAddChildRow()">+ Add Child</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="saYearsUnderCase">Number of Years as Solo Parent <span class="required">*</span></label>
                        <input type="text" id="saYearsUnderCase" name="yearsUnderCase" required placeholder="e.g., 5 years">
                    </div>
                    <div class="form-group">
                        <label>Reason for Being Solo Parent <span class="required">*</span></label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="reasonSection" value="Left the family home and abandoned us" onchange="saToggleOtherReason()" style="width: auto; margin: 0;"> Left the family home and abandoned us
                            </label>
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="reasonSection" value="Died last" onchange="saToggleOtherReason()" style="width: auto; margin: 0;"> Died last
                            </label>
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="reasonSection" value="Other reason, please state" onchange="saToggleOtherReason()" style="width: auto; margin: 0;"> Other reason, please state
                            </label>
                        </div>
                        <div id="saOtherReasonWrap" style="display:none; margin-top:8px;">
                            <input type="text" id="saOtherReason" name="otherReason" placeholder="Please specify other reason" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white; width: 100%;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Employment Status <span class="required">*</span></label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="employmentStatus" value="Employee and earning" onchange="saToggleEmploymentFields()" style="width: auto; margin: 0;"> Employed and earning Php
                            </label>
                            <div id="saEmployeeAmountWrap" class="conditional-field" style="display:none; margin-left:20px;">
                                <input type="text" id="saEmployeeAmount" name="employeeAmount" placeholder="Monthly Income Amount" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white; width: 100%;">
                            </div>
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="employmentStatus" value="Self-employed and earning" onchange="saToggleEmploymentFields()" style="width: auto; margin: 0;"> Self-employed and earning Php
                            </label>
                            <div id="saSelfEmployedAmountWrap" class="conditional-field" style="display:none; margin-left:20px;">
                                <input type="text" id="saSelfEmployedAmount" name="selfEmployedAmount" placeholder="Monthly Income Amount" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white; width: 100%;">
                            </div>
                            <label class="radio-item" style="display:flex; align-items:center; gap:8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="employmentStatus" value="Un-employed and dependent upon" onchange="saToggleEmploymentFields()" style="width: auto; margin: 0;"> Un-employed and dependent upon
                            </label>
                            <div id="saUnemployedDependentWrap" class="conditional-field" style="display:none; margin-left:20px;">
                                <input type="text" id="saUnemployedDependent" name="unemployedDependent" placeholder="Dependent upon: parents, relatives, etc." style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white; width: 100%;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="saDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="saDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeSwornAffidavitSoloParentModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saSave()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="saViewData()" class="btn btn-primary" style="background:#28a745;">View Data</button>
                    </div>
                </form>
                <div id="saDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-file-signature"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="saPreviewFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Complete Address</label>
                            <div class="data-value" id="saPreviewCompleteAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Children</label>
                            <div class="data-value" id="saPreviewChildren">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Number of Years as Solo Parent</label>
                            <div class="data-value" id="saPreviewYears">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Reason for Being Solo Parent</label>
                            <div class="data-value" id="saPreviewReason">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Employment Status</label>
                            <div class="data-value" id="saPreviewEmployment">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="saPreviewDate">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="saHideData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="saSend()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Senior ID Loss Modal -->
    <div id="seniorIDLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-id-card"></i> Affidavit of Loss (Senior ID)</h2>
                <span class="close" onclick="closeSeniorIDLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="seniorIDLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="seniorFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="seniorFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="seniorCompleteAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="seniorCompleteAddress" name="completeAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="seniorRelationship">Relationship to Senior Citizen <span class="required">*</span></label>
                        <input type="text" id="seniorRelationship" name="relationship" required placeholder="e.g., Son, Daughter, Spouse, etc.">
                    </div>
                    
                    <div class="form-group">
                        <label for="seniorCitizenName">Senior Citizen's Full Name <span class="required">*</span></label>
                        <input type="text" id="seniorCitizenName" name="seniorCitizenName" required placeholder="Enter the senior citizen's complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="seniorDetailsOfLoss">Details of Loss <span class="required">*</span></label>
                        <textarea id="seniorDetailsOfLoss" name="detailsOfLoss" required placeholder="Describe the circumstances of how the Senior ID was lost" style="resize: vertical; min-height: 80px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Please provide detailed information about when, where, and how the Senior ID was lost</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="seniorDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="seniorDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeSeniorIDLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveSeniorIDLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewSeniorIDLossData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="seniorIDLossDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-id-card"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="previewSeniorFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Complete Address</label>
                            <div class="data-value" id="previewSeniorCompleteAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Relationship to Senior Citizen</label>
                            <div class="data-value" id="previewSeniorRelationship">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Senior Citizen's Full Name</label>
                            <div class="data-value" id="previewSeniorCitizenName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Details of Loss</label>
                            <div class="data-value" id="previewSeniorDetailsOfLoss">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewSeniorDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hideSeniorIDLossData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendSeniorIDLoss()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php 
        $page_title = 'Document Generation';
        $page_subtitle = 'Generate and manage your document storage';
        include 'components/profile_header.php'; 
        ?>

         <!-- Document Generation Grid -->
         <div class="document-grid">
            <!-- Row 1 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Affidavit of Loss</h3>
                <p>Generate affidavit of loss document</p>
                <button onclick="openAffidavitLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>Affidavit of Loss<br><span style="font-size: 0.9em; font-weight: 500;">(Senior ID)</span></h3>
                <p>Generate affidavit of loss for senior ID</p>
                <button onclick="openSeniorIDLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Sworn Affidavit of Solo Parent</h3>
                <p>Generate sworn affidavit of solo parent</p>
                <button onclick="openSwornAffidavitSoloParentModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <!-- Row 2 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-female"></i>
                </div>
                <h3>Sworn Affidavit of Mother</h3>
                <p>Generate sworn affidavit of mother</p>
                <button onclick="openSwornAffidavitMotherModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-wheelchair"></i>
                </div>
                <h3>Affidavit of Loss<br><span style="font-size: 0.9em; font-weight: 500;">(PWD ID)</span></h3>
                <p>Generate affidavit of loss for PWD ID</p>
                <button onclick="openPWDLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Affidavit of Loss (Boticab Booklet/ID)</h3>
                <p>Generate affidavit of loss for Boticab booklet/ID</p>
                <button onclick="openBoticabLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <!-- Row 3 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Joint Affidavit (Two Disinterested Person)</h3>
                <p>Generate joint affidavit of two disinterested person</p>
                <button onclick="openJointAffidavitModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Joint Affidavit of Two Disinterested Person (Solo Parent)</h3>
                <p>Generate joint affidavit of two disinterested person (solo parent)</p>
                <a href="files-generation/generate_joint_affidavit_solo_parent.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3>Sworn Affidavit (Solo Parent)</h3>
                <p>Generate sworn affidavit for solo parent</p>
                <a href="files-generation/generate_sworn_affidavit_of_solo_parent.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>
        </div>
    </div>

    <!-- Affidavit of Loss Modal -->
    <div id="affidavitLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Affidavit of Loss</h2>
                <span class="close" onclick="closeAffidavitLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="affidavitLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="completeAddress">Complete Address <span class="required">*</span></label>
                        <input type="text" id="completeAddress" name="completeAddress" required placeholder="Enter your complete address">
                    </div>
                    
                    <div class="form-group">
                        <label for="specifyItemLost">Specify Item Lost <span class="required">*</span></label>
                        <input type="text" id="specifyItemLost" name="specifyItemLost" required placeholder="e.g., Driver's License, Passport, ID Card">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemLost">Item Lost <span class="required">*</span></label>
                        <input type="text" id="itemLost" name="itemLost" required placeholder="Describe the specific item that was lost">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemDetails">Item Details <span class="required">*</span></label>
                        <input type="text" id="itemDetails" name="itemDetails" required placeholder="Provide detailed description of the lost item">
                    </div>
                    
                    <div class="form-group">
                        <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="dateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeAffidavitLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveAffidavitLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewAffidavitLossData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="affidavitLossDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-exclamation-triangle"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="previewFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Complete Address</label>
                            <div class="data-value" id="previewCompleteAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Specify Item Lost</label>
                            <div class="data-value" id="previewSpecifyItemLost">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Item Lost</label>
                            <div class="data-value" id="previewItemLost">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Item Details</label>
                            <div class="data-value" id="previewItemDetails">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hideAffidavitLossData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendAffidavitLoss()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- PWD ID Loss Modal -->
    <div id="pwdLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-wheelchair"></i> Affidavit of Loss (PWD ID)</h2>
                <span class="close" onclick="closePWDLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="pwdLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="pwdFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="pwdFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdFullAddress">Full Address <span class="required">*</span></label>
                        <textarea id="pwdFullAddress" name="fullAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdDetailsOfLoss">Details of Loss <span class="required">*</span></label>
                        <textarea id="pwdDetailsOfLoss" name="detailsOfLoss" required placeholder="Describe the circumstances of how the PWD ID was lost" style="resize: vertical; min-height: 80px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Please provide detailed information about when, where, and how the PWD ID was lost</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="pwdDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closePWDLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="savePWDLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewPWDLossData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="pwdLossDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-wheelchair"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="previewPwdFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Full Address</label>
                            <div class="data-value" id="previewPwdFullAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Details of Loss</label>
                            <div class="data-value" id="previewPwdDetailsOfLoss">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewPwdDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hidePWDLossData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendPWDLoss()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boticab Booklet/ID Loss Modal -->
    <div id="boticabLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book"></i> Affidavit of Loss (Boticab Booklet/ID)</h2>
                <span class="close" onclick="closeBoticabLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="boticabLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="boticabFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="boticabFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabFullAddress">Full Address <span class="required">*</span></label>
                        <textarea id="boticabFullAddress" name="fullAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabDetailsOfLoss">Details of Loss <span class="required">*</span></label>
                        <textarea id="boticabDetailsOfLoss" name="detailsOfLoss" required placeholder="Describe the circumstances of how the Boticab booklet/ID was lost" style="resize: vertical; min-height: 80px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Please provide detailed information about when, where, and how the Boticab booklet/ID was lost</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="boticabDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeBoticabLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveBoticabLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewBoticabLossData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="boticabLossDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-book"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="previewBoticabFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Full Address</label>
                            <div class="data-value" id="previewBoticabFullAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Details of Loss</label>
                            <div class="data-value" id="previewBoticabDetailsOfLoss">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewBoticabDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hideBoticabLossData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendBoticabLoss()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Joint Affidavit (Two Disinterested Person) Modal -->
    <div id="jointAffidavitModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-users"></i> Joint Affidavit (Two Disinterested Person)</h2>
                <span class="close" onclick="closeJointAffidavitModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="jointAffidavitForm" class="modal-form">
                    <div class="form-group">
                        <label for="firstPersonName">First Person Full Name <span class="required">*</span></label>
                        <input type="text" id="firstPersonName" name="firstPersonName" required placeholder="Enter first person's complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="secondPersonName">Second Person Full Name <span class="required">*</span></label>
                        <input type="text" id="secondPersonName" name="secondPersonName" required placeholder="Enter second person's complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="firstPersonAddress">First Person Complete Address <span class="required">*</span></label>
                        <textarea id="firstPersonAddress" name="firstPersonAddress" required placeholder="Enter first person's complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="secondPersonAddress">Second Person Complete Address <span class="required">*</span></label>
                        <textarea id="secondPersonAddress" name="secondPersonAddress" required placeholder="Enter second person's complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="childName">Name of Child <span class="required">*</span></label>
                        <input type="text" id="childName" name="childName" required placeholder="Enter child's full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="placeOfBirth">Place of Birth <span class="required">*</span></label>
                        <input type="text" id="placeOfBirth" name="placeOfBirth" required placeholder="Enter place of birth">
                    </div>
                    
                    <div class="form-group">
                        <label for="fatherName">Father's Full Name <span class="required">*</span></label>
                        <input type="text" id="fatherName" name="fatherName" required placeholder="Enter father's complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="motherName">Mother's Full Name <span class="required">*</span></label>
                        <input type="text" id="motherName" name="motherName" required placeholder="Enter mother's complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="childNameNumber4">Name of Child (for number 4) <span class="required">*</span></label>
                        <input type="text" id="childNameNumber4" name="childNameNumber4" required placeholder="Enter child's name for late registration">
                    </div>
                    
                    <div class="form-group">
                        <label for="jointDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="jointDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeJointAffidavitModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveJointAffidavit()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewJointAffidavitData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="jointAffidavitDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-users"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">First Person Full Name</label>
                            <div class="data-value" id="previewFirstPersonName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Second Person Full Name</label>
                            <div class="data-value" id="previewSecondPersonName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">First Person Complete Address</label>
                            <div class="data-value" id="previewFirstPersonAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Second Person Complete Address</label>
                            <div class="data-value" id="previewSecondPersonAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Name of Child</label>
                            <div class="data-value" id="previewChildName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Birth</label>
                            <div class="data-value" id="previewDateOfBirth">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Place of Birth</label>
                            <div class="data-value" id="previewPlaceOfBirth">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Father's Full Name</label>
                            <div class="data-value" id="previewFatherName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Mother's Full Name</label>
                            <div class="data-value" id="previewMotherName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Name of Child (for number 4)</label>
                            <div class="data-value" id="previewChildNameNumber4">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewJointDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hideJointAffidavitData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendJointAffidavit()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sworn Affidavit of Mother Modal -->
    <div id="swornAffidavitMotherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-female"></i> Sworn Affidavit of Mother</h2>
                <span class="close" onclick="closeSwornAffidavitMotherModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="swornAffidavitMotherForm" class="modal-form">
                    <div class="form-group">
                        <label for="swornMotherFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="swornMotherFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="swornMotherCompleteAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="swornMotherCompleteAddress" name="completeAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="swornMotherChildName">Name of Child <span class="required">*</span></label>
                        <input type="text" id="swornMotherChildName" name="childName" required placeholder="Enter child's full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="swornMotherBirthDate">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="swornMotherBirthDate" name="birthDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="swornMotherBirthPlace">Place of Birth <span class="required">*</span></label>
                        <input type="text" id="swornMotherBirthPlace" name="birthPlace" required placeholder="Enter place of birth">
                    </div>
                    
                    <div class="form-group">
                        <label for="swornMotherDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="swornMotherDateOfNotary" name="dateOfNotary" required min="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeSwornAffidavitMotherModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveSwornAffidavitMother()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewSwornAffidavitMotherData()" class="btn btn-primary" style="background: #28a745;">View Data</button>
                    </div>
                </form>
                
                <!-- Data Preview Section -->
                <div id="swornAffidavitMotherDataPreview" class="data-preview">
                    <div class="data-preview-header">
                        <h3><i class="fas fa-female"></i> Data Preview</h3>
                    </div>
                    <div class="data-preview-content">
                        <div class="data-item">
                            <label class="data-label">Full Name</label>
                            <div class="data-value" id="previewSwornMotherFullName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Complete Address</label>
                            <div class="data-value" id="previewSwornMotherCompleteAddress">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Name of Child</label>
                            <div class="data-value" id="previewSwornMotherChildName">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Birth</label>
                            <div class="data-value" id="previewSwornMotherBirthDate">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Place of Birth</label>
                            <div class="data-value" id="previewSwornMotherBirthPlace">-</div>
                        </div>
                        <div class="data-item">
                            <label class="data-label">Date of Notary</label>
                            <div class="data-value" id="previewSwornMotherDateOfNotary">-</div>
                        </div>
                    </div>
                    <div class="data-actions">
                        <button type="button" onclick="hideSwornAffidavitMotherData()" class="btn btn-secondary">Edit</button>
                        <button type="button" onclick="sendSwornAffidavitMother()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <script src="assets/js/modal-functions.js?v=<?= time() ?>"></script>
    <script src="assets/js/form-handlers.js?v=<?= time() ?>"></script>
    <script src="assets/js/document-actions.js?v=<?= time() ?>"></script>
    <script src="assets/js/document-viewer.js?v=<?= time() ?>"></script>
    
    <script>
        // Profile Dropdown Functions
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }
        
        function editProfile() {
            document.getElementById('editProfileModal').style.display = 'block';
            // Close dropdown when opening modal
            document.getElementById('profileDropdown').classList.remove('show');
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
        }
        
        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('img') && !event.target.closest('.profile-dropdown')) {
                const dropdowns = document.getElementsByClassName('profile-dropdown-content');
                for (let dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
        }

        // Data Preview Functions
        function viewAffidavitLossData() {
            const form = document.getElementById('affidavitLossForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewCompleteAddress').textContent = formData.get('completeAddress') || '-';
            document.getElementById('previewSpecifyItemLost').textContent = formData.get('specifyItemLost') || '-';
            document.getElementById('previewItemLost').textContent = formData.get('itemLost') || '-';
            document.getElementById('previewItemDetails').textContent = formData.get('itemDetails') || '-';
            document.getElementById('previewDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('affidavitLossDataPreview').style.display = 'block';
        }

        function hideAffidavitLossData() {
            document.getElementById('affidavitLossForm').style.display = 'block';
            document.getElementById('affidavitLossDataPreview').style.display = 'none';
        }

        // Sworn Affidavit (Solo Parent) Modal Functions
        function openSwornAffidavitSoloParentModal() {
            document.getElementById('swornAffidavitSoloParentModal').style.display = 'block';
            if (!document.getElementById('saChildrenContainer').hasChildNodes()) saAddChildRow();
        }
        function closeSwornAffidavitSoloParentModal() {
            document.getElementById('swornAffidavitSoloParentModal').style.display = 'none';
        }
        function saAddChildRow() {
            const container = document.getElementById('saChildrenContainer');
            const row = document.createElement('div');
            row.style.display = 'grid';
            row.style.gridTemplateColumns = '2fr 1fr auto';
            row.style.gap = '8px';
            row.style.alignItems = 'center';
            row.innerHTML = `
                <input type="text" name="childrenNames[]" placeholder="Child's full name" required style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white;">
                <input type="text" name="childrenAges[]" placeholder="Age" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; background: white;">
                <button type="button" class="btn btn-secondary" onclick="this.parentElement.remove()">Remove</button>
            `;
            container.appendChild(row);
        }
        function saToggleOtherReason() {
            const value = document.querySelector('input[name="reasonSection"]:checked')?.value;
            document.getElementById('saOtherReasonWrap').style.display = value === 'Other reason, please state' ? 'block' : 'none';
        }
        function saToggleEmploymentFields() {
            const value = document.querySelector('input[name="employmentStatus"]:checked')?.value;
            document.getElementById('saEmployeeAmountWrap').style.display = value === 'Employee and earning' ? 'block' : 'none';
            document.getElementById('saSelfEmployedAmountWrap').style.display = value === 'Self-employed and earning' ? 'block' : 'none';
            document.getElementById('saUnemployedDependentWrap').style.display = value === 'Un-employed and dependent upon' ? 'block' : 'none';
        }
        function saViewData() {
            const form = document.getElementById('swornAffidavitSoloParentForm');
            const formData = new FormData(form);
            document.getElementById('saPreviewFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('saPreviewCompleteAddress').textContent = formData.get('completeAddress') || '-';
            const names = formData.getAll('childrenNames[]');
            const ages = formData.getAll('childrenAges[]');
            const rows = names.map((n, i) => {
                const age = ages[i] || '';
                return n ? (age ? `${n} (${age})` : n) : null;
            }).filter(Boolean);
            document.getElementById('saPreviewChildren').textContent = rows.length ? rows.join(', ') : '-';
            document.getElementById('saPreviewYears').textContent = formData.get('yearsUnderCase') || '-';
            const reason = formData.get('reasonSection') || '-';
            const reasonOut = reason === 'Other reason, please state' ? `${reason}: ${formData.get('otherReason') || ''}` : reason;
            document.getElementById('saPreviewReason').textContent = reasonOut;
            let employment = formData.get('employmentStatus') || '-';
            if (employment === 'Employee and earning') {
                const amt = formData.get('employeeAmount');
                if (amt) employment += ` (Php ${amt}/month)`;
            } else if (employment === 'Self-employed and earning') {
                const amt = formData.get('selfEmployedAmount');
                if (amt) employment += ` (Php ${amt}/month)`;
            } else if (employment === 'Un-employed and dependent upon') {
                const dep = formData.get('unemployedDependent');
                if (dep) employment += ` (${dep})`;
            }
            document.getElementById('saPreviewEmployment').textContent = employment;
            document.getElementById('saPreviewDate').textContent = formData.get('dateOfNotary') || '-';
            form.style.display = 'none';
            document.getElementById('saDataPreview').style.display = 'block';
        }
        function saHideData() {
            document.getElementById('swornAffidavitSoloParentForm').style.display = 'block';
            document.getElementById('saDataPreview').style.display = 'none';
        }
        function saSave() {
            // Save functionality - can be implemented later if needed
            alert('Data saved successfully!');
        }
        function saSend() {
            if (!confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) return;
            const form = document.getElementById('swornAffidavitSoloParentForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                completeAddress: formData.get('completeAddress'),
                childrenNames: formData.getAll('childrenNames[]'),
                childrenAges: formData.getAll('childrenAges[]'),
                yearsUnderCase: formData.get('yearsUnderCase'),
                reasonSection: formData.get('reasonSection'),
                otherReason: formData.get('otherReason'),
                employmentStatus: formData.get('employmentStatus'),
                employeeAmount: formData.get('employeeAmount'),
                selfEmployedAmount: formData.get('selfEmployedAmount'),
                unemployedDependent: formData.get('unemployedDependent'),
                dateOfNotary: formData.get('dateOfNotary')
            };
            sendDocumentToEmployee('soloParent', data);
        }

        function viewPWDLossData() {
            const form = document.getElementById('pwdLossForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewPwdFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewPwdFullAddress').textContent = formData.get('fullAddress') || '-';
            document.getElementById('previewPwdDetailsOfLoss').textContent = formData.get('detailsOfLoss') || '-';
            document.getElementById('previewPwdDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('pwdLossDataPreview').style.display = 'block';
        }

        function hidePWDLossData() {
            document.getElementById('pwdLossForm').style.display = 'block';
            document.getElementById('pwdLossDataPreview').style.display = 'none';
        }

        function viewBoticabLossData() {
            const form = document.getElementById('boticabLossForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewBoticabFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewBoticabFullAddress').textContent = formData.get('fullAddress') || '-';
            document.getElementById('previewBoticabDetailsOfLoss').textContent = formData.get('detailsOfLoss') || '-';
            document.getElementById('previewBoticabDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('boticabLossDataPreview').style.display = 'block';
        }

        function hideBoticabLossData() {
            document.getElementById('boticabLossForm').style.display = 'block';
            document.getElementById('boticabLossDataPreview').style.display = 'none';
        }

        // Joint Affidavit Modal Functions
        function openJointAffidavitModal() {
            document.getElementById('jointAffidavitModal').style.display = 'block';
        }

        function closeJointAffidavitModal() {
            document.getElementById('jointAffidavitModal').style.display = 'none';
        }

        function saveJointAffidavit() {
            // Save functionality - can be implemented later if needed
            alert('Data saved successfully!');
        }

        function viewJointAffidavitData() {
            const form = document.getElementById('jointAffidavitForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewFirstPersonName').textContent = formData.get('firstPersonName') || '-';
            document.getElementById('previewSecondPersonName').textContent = formData.get('secondPersonName') || '-';
            document.getElementById('previewFirstPersonAddress').textContent = formData.get('firstPersonAddress') || '-';
            document.getElementById('previewSecondPersonAddress').textContent = formData.get('secondPersonAddress') || '-';
            document.getElementById('previewChildName').textContent = formData.get('childName') || '-';
            document.getElementById('previewDateOfBirth').textContent = formData.get('dateOfBirth') || '-';
            document.getElementById('previewPlaceOfBirth').textContent = formData.get('placeOfBirth') || '-';
            document.getElementById('previewFatherName').textContent = formData.get('fatherName') || '-';
            document.getElementById('previewMotherName').textContent = formData.get('motherName') || '-';
            document.getElementById('previewChildNameNumber4').textContent = formData.get('childNameNumber4') || '-';
            document.getElementById('previewJointDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('jointAffidavitDataPreview').style.display = 'block';
        }

        function hideJointAffidavitData() {
            document.getElementById('jointAffidavitForm').style.display = 'block';
            document.getElementById('jointAffidavitDataPreview').style.display = 'none';
        }

        function sendJointAffidavit() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('jointAffidavitForm');
                const formData = new FormData(form);
                
                const data = {
                    firstPersonName: formData.get('firstPersonName'),
                    secondPersonName: formData.get('secondPersonName'),
                    firstPersonAddress: formData.get('firstPersonAddress'),
                    secondPersonAddress: formData.get('secondPersonAddress'),
                    childName: formData.get('childName'),
                    dateOfBirth: formData.get('dateOfBirth'),
                    placeOfBirth: formData.get('placeOfBirth'),
                    fatherName: formData.get('fatherName'),
                    motherName: formData.get('motherName'),
                    childNameNumber4: formData.get('childNameNumber4'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('jointAffidavit', data);
            }
        }

        // Sworn Affidavit of Mother Modal Functions
        function openSwornAffidavitMotherModal() {
            document.getElementById('swornAffidavitMotherModal').style.display = 'block';
        }

        function closeSwornAffidavitMotherModal() {
            document.getElementById('swornAffidavitMotherModal').style.display = 'none';
        }

        function saveSwornAffidavitMother() {
            // Save functionality - can be implemented later if needed
            alert('Data saved successfully!');
        }

        function viewSwornAffidavitMotherData() {
            const form = document.getElementById('swornAffidavitMotherForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewSwornMotherFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewSwornMotherCompleteAddress').textContent = formData.get('completeAddress') || '-';
            document.getElementById('previewSwornMotherChildName').textContent = formData.get('childName') || '-';
            document.getElementById('previewSwornMotherBirthDate').textContent = formData.get('birthDate') || '-';
            document.getElementById('previewSwornMotherBirthPlace').textContent = formData.get('birthPlace') || '-';
            document.getElementById('previewSwornMotherDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('swornAffidavitMotherDataPreview').style.display = 'block';
        }

        function hideSwornAffidavitMotherData() {
            document.getElementById('swornAffidavitMotherForm').style.display = 'block';
            document.getElementById('swornAffidavitMotherDataPreview').style.display = 'none';
        }

        function sendSwornAffidavitMother() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('swornAffidavitMotherForm');
                const formData = new FormData(form);
                
                const data = {
                    fullName: formData.get('fullName'),
                    completeAddress: formData.get('completeAddress'),
                    childName: formData.get('childName'),
                    birthDate: formData.get('birthDate'),
                    birthPlace: formData.get('birthPlace'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('swornAffidavitMother', data);
            }
        }

        // Senior ID Loss Modal Functions
        function openSeniorIDLossModal() {
            document.getElementById('seniorIDLossModal').style.display = 'block';
        }

        function closeSeniorIDLossModal() {
            document.getElementById('seniorIDLossModal').style.display = 'none';
        }

        function saveSeniorIDLoss() {
            const form = document.getElementById('seniorIDLossForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewSeniorFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewSeniorCompleteAddress').textContent = formData.get('completeAddress') || '-';
            document.getElementById('previewSeniorRelationship').textContent = formData.get('relationship') || '-';
            document.getElementById('previewSeniorCitizenName').textContent = formData.get('seniorCitizenName') || '-';
            document.getElementById('previewSeniorDetailsOfLoss').textContent = formData.get('detailsOfLoss') || '-';
            document.getElementById('previewSeniorDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('seniorIDLossDataPreview').style.display = 'block';
            
            alert('Data saved successfully!');
        }

        function viewSeniorIDLossData() {
            const form = document.getElementById('seniorIDLossForm');
            const formData = new FormData(form);
            
            // Update preview values
            document.getElementById('previewSeniorFullName').textContent = formData.get('fullName') || '-';
            document.getElementById('previewSeniorCompleteAddress').textContent = formData.get('completeAddress') || '-';
            document.getElementById('previewSeniorRelationship').textContent = formData.get('relationship') || '-';
            document.getElementById('previewSeniorCitizenName').textContent = formData.get('seniorCitizenName') || '-';
            document.getElementById('previewSeniorDetailsOfLoss').textContent = formData.get('detailsOfLoss') || '-';
            document.getElementById('previewSeniorDateOfNotary').textContent = formData.get('dateOfNotary') || '-';
            
            // Show preview, hide form
            form.style.display = 'none';
            document.getElementById('seniorIDLossDataPreview').style.display = 'block';
        }

        function hideSeniorIDLossData() {
            document.getElementById('seniorIDLossForm').style.display = 'block';
            document.getElementById('seniorIDLossDataPreview').style.display = 'none';
        }

        function sendSeniorIDLoss() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('seniorIDLossForm');
                const formData = new FormData(form);
                
                const data = {
                    fullName: formData.get('fullName'),
                    completeAddress: formData.get('completeAddress'),
                    relationship: formData.get('relationship'),
                    seniorCitizenName: formData.get('seniorCitizenName'),
                    detailsOfLoss: formData.get('detailsOfLoss'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('seniorIDLoss', data);
            }
        }


        // Send Document Functions
        function sendAffidavitLoss() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('affidavitLossForm');
                const formData = new FormData(form);
                
                const data = {
                    fullName: formData.get('fullName'),
                    completeAddress: formData.get('completeAddress'),
                    specifyItemLost: formData.get('specifyItemLost'),
                    itemLost: formData.get('itemLost'),
                    itemDetails: formData.get('itemDetails'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('affidavitLoss', data);
            }
        }

        // sendSoloParent removed - direct generation now

        function sendPWDLoss() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('pwdLossForm');
                const formData = new FormData(form);
                
                const data = {
                    fullName: formData.get('fullName'),
                    fullAddress: formData.get('fullAddress'),
                    detailsOfLoss: formData.get('detailsOfLoss'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('pwdLoss', data);
            }
        }

        function sendBoticabLoss() {
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                const form = document.getElementById('boticabLossForm');
                const formData = new FormData(form);
                
                const data = {
                    fullName: formData.get('fullName'),
                    fullAddress: formData.get('fullAddress'),
                    detailsOfLoss: formData.get('detailsOfLoss'),
                    dateOfNotary: formData.get('dateOfNotary')
                };

                sendDocumentToEmployee('boticabLoss', data);
            }
        }

        // Generic function to send document to employee
        function sendDocumentToEmployee(formType, formData) {
            // Get the send button and prevent double-clicking
            const sendBtn = event.target;
            if (sendBtn.disabled) {
                return; // Already processing
            }
            
            const originalText = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            sendBtn.disabled = true;

            // Send data to server
            fetch('send_document_handler_simple.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'form_type=' + encodeURIComponent(formType) + '&form_data=' + encodeURIComponent(JSON.stringify(formData))
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                sendBtn.innerHTML = originalText;
                sendBtn.disabled = false;
                
                if (result.status === 'success') {
                    alert('Document sent successfully to the employee!');
                    // Close the modal
                    const modal = document.querySelector('.modal[style*="block"]');
                    if (modal) {
                        modal.style.display = 'none';
                    }
                } else {
                    alert('Error: ' + result.message);
                    console.error('Error details:', result.debug_info);
                }
            })
            .catch(error => {
                sendBtn.innerHTML = originalText;
                sendBtn.disabled = false;
                console.error('Error:', error);
                alert('Error sending document: ' + error.message);
            });
        }
    </script>


</body>
</html> 