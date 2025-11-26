<?php
session_start();
include('../includes/config.php');

if (isset($_POST["type"]) && $_POST["type"] == 'add' && isset($_POST["item_qty"]) && $_POST["item_qty"] > 0) {
    $item_id = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
    $item_qty = filter_var($_POST['item_qty'], FILTER_VALIDATE_INT);

    if ($item_id && $item_qty) {
        $sql = "SELECT 
                    i.description, 
                    i.sell_price, 
                    MIN(ii.image_path) AS first_image
                FROM item i
                LEFT JOIN item_images ii ON i.item_id = ii.item_id
                WHERE i.item_id = ?
                GROUP BY i.item_id
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $item_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $new_product = [
                "item_name" => $row['description'],
                "item_price" => $row['sell_price'],
                "item_image" => $row['first_image'],
                "item_qty" => $item_qty,
                "item_id" => $item_id
            ];

            if (isset($_SESSION["cart_products"][$item_id])) {
                $_SESSION["cart_products"][$item_id]['item_qty'] += $item_qty;
            } else {
                $_SESSION["cart_products"][$item_id] = $new_product;
            }
            $_SESSION['success'] = "{$row['description']} was added to your cart!";
        }
    }
}

if (isset($_POST["product_qty"]) || isset($_POST["remove_code"])) {
    if (!empty($_POST["product_qty"]) && is_array($_POST["product_qty"])) {
        foreach ($_POST["product_qty"] as $key => $value) {
            $item_id = filter_var($key, FILTER_VALIDATE_INT);
            $new_qty = filter_var($value, FILTER_VALIDATE_INT);
            if ($item_id && isset($_SESSION["cart_products"][$item_id])) {
                if ($new_qty > 0) {
                    $_SESSION["cart_products"][$item_id]["item_qty"] = $new_qty;
                } else {
                    unset($_SESSION["cart_products"][$item_id]);
                }
            }
        }
    }

    if (!empty($_POST["remove_code"]) && is_array($_POST["remove_code"])) {
        foreach ($_POST["remove_code"] as $key) {
            $item_id = filter_var($key, FILTER_VALIDATE_INT);
            if ($item_id) {
                unset($_SESSION["cart_products"][$item_id]);
            }
        }
    }
}

$redirect_url = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: " . $redirect_url);
exit();