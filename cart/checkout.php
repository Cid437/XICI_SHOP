<?php
session_start();
// No header include needed here, as this script should produce no output, only a redirect.
include('../includes/config.php');

// Ensure the user is logged in and the cart is not empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart_products'])) {
    // Redirect them somewhere safe if they aren't supposed to be here
    header('Location: ../index.php');
    exit();
}

try {
    // --- 1. Find the customer_id safely ---
    // CORRECTED: Column name is 'userId', and now uses a prepared statement for security.
    $sql_customer = "SELECT customer_id FROM customer WHERE userId = ? LIMIT 1";
    $stmt_customer = mysqli_prepare($conn, $sql_customer);
    mysqli_stmt_bind_param($stmt_customer, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_customer);
    mysqli_stmt_bind_result($stmt_customer, $customer_id);
    mysqli_stmt_fetch($stmt_customer);
    mysqli_stmt_close($stmt_customer);

    // ADDED: Check if a customer profile exists. If not, redirect them to create one.
    if (empty($customer_id)) {
        $_SESSION['message'] = 'Please complete your profile before checking out.';
        header('Location: ../user/profile.php');
        exit();
    }

    // --- 2. Start the database transaction ---
    mysqli_begin_transaction($conn);

    // --- 3. Insert the main order information ---
    $q_orderinfo = 'INSERT INTO orderinfo(customer_id, date_placed, shipping) VALUES (?, NOW(), ?)';
    $shipping = 10.00; // Example shipping cost
    
    $stmt1 = mysqli_prepare($conn, $q_orderinfo);
    mysqli_stmt_bind_param($stmt1, 'id', $customer_id, $shipping);
    mysqli_stmt_execute($stmt1);
    $orderinfo_id = mysqli_insert_id($conn); // Get the ID of the new order

    // --- 4. Prepare statements for inserting order lines and updating stock ---
    $q_orderline = 'INSERT INTO orderline(orderinfo_id, item_id, quantity) VALUES (?, ?, ?)';
    $stmt2 = mysqli_prepare($conn, $q_orderline);
    
    $q_stock = 'UPDATE stock SET quantity = quantity - ? WHERE item_id = ?';
    $stmt3 = mysqli_prepare($conn, $q_stock);

    // --- 5. Loop through cart items and execute the prepared statements ---
    foreach ($_SESSION["cart_products"] as $cart_itm) {
        $product_qty = $cart_itm["item_qty"];
        $product_code = $cart_itm["item_id"];

        // Bind and execute for orderline
        mysqli_stmt_bind_param($stmt2, 'iii', $orderinfo_id, $product_code, $product_qty);
        mysqli_stmt_execute($stmt2);

        // Bind and execute for stock update
        mysqli_stmt_bind_param($stmt3, 'ii', $product_qty, $product_code);
        mysqli_stmt_execute($stmt3);
    }
    
    // MOVED: Commit the transaction only AFTER the loop is fully completed.
    mysqli_commit($conn);
    
    // MOVED: Clear the cart only after a successful commit.
    unset($_SESSION['cart_products']);

    // ADDED: Redirect to a success page or 'my orders' page.
    $_SESSION['success'] = 'Your order has been placed successfully!';
    header('Location: ../user/myorders.php');
    exit();

} catch (mysqli_sql_exception $e) {
    mysqli_rollback($conn); // If anything fails, undo all changes.
    
    // Set an error message and redirect back to the cart
    $_SESSION['message'] = 'There was an error placing your order. Please try again. Error: ' . $e->getMessage();
    header('Location: view_cart.php');
    exit();
}
?>