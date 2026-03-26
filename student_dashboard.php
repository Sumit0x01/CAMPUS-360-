<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Student') {
    header("Location: index.html?error=" . urlencode("Access Denied! Please login as a Student."));
    exit();
}

// Get user details from session
$user_name = htmlspecialchars($_SESSION['name']);
$user_id = htmlspecialchars($_SESSION['user_id']);
$user_role = htmlspecialchars($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart College</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            padding-top: 5rem;
        }
        .welcome-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
        }
        .role-badge {
            background-color: #e3f2fd;
            color: #1565c0;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Smart College</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link text-white me-3">Hello, <?php echo $user_name; ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm mt-1" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="welcome-card">
                    <div class="role-badge">
                        <?php echo $user_role; ?> Account
                    </div>
                    <h2 class="mb-4">Welcome to your Dashboard, <?php echo $user_name; ?>!</h2>
                    
                    <p class="text-muted mb-4">You have successfully logged into the Smart College Management System. Below are your account details:</p>
                    
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Full Name</strong>
                            <span><?php echo $user_name; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>User ID</strong>
                            <span><?php echo $user_id; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Role</strong>
                            <span><?php echo $user_role; ?></span>
                        </li>
                    </ul>

                    <?php if ($user_role === 'Admin'): ?>
                        <div class="alert alert-info border-0 rounded-3">
                            <h5 class="alert-heading">Admin Privileges</h5>
                            <p class="mb-0">As an administrator, you have full access to manage students, courses, and system settings. Additional menu options will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success border-0 rounded-3">
                            <h5 class="alert-heading">Student Portal Active</h5>
                            <p class="mb-0">You can view your enrolled courses, check your grades, and update your profile from this portal.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
