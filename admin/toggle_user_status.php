<?php
// Toggle a user's is_active status safely using prepared statements
session_start();
include_once __DIR__ . '/../includes/config.php';

// Helper to redirect back to the users list
function redirect_back() {
    header('Location: users.php');
    exit;
}

// CORRECTED: Check for 'userId' from GET
if (!isset($_GET['userId'])) {
    $_SESSION['message'] = 'Missing user ID'; // Changed to message for alert.php
    redirect_back();
}

$userId = intval($_GET['userId']);
if ($userId <= 0) {
    $_SESSION['message'] = 'Invalid user ID';
    redirect_back();
}

// Fetch current is_active value using a prepared statement
// CORRECTED: Use 'userId' in WHERE clause
$selectSql = "SELECT is_active FROM users WHERE userId = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $selectSql)) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $isActive);
    if (mysqli_stmt_fetch($stmt) === null) {
        mysqli_stmt_close($stmt);
        $_SESSION['message'] = 'User not found';
        redirect_back();
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['message'] = 'Database error on select';
    redirect_back();
}

// New status is the opposite of the current one
$newStatus = ($isActive == 1) ? 0 : 1;

// Update using a prepared statement
// CORRECTED: Use 'userId' in WHERE clause
$updateSql = "UPDATE users SET is_active = ? WHERE userId = ?";
if ($ustmt = mysqli_prepare($conn, $updateSql)) {
    mysqli_stmt_bind_param($ustmt, 'ii', $newStatus, $userId);
    $ok = mysqli_stmt_execute($ustmt);
    mysqli_stmt_close($ustmt);
    if ($ok) {
        $_SESSION['success'] = 'User status updated successfully!';
    } else {
        $_SESSION['message'] = 'Failed to update user status';
    }
} else {
    $_SESSION['message'] = 'Database error on update';
}

redirect_back();
?>