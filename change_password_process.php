<?php
session_start();
require_once 'db_connect.php';

// Strict check: only Admins who have first_login = 1 can access
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'Admin' || !isset($_SESSION['first_login']) || $_SESSION['first_login'] != 1) {
    header("Location: index.html?error=" . urlencode("Unauthorized access! Please login."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    if (empty($new_password) || empty($confirm_password)) {
        header("Location: change_password.php?error=" . urlencode("All fields are required."));
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        header("Location: change_password.php?error=" . urlencode("Passwords do not match."));
        exit();
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    try {
        // Update user password and switch first_login to 0
        $stmt = $pdo->prepare("UPDATE users SET password = :password, first_login = 0 WHERE user_id = :user_id");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($stmt->execute()) {
            // Success - update the session token immediately to allow dashboard access
            $_SESSION['first_login'] = 0; 
            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: change_password.php?error=" . urlencode("Failed to update password. Please try again."));
            exit();
        }
    } catch(PDOException $e) {
        header("Location: change_password.php?error=" . urlencode("A database error occurred."));
        exit();
    }
} else {
    // If someone tries to access this script via GET
    header("Location: change_password.php");
    exit();
}
?>
