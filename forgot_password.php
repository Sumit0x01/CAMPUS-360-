<?php
session_start();
require_once 'db_connect.php';

$step = 1;
$user_id = '';
$question = '';
$error = $_GET['error'] ?? '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_step = $_POST['step'] ?? '1';

    // STEP 1: Process User ID
    if ($posted_step == '1') {
        $user_id = trim($_POST['user_id'] ?? '');
        if (empty($user_id)) {
            $error = "Student ID is required.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT security_question, role FROM users WHERE user_id = :user_id LIMIT 1");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && $user['role'] === 'Student') {
                    $question = $user['security_question'];
                    if (empty($question)) {
                        $error = "No security question is set for this account. Please contact the administrator.";
                        $step = 1;
                    } else {
                        $step = 2; // Move to Step 2
                    }
                } else {
                    $error = "Student ID not found or invalid.";
                }
            } catch(PDOException $e) {
                $error = "A database error occurred.";
            }
        }
    } 
    // STEP 2: Process Security Answer
    elseif ($posted_step == '2') {
        $user_id = trim($_POST['user_id'] ?? '');
        $security_answer = trim($_POST['security_answer'] ?? '');
        
        try {
            $stmt = $pdo->prepare("SELECT security_question, security_answer FROM users WHERE user_id = :user_id AND role = 'Student' LIMIT 1");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify(strtolower($security_answer), $user['security_answer'])) {
                $step = 3; // Answer is correct, move to Step 3
                $_SESSION['reset_authorized_user'] = $user_id;
            } else {
                $error = "Incorrect security answer.";
                $step = 2; // Stay on Step 2
                $question = $user['security_question'] ?? '';
            }
        } catch(PDOException $e) {
            $error = "A database error occurred.";
            $step = 2;
        }
    } 
    // STEP 3: Process New Password
    elseif ($posted_step == '3') {
        if (!isset($_SESSION['reset_authorized_user'])) {
            $error = "Unauthorized password reset request.";
            $step = 1;
        } else {
            $user_id = $_SESSION['reset_authorized_user'];
            $new_password = trim($_POST['new_password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            
            if (empty($new_password) || empty($confirm_password)) {
                $error = "Both password fields are required.";
                $step = 3;
            } elseif ($new_password !== $confirm_password) {
                $error = "Passwords do not match.";
                $step = 3;
            } else {
                try {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':user_id', $user_id);
                    if ($stmt->execute()) {
                        unset($_SESSION['reset_authorized_user']);
                        header("Location: index.html?success=" . urlencode("Password reset successfully! You can now login."));
                        exit();
                    } else {
                        $error = "Failed to reset password. Please try again.";
                        $step = 3;
                    }
                } catch(PDOException $e) {
                    $error = "A database error occurred during password reset.";
                    $step = 3;
                }
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
    <title>Reset Password - Smart College</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-card { border-top: 5px solid #4a90e2; }
    </style>
</head>
<body class="bg-light">

    <div class="auth-wrapper">
        <div class="auth-card">
            
            <div class="auth-header">
                <h2>Reset Password</h2>
                <?php if ($step == 1): ?>
                    <p>Step 1: Enter your Student ID</p>
                <?php elseif ($step == 2): ?>
                    <p>Step 2: Answer your security question</p>
                <?php elseif ($step == 3): ?>
                    <p>Step 3: Create a new password</p>
                <?php endif; ?>
            </div>

            <!-- Alert Container for displaying errors -->
            <div id="alert-container">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
            </div>

            <!-- STEP 1 FORM -->
            <?php if ($step == 1): ?>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="step" value="1">

                <div class="mb-3">
                    <label for="user_id" class="form-label text-muted fw-semibold">Student ID</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" placeholder="Enter your Student ID" value="<?php echo htmlspecialchars($user_id); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Verify ID</button>

                <div class="text-center mt-2">
                    <span class="small text-muted">Remember your password? <a href="index.html" class="text-link">Login here</a></span>
                </div>
            </form>

            <!-- STEP 2 FORM -->
            <?php elseif ($step == 2): ?>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                <div class="mb-3">
                    <label class="form-label text-muted fw-semibold">Security Question</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($question); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="security_answer" class="form-label text-muted fw-semibold">Your Answer</label>
                    <input type="text" class="form-control" id="security_answer" name="security_answer" placeholder="Enter your answer" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Check Answer</button>

                <div class="text-center mt-2">
                    <span class="small text-muted">Back to <a href="forgot_password.php" class="text-link">Step 1</a></span>
                </div>
            </form>

            <!-- STEP 3 FORM -->
            <?php elseif ($step == 3): ?>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="step" value="3">

                <div class="mb-3">
                    <label for="new_password" class="form-label text-muted fw-semibold">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label text-muted fw-semibold">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Update Password</button>

            </form>
            <?php endif; ?>

        </div>
    </div>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clean URL from GET errors
            const urlParams = new URLSearchParams(window.location.search);
            if(urlParams.get('error')) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>
</html>
