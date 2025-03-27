<?php
require_once "../includes/session_check.php";
require_once "../config/database.php";

// Fetch employer profile
$sql = "SELECT u.*, ep.* 
        FROM users u 
        LEFT JOIN employer_profiles ep ON u.id = ep.user_id 
        WHERE u.id = ? AND u.user_type = 'employer'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$profile = mysqli_stmt_get_result($stmt)->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = trim($_POST['company_name']);
    $company_description = trim($_POST['company_description']);
    $industry = trim($_POST['industry']);
    $website = trim($_POST['website']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    
    $errors = [];
    
    // Validate required fields
    if (empty($company_name)) $errors[] = "Company name is required";
    if (empty($company_description)) $errors[] = "Company description is required";
    if (empty($industry)) $errors[] = "Industry is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) $errors[] = "Invalid website URL";
    
    // Handle logo upload
    $logo_path = isset($profile['logo_path']) ? $profile['logo_path'] : null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['logo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Invalid file type. Only JPG, PNG and GIF are allowed.";
        } else {
            $upload_dir = "../uploads/logos/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('logo_') . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                // Delete old logo if exists
                if (!empty($logo_path) && file_exists("../" . $logo_path)) {
                    unlink("../" . $logo_path);
                }
                $logo_path = "uploads/logos/" . $file_name;
            } else {
                $errors[] = "Failed to upload logo. Please try again.";
            }
        }
    }
    
    if (empty($errors)) {
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update user email
            $update_user_sql = "UPDATE users SET email = ? WHERE id = ?";
            $user_stmt = mysqli_prepare($conn, $update_user_sql);
            mysqli_stmt_bind_param($user_stmt, "si", $email, $_SESSION['id']);
            mysqli_stmt_execute($user_stmt);
            
            // Check if employer profile exists
            if ($profile['user_id']) {
                // Update existing profile
                $update_sql = "UPDATE employer_profiles SET 
                            company_name = ?, 
                            company_description = ?, 
                            industry = ?, 
                            website = ?, 
                            address = ?, 
                            phone = ?,
                            logo_path = ?
                            WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($stmt, "sssssssi", 
                    $company_name, 
                    $company_description, 
                    $industry, 
                    $website, 
                    $address, 
                    $phone,
                    $logo_path,
                    $_SESSION['id']
                );
            } else {
                // Insert new profile
                $insert_sql = "INSERT INTO employer_profiles (
                            user_id, 
                            company_name, 
                            company_description, 
                            industry, 
                            website, 
                            address, 
                            phone,
                            logo_path
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($stmt, "isssssss", 
                    $_SESSION['id'], 
                    $company_name, 
                    $company_description, 
                    $industry, 
                    $website, 
                    $address, 
                    $phone,
                    $logo_path
                );
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update profile");
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Redirect to prevent form resubmission
            header("Location: profile.php?success=1");
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - WorkAround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <i class="fas fa-briefcase me-2"></i>
                WorkAround
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">
                            <i class="fas fa-list me-1"></i> My Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="applications.php">
                            <i class="fas fa-users me-1"></i> Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">
                            <i class="fas fa-envelope me-1"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="fas fa-user me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
                <h2>Company Profile</h2>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <!-- Profile Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="company_description" class="form-label">Company Description *</label>
                                <textarea class="form-control" id="company_description" name="company_description" 
                                          rows="4" required><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Company Logo</label>
                                <?php if (!empty($profile['logo_path'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars('../' . $profile['logo_path']); ?>" 
                                             alt="Company Logo" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/gif">
                                <small class="text-muted">Upload a new logo (JPG, PNG, or GIF)</small>
                            </div>

                            <div class="mb-3">
                                <label for="industry" class="form-label">Industry *</label>
                                <input type="text" class="form-control" id="industry" name="industry" 
                                       value="<?php echo htmlspecialchars($profile['industry'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>" 
                                       placeholder="https://example.com">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address *</label>
                                <textarea class="form-control" id="address" name="address" 
                                          rows="2" required><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Profile Preview -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Profile Preview</h5>
                        <div class="text-center mb-3">
                            <?php if (!empty($profile['logo_path'])): ?>
                                <img src="<?php echo htmlspecialchars('../' . $profile['logo_path']); ?>" 
                                     alt="Company Logo" class="img-fluid rounded-circle mb-2" 
                                     style="max-width: 150px; height: auto;">
                            <?php else: ?>
                                <div class="default-logo mb-2">
                                    <i class="fas fa-building"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="mb-0"><?php echo htmlspecialchars($profile['company_name'] ?? 'Your Company'); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($profile['industry'] ?? 'Industry'); ?></p>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <strong>Description:</strong><br>
                            <?php echo nl2br(htmlspecialchars($profile['company_description'] ?? 'No description available')); ?>
                        </div>
                        <?php if (!empty($profile['website'])): ?>
                            <div class="mb-2">
                                <strong>Website:</strong><br>
                                <a href="<?php echo htmlspecialchars($profile['website']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($profile['website']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .default-logo {
            width: 150px;
            height: 150px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #6c757d;
            font-size: 3rem;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>