<?php
session_start();
include("../includes/config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$status = $_POST['status'];
$orderId = $_SESSION['orderId'];

$sql = "UPDATE orderinfo SET status = ? WHERE orderinfo_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'si', $status, $orderId);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    $sql_customer = "SELECT fname, lname, phone FROM orderdetails WHERE orderinfo_id = ? LIMIT 1";
    $stmt_customer = mysqli_prepare($conn, $sql_customer);
    mysqli_stmt_bind_param($stmt_customer, 'i', $orderId);
    mysqli_stmt_execute($stmt_customer);
    $customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_customer));

    $sql_items = "SELECT description, quantity, sell_price FROM orderdetails WHERE orderinfo_id = ?";
    $stmt_items = mysqli_prepare($conn, $sql_items);
    mysqli_stmt_bind_param($stmt_items, 'i', $orderId);
    mysqli_stmt_execute($stmt_items);
    $items_result = mysqli_stmt_get_result($stmt_items);

    $email_body = "<h1>Order Update</h1>";
    $email_body .= "<p>Hello " . htmlspecialchars($customer['fname']) . ",</p>";
    $email_body .= "<p>The status of your order #{$orderId} has been updated to: <strong>" . htmlspecialchars($status) . "</strong></p>";
    $email_body .= "<h3>Order Summary:</h3>";
    $email_body .= "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
    $email_body .= "<thead><tr><th>Item</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr></thead>";
    $email_body .= "<tbody>";

    $grandTotal = 0;
    while ($item = mysqli_fetch_assoc($items_result)) {
        $subtotal = $item['quantity'] * $item['sell_price'];
        $grandTotal += $subtotal;
        $email_body .= "<tr>";
        $email_body .= "<td>" . htmlspecialchars($item['description']) . "</td>";
        $email_body .= "<td>" . $item['quantity'] . "</td>";
        $email_body .= "<td>₱" . number_format($item['sell_price'], 2) . "</td>";
        $email_body .= "<td>₱" . number_format($subtotal, 2) . "</td>";
        $email_body .= "</tr>";
    }

    $email_body .= "</tbody></table>";
    $email_body .= "<h3 style='text-align: right;'>Grand Total: ₱" . number_format($grandTotal, 2) . "</h3>";
    $email_body .= "<p>Thank you for shopping with us!</p>";

    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '74e09d09307fb9';
        $mail->Password   = '9abc544a1fc403';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;

        $mail->setFrom('sales@xicitestshop.com', 'XiciTestShop');
        $mail->addAddress('test-recipient@example.com', $customer['fname'] . ' ' . $customer['lname']);

        $mail->isHTML(true);
        $mail->Subject = 'Update on your Order #' . $orderId;
        $mail->Body    = $email_body;
        $mail->AltBody = 'Your order status has been updated to ' . $status;

        $mail->send();
        $_SESSION['success'] = 'Order updated and email notification sent!';
    } catch (Exception $e) {
        $_SESSION['message'] = "Order updated, but could not send email notification. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    $_SESSION['message'] = 'Error: Could not update order.';
}

header("Location: orders.php");
exit();