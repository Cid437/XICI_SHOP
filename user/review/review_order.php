<?php
session_start();
include('../../includes/header.php');
include('../../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header('Location: ../myorders.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            i.item_id, 
            i.description, 
            MIN(ii.image_path) AS first_image,
            r.review_id
        FROM orderline ol 
        JOIN item i ON ol.item_id = i.item_id
        LEFT JOIN item_images ii ON i.item_id = ii.item_id
        LEFT JOIN review r ON i.item_id = r.item_id AND r.user_id = ?
        WHERE ol.orderinfo_id = ?
        GROUP BY i.item_id, i.description, r.review_id";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container" style="padding: 24px 0;">
    <div class="cart-view-table-back">
        <h2 class="mb-4">Review Items from Order #<?php echo htmlspecialchars($order_id); ?></h2>
        
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <ul class="list-group">
                <?php while ($item = mysqli_fetch_assoc($result)): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <img src="/XiCiTest1/item/<?php echo htmlspecialchars($item['first_image']); ?>" alt="<?php echo htmlspecialchars($item['description']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px; border-radius: 5px;">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </div>
                        
                        <?php if ($item['review_id']): ?>
                            <a href="write_review.php?item_id=<?php echo $item['item_id']; ?>" class="btn btn-warning">Edit Your Review</a>
                        <?php else: ?>
                            <a href="write_review.php?item_id=<?php echo $item['item_id']; ?>" class="btn btn-primary">Write a Review</a>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Could not find items for this order.</p>
        <?php endif; ?>

        <a href="../myorders.php" class="btn btn-secondary mt-4">Back to My Orders</a>
    </div>
</div>

<?php
include('../../includes/footer.php'); 
?>