<?php
session_start();
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || empty($_SESSION['cart_products'])) {
    header("Location: ../index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$selected_address_id = $_POST['selected_address_id'] ?? 0;

if ($selected_address_id <= 0) {
    $_SESSION['message'] = "Please select a valid shipping address.";
    header("Location: checkout_confirm.php");
    exit;
}

mysqli_begin_transaction($conn);

try {
    $customer_id = null;
    $sql_get_customer = "SELECT customer_id FROM customer WHERE userId = ? LIMIT 1";
    $stmt_get_customer = mysqli_prepare($conn, $sql_get_customer);
    mysqli_stmt_bind_param($stmt_get_customer, 'i', $userId);
    mysqli_stmt_execute($stmt_get_customer);
    $res = mysqli_stmt_get_result($stmt_get_customer);
    if ($row = mysqli_fetch_assoc($res)) {
        $customer_id = $row['customer_id'];
    }
    mysqli_stmt_close($stmt_get_customer);

    if ($customer_id === null) {
        throw new Exception("Customer profile not found. Please update your profile.");
    }

    $shipping_fee = 50.00;
    $sql_orderinfo = 'INSERT INTO orderinfo(customer_id, date_placed, shipping, shipping_address_id, status) VALUES (?, NOW(), ?, ?, "Processing")';
    $stmt_orderinfo = mysqli_prepare($conn, $sql_orderinfo);
    mysqli_stmt_bind_param($stmt_orderinfo, 'idi', $customer_id, $shipping_fee, $selected_address_id);
    mysqli_stmt_execute($stmt_orderinfo);

    $orderinfo_id = mysqli_insert_id($conn);
    if ($orderinfo_id == 0) {
        throw new Exception("Failed to create order.");
    }

    $sql_orderline = 'INSERT INTO orderline(orderinfo_id, item_id, quantity) VALUES (?, ?, ?)';
    $stmt_orderline = mysqli_prepare($conn, $sql_orderline);

    $sql_stock = 'UPDATE stock SET quantity = quantity - ? WHERE item_id = ?';
    $stmt_stock = mysqli_prepare($conn, $sql_stock);

    foreach ($_SESSION["cart_products"] as $cart_item) {
        $product_id = $cart_item["item_id"];
        $product_qty = $cart_item["item_qty"];

        mysqli_stmt_bind_param($stmt_orderline, 'iii', $orderinfo_id, $product_id, $product_qty);
        mysqli_stmt_execute($stmt_orderline);

        mysqli_stmt_bind_param($stmt_stock, 'ii', $product_qty, $product_id);
        mysqli_stmt_execute($stmt_stock);
    }

    mysqli_commit($conn);

    unset($_SESSION['cart_products']);

    $_SESSION['success'] = "Your order has been placed successfully! Your Order ID is #{$orderinfo_id}.";
    header("Location: ../user/myorders.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['message'] = "An error occurred while placing your order: " . $e->getMessage();
    header("Location: checkout_confirm.php");
    exit;
}