<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../includes/config.php");


if (!isset($_SESSION['user_id'])) {
    
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  
    header("Location: profile.php");
    exit;
}


$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_new_password = $_POST['confirm_new_password'] ?? '';
$userId = $_SESSION['user_id'];


if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
    $_SESSION['message'] = 'All password fields are required.';
    header("Location: profile.php");
    exit;
}

if ($new_password !== $confirm_new_password) {
    $_SESSION['message'] = 'New passwords do not match.';
    header("Location: profile.php");
    exit;
}


$sql = "SELECT password FROM users WHERE userId = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $db_password_hash);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    
    $hashed_current_password = sha1($current_password);

    if ($hashed_current_password !== $db_password_hash) {
        $_SESSION['message'] = 'Incorrect current password.';
        header("Location: profile.php");
        exit;
    }
} else {
    $_SESSION['message'] = 'Database error. Please try again.';
    header("Location: profile.php");
    exit;
}


$hashed_new_password = sha1($new_password); 

$update_sql = "UPDATE users SET password = ? WHERE userId = ?";
if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
    mysqli_stmt_bind_param($update_stmt, 'si', $hashed_new_password, $userId);
    
    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['success'] = 'Password updated successfully!';
    } else {
        $_SESSION['message'] = 'Failed to update password. Please try again.';
    }
    mysqli_stmt_close($update_stmt);
}

header("Location: profile.php");
exit;
?>