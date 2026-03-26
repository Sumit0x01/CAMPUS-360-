<?php
session_start();
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize input
    $user_id = trim($_POST['user_id'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Basic validation
    if (empty($user_id) || empty($password)) {
        header("Location: index.html?error=" . urlencode("User ID and password are required."));
        exit();
    }
    
    try {
        // Query to find user by user_id
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify user and password
        if ($user && password_verify($password, $user['password'])) {
            // Login successful, set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role']; // e.g. 'Student' or 'Admin'
            $_SESSION['first_login'] = $user['first_login'] ?? 0;
            
            // Redirect based on role
            if ($user['role'] === 'Admin') {
                if ($_SESSION['first_login'] == 1) {
                    header("Location: change_password.php?info=" . urlencode("Security Policy: Please change your default password to proceed."));
                } else {
                    header("Location: admin_dashboard.php");
                }
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            // Invalid credentials
            header("Location: index.html?error=" . urlencode("Invalid User ID or Password."));
            exit();
        }
        
    } catch(PDOException $e) {
        header("Location: index.html?error=" . urlencode("A database error occurred. Please try again later."));
        exit();
    }
    
} else {
    // If someone tries to access this file directly
    header("Location: index.html");
    exit();
}
?>
