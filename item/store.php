<?php
session_start();
include('../includes/config.php');

$_SESSION['cost'] = trim($_POST['cost_price'] ?? '');
$_SESSION['sell'] = trim($_POST['sell_price'] ?? '');
$_SESSION['desc'] = trim($_POST['description'] ?? '');
$_SESSION['long_desc'] = trim($_POST['long_description'] ?? '');
$_SESSION['qty'] = trim($_POST['quantity'] ?? '');
$_SESSION['category'] = trim($_POST['category'] ?? '');

if (isset($_POST['submit'])) {
    $cost = $_SESSION['cost'];
    $sell = $_SESSION['sell'];
    $desc = $_SESSION['desc'];
    $long_desc = $_SESSION['long_desc'];
    $qty = $_SESSION['qty'];
    $category = $_SESSION['category'];
    $has_error = false;

    if (empty($desc)) {
        $_SESSION['descError'] = 'Please input a product name.';
        $has_error = true;
    }
    if (empty($category)) {
        $_SESSION['categoryError'] = 'Please select a category.';
        $has_error = true;
    }
    if (empty($cost) || !is_numeric($cost)) {
        $_SESSION['costError'] = 'Please enter a valid cost price.';
        $has_error = true;
    }
    if (empty($sell) || !is_numeric($sell)) {
        $_SESSION['sellError'] = 'Please enter a valid sell price.';
        $has_error = true;
    }

    if (empty($_FILES['images']['name'][0])) {
        $_SESSION['imageError'] = "Please select at least one image file.";
        $has_error = true;
    } else {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $total_files = count($_FILES['images']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            $filename = $_FILES['images']['name'][$i];
            $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                $_SESSION['imageError'] = "Invalid file type: '{$filename}'. Only JPG, JPEG, PNG, and GIF are allowed.";
                $has_error = true;
                break;
            }
        }
    }

    if ($has_error) {
        header("Location: create.php");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        $sql_item = "INSERT INTO item(description, long_description, cost_price, sell_price, category) VALUES(?, ?, ?, ?, ?)";
        $stmt1 = mysqli_prepare($conn, $sql_item);
        mysqli_stmt_bind_param($stmt1, 'ssdds', $desc, $long_desc, $cost, $sell, $category);
        mysqli_stmt_execute($stmt1);
        $last_item_id = mysqli_insert_id($conn);

        $sql_stock = "INSERT INTO stock(item_id, quantity) VALUES(?, ?)";
        $stmt2 = mysqli_prepare($conn, $sql_stock);
        mysqli_stmt_bind_param($stmt2, 'ii', $last_item_id, $qty);
        mysqli_stmt_execute($stmt2);

        $sql_image = "INSERT INTO item_images(item_id, image_path) VALUES (?, ?)";
        $stmt3 = mysqli_prepare($conn, $sql_image);
        
        for ($i = 0; $i < $total_files; $i++) {
            $source = $_FILES['images']['tmp_name'][$i];
            $filename = time() . '_' . basename($_FILES['images']['name'][$i]);
            $target = 'images/' . $filename;
            
            if (move_uploaded_file($source, $target)) {
                mysqli_stmt_bind_param($stmt3, 'is', $last_item_id, $target);
                mysqli_stmt_execute($stmt3);
            }
        }
        
        mysqli_commit($conn);
        
        $_SESSION['success'] = "Item added successfully!";
        unset($_SESSION['desc'], $_SESSION['long_desc'], $_SESSION['cost'], $_SESSION['sell'], $_SESSION['qty'], $_SESSION['category']);
        header("Location: index.php");
        exit();

    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($conn);
        $_SESSION['message'] = "Error adding item: " . $exception->getMessage();
        header("Location: create.php");
        exit();
    }
}