<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    $_SESSION['message'] = "Invalid request.";
    header('Location: myorders.php');
    exit();
}

$order_id_to_cancel = intval($_GET['order_id']);
$logged_in_user_id = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {
    $sql_verify = "SELECT o.orderinfo_id, o.status FROM orderinfo o
                   JOIN customer c ON o.customer_id = c.customer_id
                   WHERE o.orderinfo_id = ? AND c.userId = ?
                   LIMIT 1";
    $stmt_verify = mysqli_prepare($conn, $sql_verify);
    mysqli_stmt_bind_param($stmt_verify, 'ii', $order_id_to_cancel, $logged_in_user_id);
    mysqli_stmt_execute($stmt_verify);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_verify));

    if (!$order || $order['status'] !== 'Processing') {
        throw new Exception("This order cannot be canceled.");
    }

    $sql_items = "SELECT item_id, quantity FROM orderline WHERE orderinfo_id = ?";
    $stmt_items = mysqli_prepare($conn, $sql_items);
    mysqli_stmt_bind_param($stmt_items, 'i', $order_id_to_cancel);
    mysqli_stmt_execute($stmt_items);
    $result_items = mysqli_stmt_get_result($stmt_items);

    $sql_stock_update = "UPDATE stock SET quantity = quantity + ? WHERE item_id = ?";
    $stmt_stock_update = mysqli_prepare($conn, $sql_stock_update);

    while ($item = mysqli_fetch_assoc($result_items)) {
        mysqli_stmt_bind_param($stmt_stock_update, 'ii', $item['quantity'], $item['item_id']);
        mysqli_stmt_execute($stmt_stock_update);
    }

    $sql_cancel = "UPDATE orderinfo SET status = 'Canceled' WHERE orderinfo_id = ?";
    $stmt_cancel = mysqli_prepare($conn, $sql_cancel);
    mysqli_stmt_bind_param($stmt_cancel, 'i', $order_id_to_cancel);
    mysqli_stmt_execute($stmt_cancel);

    mysqli_commit($conn);
    $_SESSION['success'] = "Order #{$order_id_to_cancel} has been successfully canceled.";

} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['message'] = $e->getMessage();
}

header('Location: myorders.php');
exit();
?>