<?php
require_once "../includes/session_check.php";
require_once "../config/database.php";

// Verify user is a jobseeker
$user_check_sql = "SELECT user_type FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_check_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user['user_type'] !== 'jobseeker') {
    header("Location: ../auth/login.php");
    exit();
}

// Get selected employer ID from URL
$selected_employer = isset($_GET['user']) ? (int)$_GET['user'] : 0;

// Fetch all conversations for the jobseeker
$conversations_sql = "SELECT DISTINCT 
    j.employer_id,
    ep.company_name,
    (SELECT message FROM messages 
     WHERE (sender_id = ? AND receiver_id = j.employer_id) 
     OR (sender_id = j.employer_id AND receiver_id = ?) 
     ORDER BY sent_at DESC LIMIT 1) as last_message,
    (SELECT sent_at FROM messages 
     WHERE (sender_id = ? AND receiver_id = j.employer_id) 
     OR (sender_id = j.employer_id AND receiver_id = ?) 
     ORDER BY sent_at DESC LIMIT 1) as last_message_time,
    (SELECT COUNT(*) FROM messages 
     WHERE sender_id = j.employer_id 
     AND receiver_id = ? 
     AND is_read = 0) as unread_count
    FROM job_applications ja
    INNER JOIN jobs j ON ja.job_id = j.id
    INNER JOIN employer_profiles ep ON j.employer_id = ep.user_id
    WHERE ja.jobseeker_id = ?";

$stmt = mysqli_prepare($conn, $conversations_sql);
mysqli_stmt_bind_param($stmt, "iiiiii", 
    $_SESSION['id'], // for first sender_id
    $_SESSION['id'], // for first receiver_id
    $_SESSION['id'], // for second sender_id
    $_SESSION['id'], // for second receiver_id
    $_SESSION['id'], // for unread count receiver_id
    $_SESSION['id']  // for jobseeker_id in WHERE clause
);
mysqli_stmt_execute($stmt);
$conversations = mysqli_stmt_get_result($stmt);

// If an employer is selected, fetch messages for that conversation
if ($selected_employer > 0) {
    // Mark messages as read
    $update_sql = "UPDATE messages SET is_read = 1 
                   WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $selected_employer, $_SESSION['id']);
    mysqli_stmt_execute($update_stmt);
    
    // Fetch employer details
    $employer_sql = "SELECT u.id, u.email, ep.company_name, ep.phone
                    FROM users u
                    INNER JOIN employer_profiles ep ON u.id = ep.user_id
                    WHERE u.id = ? AND u.user_type = 'employer'";
    $employer_stmt = mysqli_prepare($conn, $employer_sql);
    mysqli_stmt_bind_param($employer_stmt, "i", $selected_employer);
    mysqli_stmt_execute($employer_stmt);
    $selected_employer_details = mysqli_stmt_get_result($employer_stmt)->fetch_assoc();
    
    // Fetch messages
    $messages_sql = "SELECT m.*, 
                    ep.company_name as sender_name,
                    jp.profile_picture as jobseeker_picture
                    FROM messages m
                    LEFT JOIN employer_profiles ep ON m.sender_id = ep.user_id
                    LEFT JOIN jobseeker_profiles jp ON m.sender_id = jp.user_id
                    WHERE (sender_id = ? AND receiver_id = ?) 
                    OR (sender_id = ? AND receiver_id = ?)
                    ORDER BY sent_at ASC";
    $messages_stmt = mysqli_prepare($conn, $messages_sql);
    mysqli_stmt_bind_param($messages_stmt, "iiii", 
        $selected_employer, $_SESSION['id'],
        $_SESSION['id'], $selected_employer
    );
    mysqli_stmt_execute($messages_stmt);
    $messages = mysqli_stmt_get_result($messages_stmt);
}

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && $selected_employer > 0) {
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $insert_sql = "INSERT INTO messages (sender_id, receiver_id, message, sent_at, is_read) 
                      VALUES (?, ?, ?, CURRENT_TIMESTAMP, 0)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "iis", $_SESSION['id'], $selected_employer, $message);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            // Redirect to prevent form resubmission
            header("Location: messages.php?user=" . $selected_employer);
            exit;
        }
    }
}

