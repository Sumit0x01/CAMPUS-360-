<?php
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize input
    $email = trim($_POST['email'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Basic validation
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        header("Location: admin_forgot_password.html?error=" . urlencode("All fields are required."));
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: admin_forgot_password.html?error=" . urlencode("Invalid email format."));
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        header("Location: admin_forgot_password.html?error=" . urlencode("Passwords do not match."));
        exit();
    }
    
    // Check if the Email exists AND belongs to an Admin
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND role = 'Admin' LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            // Email does not exist or user is not an admin
            header("Location: admin_forgot_password.html?error=" . urlencode("No Admin account found with that email address."));
            exit();
        }
        
    } catch(PDOException $e) {
        header("Location: admin_forgot_password.html?error=" . urlencode("A database error occurred."));
        exit();
    }
    
    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update the admin's password in the database
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email AND role = 'Admin'");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            // Reset successful, redirect to admin login page
            header("Location: admin_login.html?success=" . urlencode("Admin Password reset successfully! You can now login."));
            exit();
        } else {
            header("Location: admin_forgot_password.html?error=" . urlencode("Failed to reset password. Please try again."));
            exit();
        }
        
    } catch(PDOException $e) {
        header("Location: admin_forgot_password.html?error=" . urlencode("A database error occurred during password reset."));
        exit();
    }
    
} else {
    // If someone tries to access this file directly
    header("Location: admin_forgot_password.html");
    exit();
}
?>
