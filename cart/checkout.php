<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart_products'])) {
    header('Location: ../index.php');
    exit();
}

try {
    $sql_customer = "SELECT customer_id FROM customer WHERE userId = ? LIMIT 1";
    $stmt_customer = mysqli_prepare($conn, $sql_customer);
    mysqli_stmt_bind_param($stmt_customer, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_customer);
    mysqli_stmt_bind_result($stmt_customer, $customer_id);
    mysqli_stmt_fetch($stmt_customer);
    mysqli_stmt_close($stmt_customer);

    if (empty($customer_id)) {
        $_SESSION['message'] = 'Please complete your profile before checking out.';
        header('Location: ../user/profile.php');
        exit();
    }

    mysqli_begin_transaction($conn);

    $q_orderinfo = 'INSERT INTO orderinfo(customer_id, date_placed, shipping) VALUES (?, NOW(), ?)';
    $shipping = 10.00;

    $stmt1 = mysqli_prepare($conn, $q_orderinfo);
    mysqli_stmt_bind_param($stmt1, 'id', $customer_id, $shipping);
    mysqli_stmt_execute($stmt1);
    $orderinfo_id = mysqli_insert_id($conn);

    $q_orderline = 'INSERT INTO orderline(orderinfo_id, item_id, quantity) VALUES (?, ?, ?)';
    $stmt2 = mysqli_prepare($conn, $q_orderline);

    $q_stock = 'UPDATE stock SET quantity = quantity - ? WHERE item_id = ?';
    $stmt3 = mysqli_prepare($conn, $q_stock);

    foreach ($_SESSION["cart_products"] as $cart_itm) {
        $product_qty = $cart_itm["item_qty"];
        $product_code = $cart_itm["item_id"];

        mysqli_stmt_bind_param($stmt2, 'iii', $orderinfo_id, $product_code, $product_qty);
        mysqli_stmt_execute($stmt2);

        mysqli_stmt_bind_param($stmt3, 'ii', $product_qty, $product_code);
        mysqli_stmt_execute($stmt3);
    }

    mysqli_commit($conn);
    unset($_SESSION['cart_products']);

    $_SESSION['success'] = 'Your order has been placed successfully!';
    header('Location: ../user/myorders.php');
    exit();

} catch (mysqli_sql_exception $e) {
    mysqli_rollback($conn);
    $_SESSION['message'] = 'There was an error placing your order. Please try again. Error: ' . $e->getMessage();
    header('Location: view_cart.php');
    exit();
}