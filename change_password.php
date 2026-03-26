<?php
session_start();
// Check if user is logged in and is an Admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Admin') {
    header("Location: index.html?error=" . urlencode("Unauthorized access!"));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Default Password - Smart College</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <div class="auth-wrapper">
        <div class="auth-card" style="border-top: 5px solid #ff9800;">
            
            <div class="auth-header">
                <?php if (isset($_SESSION['first_login']) && $_SESSION['first_login'] == 1): ?>
                    <h2>Security Notice</h2>
                    <p>Please change your default admin password to continue.</p>
                <?php else: ?>
                    <h2>Change Password</h2>
                    <p>Update your admin account password</p>
                <?php endif; ?>
            </div>

            <!-- Alert Container for displaying errors and success messages via JS -->
            <div id="alert-container"></div>

            <form action="change_password_process.php" method="POST">
                
                <div class="mb-3">
                    <label for="new_password" class="form-label text-muted fw-semibold">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Create a strong password" required>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label text-muted fw-semibold">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                </div>

                <button type="submit" class="btn btn-warning w-100 mb-3 fw-bold text-dark">Update Password & Continue</button>

            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const info = urlParams.get('info');
            
            const alertContainer = document.getElementById('alert-container');
            
            if (error) {
                alertContainer.innerHTML = `<div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    ${decodeURIComponent(error)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            }
            if (info) {
                alertContainer.innerHTML = `<div class="alert alert-info alert-custom alert-dismissible fade show" role="alert">
                    ${decodeURIComponent(info)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            }
            // Clear URL gracefully
            if(error || info) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>
