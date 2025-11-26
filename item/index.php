<?php
session_start();

// header for admin pages (adjust path if you use a different header file)
include __DIR__ . '/../includes/adminHeader.php';
// DB config - make sure this file defines $conn (mysqli connection)
include __DIR__ . '/../includes/config.php';

// Debug (optional)
// print_r($_SESSION);

// Access control (uncomment to enforce login)
// if (!isset($_SESSION['user_id'])) {
//     $_SESSION['message'] = "please Login to access the page";
//     header("Location: ../user/login.php" );
//     exit;
// }

$keyword = '';
if (isset($_GET['search'])) {
    $keyword = strtolower(trim($_GET['search']));
}

if ($keyword !== '') {
    // escape input for safety
    $safe = mysqli_real_escape_string($conn, $keyword);
    $sql = "SELECT * FROM item LEFT JOIN stock USING (item_id) WHERE LOWER(description) LIKE '%{$safe}%'";
} else {
    $sql = "SELECT * FROM item LEFT JOIN stock USING (item_id)";
}

$result = mysqli_query($conn, $sql);
if ($result === false) {
    // simple error handling
    $err = mysqli_error($conn);
    echo "<div class='container'><p style='color:red'>Database error: " . htmlspecialchars($err) . "</p></div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$itemCount = mysqli_num_rows($result);
?>

<div class="container" style="padding:18px">
    <p><a href="create.php" class="btn btn-primary">Add Item</a></p>
    <h2>Number of items: <?php echo $itemCount; ?></h2>

    <table class="table" style="width:100%;border-collapse:collapse;margin-top:12px">
        <thead>
            <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06);">
                <th>Image</th>
                <th>ID</th>
                <th>Description</th>
                <th>Sell Price</th>
                <th>Cost Price</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $img = htmlspecialchars($row['img_path'] ?? '');
            $id = htmlspecialchars($row['item_id'] ?? '');
            $desc = htmlspecialchars($row['description'] ?? '');
            $sell = isset($row['sell_price']) ? number_format((float)$row['sell_price'], 2) : '';
            $cost = isset($row['cost_price']) ? number_format((float)$row['cost_price'], 2) : '';
            $qty = htmlspecialchars($row['quantity'] ?? '0');

            echo "<tr style='border-bottom:1px solid rgba(0,0,0,0.04)'>";
            echo "<td><img src='{$img}' alt='" . $desc . "' style='width:120px;height:120px;object-fit:cover;border-radius:6px' /></td>";
            echo "<td>{$id}</td>";
            echo "<td>{$desc}</td>";
            echo "<td>₱{$sell}</td>";
            echo "<td>₱{$cost}</td>";
            echo "<td>{$qty}</td>";

            $edit = 'edit.php?id=' . urlencode($row['item_id']);
            $del = 'delete.php?id=' . urlencode($row['item_id']);

            echo "<td><a href='{$edit}' title='Edit'><i class='fa-regular fa-pen-to-square' style='color: #8f5538; margin-right:8px'></i></a>";
            echo "<a href='{$del}' title='Delete' onclick=\"return confirm('Delete item #{$id}?')\"><i class='fa-solid fa-trash' style='color: #a33'></i></a></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<?php
include __DIR__ . '/../includes/footer.php';
?>
