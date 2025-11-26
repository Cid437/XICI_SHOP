<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? null;
    if (!$item_id) {
        die("Invalid submission. Item ID is missing.");
    }

    $desc = $_POST['description'];
    $long_desc = $_POST['long_description'];
    $category = $_POST['category'];
    $cost = $_POST['cost_price'];
    $sell = $_POST['sell_price'];
    $qty = $_POST['quantity'];

    mysqli_begin_transaction($conn);
    try {
        if (!empty($_POST['delete_images'])) {
            $delete_ids = $_POST['delete_images'];
            $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));
            $types = str_repeat('i', count($delete_ids));

            $sql_delete = "DELETE FROM item_images WHERE image_id IN ($placeholders)";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, $types, ...$delete_ids);
            mysqli_stmt_execute($stmt_delete);
        }

        if (!empty($_FILES['new_images']['name'][0])) {
            $sql_image = "INSERT INTO item_images(item_id, image_path) VALUES (?, ?)";
            $stmt_image = mysqli_prepare($conn, $sql_image);
            $total_files = count($_FILES['new_images']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                $source = $_FILES['new_images']['tmp_name'][$i];
                $filename = time() . '_' . basename($_FILES['new_images']['name'][$i]);
                $target = 'images/' . $filename;
                if (move_uploaded_file($source, $target)) {
                    mysqli_stmt_bind_param($stmt_image, 'is', $item_id, $target);
                    mysqli_stmt_execute($stmt_image);
                }
            }
        }

        $updateItem = "UPDATE item SET description = ?, long_description = ?, category = ?, cost_price = ?, sell_price = ? WHERE item_id = ?";
        $stmt1 = mysqli_prepare($conn, $updateItem);
        mysqli_stmt_bind_param($stmt1, 'sssddi', $desc, $long_desc, $category, $cost, $sell, $item_id);
        mysqli_stmt_execute($stmt1);

        $updateStock = "UPDATE stock SET quantity = ? WHERE item_id = ?";
        $stmt2 = mysqli_prepare($conn, $updateStock);
        mysqli_stmt_bind_param($stmt2, 'ii', $qty, $item_id);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);

        $_SESSION['success'] = "Item updated successfully!";
        header("Location: index.php");
        exit;
    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($conn);
        $error_message = "Error updating item: " . $exception->getMessage();
    }
}

include('../includes/header.php');

$item_id = $_GET['id'] ?? null;
if (!$item_id && !isset($error_message)) {
    die("No item ID provided.");
}

$sql_item = "SELECT i.item_id, i.description, i.long_description, i.category, i.cost_price, i.sell_price, s.quantity 
             FROM item i 
             INNER JOIN stock s ON i.item_id = s.item_id 
             WHERE i.item_id = ?";
$stmt = mysqli_prepare($conn, $sql_item);
mysqli_stmt_bind_param($stmt, 'i', $item_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if (!$item && !isset($error_message)) {
    die("Item not found.");
}

$sql_images = "SELECT image_id, image_path FROM item_images WHERE item_id = ?";
$stmt_images = mysqli_prepare($conn, $sql_images);
mysqli_stmt_bind_param($stmt_images, 'i', $item_id);
mysqli_stmt_execute($stmt_images);
$images_result = mysqli_stmt_get_result($stmt_images);
?>

<div class="container" style="max-width: 800px; margin-top: 2rem;">
    <h2>Edit Item</h2>

    <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">

        <div class="form-group mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($item['description']); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Detailed Description</label>
            <textarea class="form-control" name="long_description" rows="5"><?php echo htmlspecialchars($item['long_description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">-- Select a Category --</option>
                <?php
                $categories = ["Guitars", "Keyboards", "Drums", "Studio"];
                foreach ($categories as $cat) {
                    $selected = ($cat == $item['category']) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>$cat</option>";
                }
                ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Cost Price</label>
                <input type="text" name="cost_price" class="form-control" value="<?php echo htmlspecialchars($item['cost_price']); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Sell Price</label>
                <input type="text" name="sell_price" class="form-control" value="<?php echo htmlspecialchars($item['sell_price']); ?>" required>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Current Images</label><br>
            <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                <?php while ($img = mysqli_fetch_assoc($images_result)) : ?>
                    <div style="text-align: center;">
                        <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Item Image" width="150"><br>
                        <input type="checkbox" name="delete_images[]" value="<?php echo $img['image_id']; ?>">
                        <label>Delete</label>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Upload New Images</label>
            <input type="file" name="new_images[]" class="form-control" multiple><br>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('../includes/footer.php'); ?>