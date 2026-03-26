<?php
require_once 'db_connect.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve and sanitize input
    $name = trim($_POST['name'] ?? '');
    $user_id = trim($_POST['user_id'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $security_question = trim($_POST['security_question'] ?? '');
    $security_answer = trim($_POST['security_answer'] ?? '');
    $role = 'Student'; // Default role for registration is always Student
    
    // Basic validation
    if (empty($name) || empty($user_id) || empty($password) || empty($confirm_password) || empty($security_question) || empty($security_answer)) {
        header("Location: register.html?error=" . urlencode("All fields are required."));
        exit();
    }
    
    if ($password !== $confirm_password) {
        header("Location: register.html?error=" . urlencode("Passwords do not match."));
        exit();
    }
    
    // Check if the User ID already exists in the database
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE user_id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // User ID exists
            header("Location: register.html?error=" . urlencode("Student ID already exists. Please choose another one."));
            exit();
        }
        
    } catch(PDOException $e) {
        header("Location: register.html?error=" . urlencode("A database error occurred during validation."));
        exit();
    }
    
    // NEW REQUIREMENT: Validate Student ID against `valid_students` table
    try {
        $stmt = $pdo->prepare("SELECT reg_no FROM valid_students WHERE reg_no = :reg_no LIMIT 1");
        $stmt->bindParam(':reg_no', $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            // Registration number not found in approved list
            header("Location: register.html?error=" . urlencode("Invalid Registration Number. You are not pre-approved by the admin."));
            exit();
        }
        
    } catch(PDOException $e) {
        header("Location: register.html?error=" . urlencode("A database error occurred during Student ID verification."));
        exit();
    }
    
    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // Hash the security answer (case-insensitive by lowercasing)
    $hashed_answer = password_hash(strtolower($security_answer), PASSWORD_DEFAULT);
    
    // Insert new user into database and remove from valid_students
    try {
        // Start a transaction to ensure both operations succeed or fail together
        $pdo->beginTransaction();
        
        // 1. Insert the student into users table with first_login explicitly 0
        $stmt = $pdo->prepare("INSERT INTO users (name, user_id, password, role, first_login, security_question, security_answer) VALUES (:name, :user_id, :password, :role, 0, :security_question, :security_answer)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':security_question', $security_question);
        $stmt->bindParam(':security_answer', $hashed_answer);
        
        $stmt->execute();
        
        // 2. Remove the registration number from valid_students
        $delStmt = $pdo->prepare("DELETE FROM valid_students WHERE reg_no = :reg_no");
        $delStmt->bindParam(':reg_no', $user_id);
        
        $delStmt->execute();
        
        // If everything was successful, commit the transaction
        $pdo->commit();
        
        // Registration successful, redirect to login page with a success message
        header("Location: index.html?success=" . urlencode("Registration successful! You can now login."));
        exit();
        
    } catch(PDOException $e) {
        // Roll back the transaction if something failed
        $pdo->rollBack();
        header("Location: register.html?error=" . urlencode("A database error occurred during registration."));
        exit();
    }
    
} else {
    // If someone tries to access this file directly
    header("Location: register.html");
    exit();
}
?>
