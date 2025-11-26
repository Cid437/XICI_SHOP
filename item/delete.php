<?php
include('../includes/header.php');
include('../includes/config.php');

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // First delete from stock (because of FK)
    $deleteStock = "DELETE FROM stock WHERE item_id = ?";
    $stmt1 = mysqli_prepare($conn, $deleteStock);
    mysqli_stmt_bind_param($stmt1, 'i', $item_id);

    // Then delete the item itself
    $deleteItem = "DELETE FROM item WHERE item_id = ?";
    $stmt2 = mysqli_prepare($conn, $deleteItem);
    mysqli_stmt_bind_param($stmt2, 'i', $item_id);

    if (mysqli_stmt_execute($stmt1) && mysqli_stmt_execute($stmt2)) {
        header("Location: index.php?deleted=1");
        exit;
    } else {
        echo " Error deleting item: " . mysqli_error($conn);
    }
} else {
    echo "No item ID provided.";
}
?>
