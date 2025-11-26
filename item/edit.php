<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    $sql = "SELECT i.item_id, i.description, i.cost_price, i.sell_price, i.img_path, s.quantity 
            FROM item i 
            INNER JOIN stock s ON i.item_id = s.item_id 
            WHERE i.item_id = $item_id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        die(" Item not found.");
    }
} else {
    die(" No item ID provided.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = $_POST['description'];
    $cost = $_POST['cost_price'];
    $sell = $_POST['sell_price'];
    $qty = $_POST['quantity'];
    $img_path = $item['img_path']; // keep old image by default

    // Handle new image upload if provided
    if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] == 0) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['img_path']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['img_path']['tmp_name'], $targetFile)) {
            $img_path = $targetFile;
        }
    }

    $updateItem = "UPDATE item SET description = ?, cost_price = ?, sell_price = ?, img_path = ? WHERE item_id = ?";
    $stmt1 = mysqli_prepare($conn, $updateItem);
    mysqli_stmt_bind_param($stmt1, 'sddsi', $desc, $cost, $sell, $img_path, $item_id);

    $updateStock = "UPDATE stock SET quantity = ? WHERE item_id = ?";
    $stmt2 = mysqli_prepare($conn, $updateStock);
    mysqli_stmt_bind_param($stmt2, 'ii', $qty, $item_id);

    if (mysqli_stmt_execute($stmt1) && mysqli_stmt_execute($stmt2)) {
        $_SESSION['success'] = "Item updated successfully!";
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating item: " . mysqli_error($conn);
    }
}
?>

<div class="container">
    <h2>Edit Item</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Description</label>
        <input type="text" name="description" class="form-control" value="<?php echo $item['description']; ?>" required>

        <label>Cost Price</label>
        <input type="text" name="cost_price" class="form-control" value="<?php echo $item['cost_price']; ?>" required>

        <label>Sell Price</label>
        <input type="text" name="sell_price" class="form-control" value="<?php echo $item['sell_price']; ?>" required>

        <label>Quantity</label>
        <input type="number" name="quantity" class="form-control" value="<?php echo $item['quantity']; ?>" required>

        <label>Current Image</label><br>
        <?php if (!empty($item['img_path'])): ?>
            <img src="<?php echo $item['img_path']; ?>" alt="Item Image" width="100"><br>
        <?php else: ?>
            <p>No image uploaded</p>
        <?php endif; ?>

        <label>Change Image (optional)</label>
        <input type="file" name="img_path" class="form-control"><br>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
