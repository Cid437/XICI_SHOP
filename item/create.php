<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/config.php');
?>

<div class="container" style="max-width: 800px; margin-top: 2rem;">
    <h2>Add New Item</h2>
    <form method="POST" action="store.php" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="name" placeholder="Enter item name" name="description" value="<?php echo htmlspecialchars($_SESSION['desc'] ?? ''); ?>" />
            <small class="text-danger">
                <?php
                if (isset($_SESSION['descError'])) {
                    echo $_SESSION['descError'];
                    unset($_SESSION['descError']);
                }
                ?>
            </small>
        </div>

        <div class="form-group mb-3">
            <label for="long_description" class="form-label">Detailed Description</label>
            <textarea class="form-control" id="long_description" name="long_description" rows="5" placeholder="Enter detailed product description..."><?php echo htmlspecialchars($_SESSION['long_desc'] ?? ''); ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">-- Select a Category --</option>
                <?php
                $categories = ["Guitars", "Keyboards", "Drums", "Studio"];
                $selected_category = $_SESSION['category'] ?? '';
                foreach ($categories as $cat) {
                    $selected = ($cat == $selected_category) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>$cat</option>";
                }
                ?>
            </select>
            <small class="text-danger">
                <?php
                if (isset($_SESSION['categoryError'])) {
                    echo $_SESSION['categoryError'];
                    unset($_SESSION['categoryError']);
                }
                ?>
            </small>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cost" class="form-label">Cost Price</label>
                <input type="text" class="form-control" id="cost" placeholder="e.g., 1500.00" name="cost_price" value="<?php echo htmlspecialchars($_SESSION['cost'] ?? ''); ?>" />
                <small class="text-danger">
                    <?php
                    if (isset($_SESSION['costError'])) {
                        echo $_SESSION['costError'];
                        unset($_SESSION['costError']);
                    }
                    ?>
                </small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="sell" class="form-label">Sell Price</label>
                <input type="text" class="form-control" id="sell" placeholder="e.g., 2499.00" name="sell_price" value="<?php echo htmlspecialchars($_SESSION['sell'] ?? ''); ?>">
                <small class="text-danger">
                    <?php
                    if (isset($_SESSION['sellError'])) {
                        echo $_SESSION['sellError'];
                        unset($_SESSION['sellError']);
                    }
                    ?>
                </small>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="qty" placeholder="e.g., 50" name="quantity" value="<?php echo htmlspecialchars($_SESSION['qty'] ?? '1'); ?>" />
        </div>

        <div class="form-group mb-3">
            <label for="images" class="form-label">Product Images</label>
            <input class="form-control" type="file" name="images[]" id="images" multiple />
            <small class="text-danger">
                <?php
                if (isset($_SESSION['imageError'])) {
                    echo $_SESSION['imageError'];
                    unset($_SESSION['imageError']);
                }
                ?>
            </small>
        </div>

        <button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>
        <a href="index.php" role="button" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php
unset($_SESSION['desc'], $_SESSION['long_desc'], $_SESSION['cost'], $_SESSION['sell'], $_SESSION['qty'], $_SESSION['category']);
include('../includes/footer.php');
?>