// Get jobseeker profile for the profile picture
$profile_sql = "SELECT profile_picture FROM jobseeker_profiles WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $profile_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$jobseeker_profile = mysqli_stmt_get_result($stmt)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - WorkAround</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        /* Modern Typography */
        body {
            font-family: 'Poppins', sans-serif;
        }

        h1, h2, h3, .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Headings Enhancement */
        .display-4 {
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        /* Card Titles */
        .card-title {
            font-weight: 600;
            color: #2d3436;
        }

        /* Navigation Enhancement */
        .navbar-nav .nav-link {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Button Text */
        .btn {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Section Headings */
        section h2 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        section h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: #0d6efd;
            border-radius: 2px;
        }

        /* Job Titles */
        .job-title {
            font-weight: 600;
            color: #2d3436;
            font-size: 1.25rem;
        }

        /* Company Names */
        .company-name {
            font-weight: 500;
            color: #636e72;
        }

        /* Status Labels */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
            padding: 0.5em 1em;
        }

        /* Message Text */
        .message-bubble {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Form Labels */
        .form-label {
            font-weight: 500;
            color: #2d3436;
        }

        /* Dashboard Stats */
        .stats-number {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: #2d3436;
        }

        .stats-label {
            font-weight: 500;
            color: #636e72;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        /* Footer Text */
        footer {
            font-weight: 400;
            letter-spacing: 0.2px;
        }

        footer h5 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .messages-container {
            height: 400px;
            overflow-y: auto;
            padding: 1rem;
        }
        .message-bubble {
            max-width: 75%;
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            position: relative;
        }
        .message-sent {
            margin-left: auto;
            background-color: #007bff;
            color: white;
            border-top-right-radius: 0.25rem;
        }
        .message-received {
            margin-right: auto;
            background-color: #f8f9fa;
            border-top-left-radius: 0.25rem;
        }
        .conversation-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .conversation-item {
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.2s;
        }
        .conversation-item:hover,
        .conversation-item.active {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .conversation-item a {
            display: block;
        }
        .default-logo {
            width: 48px;
            height: 48px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.25rem;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-top: 0.25rem;
        }
        .unread-badge {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
        }
        .profile-picture img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .default-profile-picture {
            width: 50px;
            height: 50px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .default-profile-picture i {
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
                            <i class="fas fa-search me-1"></i> Find Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="applications.php">
                            <i class="fas fa-file-alt me-1"></i> My Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="messages.php">
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
                        <li class="breadcrumb-item active">Messages</li>
                    </ol>
                </nav>
                <h2>Messages</h2>
            </div>
        </div>

        <div class="row">
            <!-- Conversations List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Conversations</h5>
                    </div>
                    <div class="conversation-list">
                        <?php if (mysqli_num_rows($conversations) > 0): ?>
                            <?php while ($conversation = mysqli_fetch_assoc($conversations)): ?>
                                <div class="conversation-item <?php echo isset($_GET['user']) && $_GET['user'] == $conversation['employer_id'] ? 'active' : ''; ?>">
                                    <a href="?user=<?php echo $conversation['employer_id']; ?>" class="text-decoration-none text-dark">
                                        <div class="d-flex align-items-center p-3">
                                            <div class="profile-picture me-3">
                                                <div class="default-profile-picture">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($conversation['company_name']); ?></h6>
                                                <p class="mb-1 small text-muted text-truncate">
                                                    <?php echo !empty($conversation['last_message']) ? htmlspecialchars($conversation['last_message']) : 'No messages yet'; ?>
                                                </p>
                                                <?php if (!empty($conversation['last_message_time'])): ?>
                                                    <small class="text-muted">
                                                        <?php echo date('M d, Y g:i A', strtotime($conversation['last_message_time'])); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($conversation['unread_count'] > 0): ?>
                                                <span class="badge bg-primary rounded-pill"><?php echo $conversation['unread_count']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No conversations yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="col-md-8">
                <?php if ($selected_employer > 0 && isset($selected_employer_details)): ?>
                    <div class="card">
                        <!-- Chat Header -->
                        <div class="card-header bg-white">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="profile-picture">
                                        <?php if (!empty($selected_employer_details['profile_picture'])): ?>
                                            <img src="../<?php echo htmlspecialchars($selected_employer_details['profile_picture']); ?>" 
                                                 alt="Profile Picture">
                                        <?php else: ?>
                                            <div class="default-profile-picture">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($selected_employer_details['company_name']); ?></h5>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($selected_employer_details['email']); ?>
                                        <?php if (!empty($selected_employer_details['phone'])): ?>
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($selected_employer_details['phone']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Messages Container -->
                        <div class="card-body messages-container" id="messagesContainer">
                            <?php if (mysqli_num_rows($messages) > 0): ?>
                                <?php while ($message = mysqli_fetch_assoc($messages)): ?>
                                    <div class="message-bubble <?php echo $message['sender_id'] == $_SESSION['id'] ? 'message-sent' : 'message-received'; ?>">
                                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                        <div class="message-time <?php echo $message['sender_id'] == $_SESSION['id'] ? 'text-white' : 'text-muted'; ?>">
                                            <?php echo date('g:i A', strtotime($message['sent_at'])); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No messages yet</p>
                                    <p class="text-muted small">Start the conversation by sending a message below</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Message Input -->
                        <div class="card-footer bg-white">
                            <form method="post" class="message-form">
                                <div class="input-group">
                                    <textarea class="form-control" name="message" rows="1" 
                                              placeholder="Type your message..." required></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                            <h5>Select a Conversation</h5>
                            <p class="text-muted">Choose a conversation from the list to start messaging</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to bottom of messages container
        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }

        // Scroll to bottom on page load
        window.onload = scrollToBottom;
    </script>
</body>
</html>