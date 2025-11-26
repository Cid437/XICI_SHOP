<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/config.php';

$sql = "SELECT 
            i.item_id, i.description, i.category, i.cost_price, i.sell_price,
            s.quantity,
            MIN(ii.image_path) AS first_image
        FROM item i
        LEFT JOIN stock s ON i.item_id = s.item_id
        LEFT JOIN item_images ii ON i.item_id = ii.item_id
        GROUP BY i.item_id 
        ORDER BY i.item_id DESC";

$result = mysqli_query($conn, $sql);
$itemCount = mysqli_num_rows($result);
?>

<div class="container" style="padding:18px; max-width: 1200px; display: block;">
    <?php include('../includes/alert.php'); ?>
    
    <p><a href="create.php" class="btn btn-primary">Add Item</a></p>
    <h2>Item Management (<?php echo $itemCount; ?> items)</h2>

    <table class="table table-striped" style="width:100%; margin-top:12px;">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>Description</th>
                <th>Category</th>
                <th>Sell Price</th>
                <th>Cost Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                $img = htmlspecialchars($row['first_image'] ?? 'images/placeholder.png');
                $id = htmlspecialchars($row['item_id']);
                $desc = htmlspecialchars($row['description']);
                $category = htmlspecialchars($row['category']);
                $sell = number_format((float)$row['sell_price'], 2);
                $cost = number_format((float)$row['cost_price'], 2);
                $qty = htmlspecialchars($row['quantity'] ?? '0');

                echo "<tr style='vertical-align: middle;'>";
                echo "<td><img src='../item/{$img}' alt='{$desc}' style='width:100px;height:100px;object-fit:cover;border-radius:6px' /></td>";
                echo "<td>{$id}</td>";
                echo "<td>{$desc}</td>";
                echo "<td>{$category}</td>";
                echo "<td>₱{$sell}</td>";
                echo "<td>₱{$cost}</td>";
                echo "<td>{$qty}</td>";
                echo "<td>";
                echo "<a href='edit.php?id={$id}' title='Edit'><i class='fa-regular fa-pen-to-square' style='font-size: 1.2rem; color: #8f5538; margin-right:8px'></i></a>";
                echo "<a href='delete.php?id={$id}' title='Delete' onclick=\"return confirm('Delete item #{$id}?')\"><i class='fa-solid fa-trash' style='font-size: 1.2rem; color: #a33'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
include __DIR__ . '/../includes/footer.php';
?>