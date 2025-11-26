<?php
session_start();
include('../includes/config.php');

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    die("No item ID provided.");
}

mysqli_begin_transaction($conn);
// Cyrus: Paayos nga donn, di nag dedelete ng tamta
// Donn: Okay na
try {
    $deleteImages = "DELETE FROM item_images WHERE item_id = ?";
    $stmt1 = mysqli_prepare($conn, $deleteImages);
    mysqli_stmt_bind_param($stmt1, 'i', $item_id);
    mysqli_stmt_execute($stmt1);

    $deleteStock = "DELETE FROM stock WHERE item_id = ?";
    $stmt2 = mysqli_prepare($conn, $deleteStock);
    mysqli_stmt_bind_param($stmt2, 'i', $item_id);
    mysqli_stmt_execute($stmt2);

    $deleteItem = "DELETE FROM item WHERE item_id = ?";
    $stmt3 = mysqli_prepare($conn, $deleteItem);
    mysqli_stmt_bind_param($stmt3, 'i', $item_id);
    mysqli_stmt_execute($stmt3);

    mysqli_commit($conn);
    
    $_SESSION['success'] = "Item #{$item_id} and all its data have been deleted successfully.";
    header("Location: index.php");
    exit();

} catch (mysqli_sql_exception $exception) {
    mysqli_rollback($conn);
    $_SESSION['message'] = "Error deleting item: " . $exception->getMessage();
    header("Location: index.php");
    exit();
}
?>