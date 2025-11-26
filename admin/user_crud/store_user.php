<?php
session_start();
include("../../includes/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPass = trim($_POST['confirmPass']);
    $role = $_POST['role'];

    if ($password !== $confirmPass) {
        $_SESSION['message'] = 'Passwords do not match.';
        header("Location: create_user.php");
        exit();
    }

    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['message'] = 'All fields are required.';
        header("Location: create_user.php");
        exit();
    }

    $hashed_password = sha1($password);

    $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sss', $email, $hashed_password, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'User created successfully!';
        } else {
            $_SESSION['message'] = 'Error creating user: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Database error: Could not prepare statement.';
    }

    header("Location: ../users.php");
    exit();

} else {
    header("Location: ../users.php");
    exit();
}