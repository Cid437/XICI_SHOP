<?php
session_start();
include_once __DIR__ . '/../includes/config.php';

// Helper to redirect back to the users list
function redirect_back() {
    header('Location: users.php');
    exit;
}

// 1. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_back();
}

// 2. Validate incoming data
if (!isset($_POST['userId'], $_POST['role'])) {
    $_SESSION['message'] = 'Missing required data.';
    redirect_back();
}

$userId = intval($_POST['userId']);
$role = trim($_POST['role']);
$allowedRoles = ['user', 'admin'];

if ($userId <= 0) {
    $_SESSION['message'] = 'Invalid User ID.';
    redirect_back();
}

if (!in_array($role, $allowedRoles)) {
    $_SESSION['message'] = 'Invalid role selected.';
    redirect_back();
}

// 3. Update the database using a prepared statement
$sql = "UPDATE users SET role = ? WHERE userId = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'si', $role, $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "User's role has been updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating role: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = "Database error: Could not prepare statement.";
}

redirect_back();
?>