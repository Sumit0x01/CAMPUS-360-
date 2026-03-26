<?php
require_once 'db_connect.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Auto-set the default password
    $default_password = "admin123";
    
    if (empty($name) || empty($user_id)) {
         $error = "Name and Employee ID are required.";
    } else {
         try {
             // Hash the default password securely
             $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
             
             // Insert Admin with first_login = 1
             $stmt = $pdo->prepare("INSERT INTO users (name, user_id, email, password, role, first_login) VALUES (:name, :user_id, :email, :password, 'Admin', 1)");
             $stmt->bindParam(':name', $name);
             $stmt->bindParam(':user_id', $user_id);
             $stmt->bindParam(':email', $email);
             $stmt->bindParam(':password', $hashed_password);
             
             if ($stmt->execute()) {
                 $message = "✅ Admin account established successfully.<br><br>
                             Employee ID: <strong>" . htmlspecialchars($user_id) . "</strong><br>
                             Password: <strong>admin123</strong><br><br>
                             <span class='text-danger fw-bold'>CRITICAL WARNING: Delete the `create_admin.php` file immediately from your server for security reasons!</span>";
             } else {
                 $error = "Failed to create Admin. It may already exist.";
             }
         } catch(PDOException $e) {
             if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), '1062') !== false) {
                 $error = "An account with that Employee ID or Email already exists.";
             } else {
                 $error = "Database Error: " . $e->getMessage();
             }
         }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Admin Creator - Smart College</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-card { border-top: 5px solid #dc3545; }
    </style>
</head>
<body class="bg-light">
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 500px;">
            <div class="auth-header">
                <h2>Admin Setup Utility</h2>
                <p class="text-danger fw-bold">DANGER: Maintain strict access to this file.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-custom alert-dismissible fade show">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label text-muted fw-semibold">Admin Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="E.g., John Doe" required>
                </div>
                
                <div class="mb-3">
                    <label for="user_id" class="form-label text-muted fw-semibold">Employee ID</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" placeholder="E.g., ADM1001" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label text-muted fw-semibold">Email Address (Optional)</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="admin@smartcollege.edu">
                </div>

                <div class="alert alert-warning mb-4 small border-warning">
                    <strong>Security Notice:</strong> Password will be automatically set to <code>admin123</code>. Due to First Login Security, the admin will be <strong>forced</strong> to change this password prior to gaining dashboard access.
                </div>
                
                <button type="submit" class="btn btn-danger w-100 mb-3 fw-bold">Create Administrator Account</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="index.html" class="text-link small">Return to Login Portal</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
