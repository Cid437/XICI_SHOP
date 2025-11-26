<?php
session_start();
include("../../includes/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../user/login.php");
    exit();
}

$user_id_to_delete = $_GET['id'] ?? null;

if (!$user_id_to_delete) {
    header("Location: ../users.php");
    exit();
}

if ($user_id_to_delete == $_SESSION['user_id']) {
    $_SESSION['message'] = "Error: You cannot delete your own account.";
    header("Location: ../users.php");
    exit();
}

$sql = "DELETE FROM users WHERE userId = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id_to_delete);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'User deleted successfully!';
    } else {
        $_SESSION['message'] = 'Error deleting user: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = 'Database error: Could not prepare statement.';
}

header("Location: ../users.php");
exit();