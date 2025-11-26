<?php
session_start();
include("../includes/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $recipient_name = trim($_POST['recipient_name'] ?? '');
        $address_line1  = trim($_POST['address_line1'] ?? '');
        $city           = trim($_POST['city'] ?? '');
        $province       = trim($_POST['province'] ?? '');
        $zip_code       = trim($_POST['zip_code'] ?? '');
        $phone_number   = trim($_POST['phone_number'] ?? '');

        if (empty($recipient_name) || empty($address_line1) || empty($city) || empty($province) || empty($zip_code) || empty($phone_number)) {
            $_SESSION['message'] = 'All address fields are required.';
        } else {
            $sql = "INSERT INTO addresses (user_id, recipient_name, address_line1, city, province, zip_code, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'issssss', $userId, $recipient_name, $address_line1, $city, $province, $zip_code, $phone_number);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'New address added successfully!';
            } else {
                $_SESSION['message'] = 'Error: Could not save the address.';
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($_POST['action'] === 'delete') {
        $address_id = $_POST['address_id'] ?? 0;

        if ($address_id > 0) {
            $sql = "DELETE FROM addresses WHERE address_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $address_id, $userId);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'Address deleted successfully!';
            } else {
                $_SESSION['message'] = 'Error: Could not delete the address.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

header("Location: profile.php#addresses");
exit;