<?php
require_once "../includes/session_check.php";
require_once "../config/database.php";

// Verify user is an employer
$user_check_sql = "SELECT user_type FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_check_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user['user_type'] !== 'employer') {
    header("Location: ../auth/login.php");
    exit();
}

// Get job ID from URL
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

// Verify the job belongs to this employer
$job_sql = "SELECT j.*, COUNT(ja.id) as total_applications 
            FROM jobs j 
            LEFT JOIN job_applications ja ON j.id = ja.job_id
            WHERE j.id = ? AND j.employer_id = ?
            GROUP BY j.id";
$job_stmt = mysqli_prepare($conn, $job_sql);
mysqli_stmt_bind_param($job_stmt, "ii", $job_id, $_SESSION['id']);
mysqli_stmt_execute($job_stmt);
$job = mysqli_stmt_get_result($job_stmt)->fetch_assoc();

if (!$job) {
    header("Location: jobs.php");
    exit();
}

// Fetch applications for this job
$applications_sql = "SELECT ja.*, 
                    u.email,
                    CONCAT(jp.first_name, ' ', jp.last_name) as full_name,
                    jp.phone,
                    jp.profile_picture,
                    jp.experience,
                    jp.resume_path
                    FROM job_applications ja
                    INNER JOIN users u ON ja.jobseeker_id = u.id
                    INNER JOIN jobseeker_profiles jp ON ja.jobseeker_id = jp.user_id
                    WHERE ja.job_id = ?
                    ORDER BY ja.applied_at DESC";
$applications_stmt = mysqli_prepare($conn, $applications_sql);
mysqli_stmt_bind_param($applications_stmt, "i", $job_id);
mysqli_stmt_execute($applications_stmt);
$applications = mysqli_stmt_get_result($applications_stmt);

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['status'])) {
    $valid_statuses = ['pending', 'reviewing', 'accepted', 'rejected'];
    $new_status = $_POST['status'];
    $application_id = (int)$_POST['application_id'];
    
    if (in_array($new_status, $valid_statuses)) {
        $update_sql = "UPDATE job_applications SET status = ? WHERE id = ? AND job_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sii", $new_status, $application_id, $job_id);
        mysqli_stmt_execute($update_stmt);
        
        // Redirect to prevent form resubmission
        header("Location: view-applications.php?job_id=" . $job_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications - PartTimePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .application-card {
            transition: transform 0.2s;
        }
        .application-card:hover {
            transform: translateY(-2px);
        }
        .status-badge {
            font-size: 0.875rem;
        }
        .status-pending { background-color: #ffc107; }
        .status-reviewing { background-color: #17a2b8; }
        .status-accepted { background-color: #28a745; }
        .status-rejected { background-color: #dc3545; }
        .profile-picture {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 50%;
        }
        .default-profile {
            width: 64px;
            height: 64px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <i class="fas fa-briefcase me-2"></i>
                PartTimePro
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
                        <a class="nav-link active" href="jobs.php">
                            <i class="fas fa-list me-1"></i> My Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">
                            <i class="fas fa-envelope me-1"></i> Messages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
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
                        <li class="breadcrumb-item"><a href="jobs.php">My Jobs</a></li>
                        <li class="breadcrumb-item active">Applications</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Applications for <?php echo htmlspecialchars($job['title']); ?></h2>
                    <span class="badge bg-secondary">
                        <?php echo $job['total_applications']; ?> Application<?php echo $job['total_applications'] !== 1 ? 's' : ''; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Job Details Summary -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Job Details</h5>
                                <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p class="mb-1"><strong>Salary:</strong> $<?php echo htmlspecialchars($job['salary']); ?> per hour</p>
                                <p class="mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Status</h5>
                                <p class="mb-1"><strong>Posted:</strong> <?php echo date('M d, Y', strtotime($job['created_at'])); ?></p>
                                <p class="mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge <?php echo $job['status'] === 'open' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ucfirst($job['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="row">
            <?php if (mysqli_num_rows($applications) > 0): ?>
                <?php while ($application = mysqli_fetch_assoc($applications)): ?>
                    <div class="col-12 mb-4">
                        <div class="card application-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Applicant Photo -->
                                    <div class="col-auto">
                                        <?php if (!empty($application['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($application['profile_picture']); ?>" 
                                                 alt="Profile" class="profile-picture">
                                        <?php else: ?>
                                            <div class="default-profile">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Applicant Details -->
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1"><?php echo htmlspecialchars($application['full_name']); ?></h5>
                                                <p class="mb-1">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <?php echo htmlspecialchars($application['email']); ?>
                                                    <?php if (!empty($application['phone'])): ?>
                                                        <span class="mx-2">•</span>
                                                        <i class="fas fa-phone me-1"></i>
                                                        <?php echo htmlspecialchars($application['phone']); ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <span class="badge status-<?php echo $application['status']; ?>">
                                                <?php echo ucfirst($application['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Experience -->
                                        <?php if (!empty($application['experience'])): ?>
                                            <div class="mt-2">
                                                <h6 class="mb-1">Experience</h6>
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['experience'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Application Message -->
                                        <?php if (!empty($application['message'])): ?>
                                            <div class="mt-2">
                                                <h6 class="mb-1">Cover Message</h6>
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['message'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <div class="mt-3 d-flex align-items-center">
                                            <?php if (!empty($application['resume_path'])): ?>
                                                <a href="<?php echo htmlspecialchars($application['resume_path']); ?>" 
                                                   class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                                    <i class="fas fa-file-pdf me-1"></i> View Resume
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="messages.php?user=<?php echo $application['jobseeker_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-envelope me-1"></i> Message
                                            </a>

                                            <!-- Status Update Form -->
                                            <form method="post" class="d-inline-block ms-auto">
                                                <input type="hidden" name="application_id" 
                                                       value="<?php echo $application['id']; ?>">
                                                <div class="input-group input-group-sm">
                                                    <select name="status" class="form-select" 
                                                            onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>
                                                            Pending
                                                        </option>
                                                        <option value="reviewing" <?php echo $application['status'] === 'reviewing' ? 'selected' : ''; ?>>
                                                            Reviewing
                                                        </option>
                                                        <option value="accepted" <?php echo $application['status'] === 'accepted' ? 'selected' : ''; ?>>
                                                            Accepted
                                                        </option>
                                                        <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>
                                                            Rejected
                                                        </option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Applied <?php echo date('M d, Y g:i A', strtotime($application['applied_at'])); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                            <h5>No Applications Yet</h5>
                            <p class="text-muted mb-0">There are no applications for this job posting yet.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 