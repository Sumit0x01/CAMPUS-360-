<?php
session_start();

// Strict check: if user is not logged in OR role is not 'Admin'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html?error=" . urlencode("Unauthorized access! Please login as an Admin."));
    exit();
}

// Ensure Admins with first_login = 1 cannot access dashboard
if (isset($_SESSION['first_login']) && $_SESSION['first_login'] == 1) {
    header("Location: change_password.php?error=" . urlencode("You must change your default password before accessing the dashboard."));
    exit();
}

// Get admin details from session
$admin_name = htmlspecialchars($_SESSION['name']);
$employee_id = htmlspecialchars($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Smart College</title>
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
            border-top: 5px solid #dc3545; /* Red border to differentiate admin from student */
        }
        .role-badge {
            background-color: #f8d7da;
            color: #842029;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Smart College Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link text-white me-3 fw-medium">Welcome, <?php echo $admin_name; ?></span>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-warning btn-sm mt-1 fw-bold text-dark" href="change_password.php">Change Password</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm mt-1 fw-bold text-danger" href="logout.php">Logout</a>
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
                        Administrator Access Active
                    </div>
                    <h2 class="mb-4">Welcome Admin, <?php echo $admin_name; ?>!</h2>
                    
                    <p class="text-muted mb-4">You have securely accessed the Admin Portal. Here are your account details:</p>
                    
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Full Developer Name</strong>
                            <span><?php echo $admin_name; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Employee ID</strong>
                            <span><?php echo $employee_id; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Role</strong>
                            <span class="badge bg-danger rounded-pill">Admin</span>
                        </li>
                    </ul>

                    <div class="alert alert-danger bg-danger text-white border-0 rounded-3">
                        <h5 class="alert-heading fw-bold">Admin Privileges</h5>
                        <p class="mb-0 text-white-50">You have full control over the Smart College system. Use this portal to manage students, staff, billing, and system configurations. Features will be available here soon.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
