<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'You must be logged in to view your orders.';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            so.orderId, 
            so.total, 
            so.status, 
            o.date_placed 
        FROM salesperorder so
        INNER JOIN orderinfo o ON so.orderId = o.orderinfo_id
        INNER JOIN customer c ON o.customer_id = c.customer_id
        WHERE c.userId = ? 
        ORDER BY o.date_placed DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products_sql = "SELECT i.description 
                 FROM item i 
                 INNER JOIN orderline ol ON i.item_id = ol.item_id 
                 WHERE ol.orderinfo_id = ?";
$products_stmt = mysqli_prepare($conn, $products_sql);
?>

<div class="container" style="padding: 24px 0;">
    <div class="cart-view-table-back">
        <h2 class="mb-4">My Orders</h2>

        <?php include('../includes/alert.php'); ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Products</th>
                        <th>Date Placed</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($row['orderId']); ?></td>
                                <td>
                                    <?php
                                    $current_order_id = $row['orderId'];
                                    mysqli_stmt_bind_param($products_stmt, 'i', $current_order_id);
                                    mysqli_stmt_execute($products_stmt);
                                    $products_result = mysqli_stmt_get_result($products_stmt);

                                    $product_names = [];
                                    while ($product_row = mysqli_fetch_assoc($products_result)) {
                                        $product_names[] = htmlspecialchars($product_row['description']);
                                    }
                                    echo implode('<br>', $product_names);
                                    ?>
                                </td>
                                <td><?php echo date('F j, Y', strtotime($row['date_placed'])); ?></td>
                                <td>₱<?php echo number_format($row['total'], 2); ?></td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($row['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status === 'Delivered') {
                                        $badge_class = 'bg-success text-white';
                                    } elseif ($status === 'Processing') {
                                        $badge_class = 'bg-info text-dark';
                                    } elseif ($status === 'Canceled') {
                                        $badge_class = 'bg-danger text-white';
                                    }
                                    echo "<span class='badge {$badge_class}'>" . ucfirst($status) . "</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Delivered') : ?>
                                        <a href="review/review_order.php?order_id=<?php echo $row['orderId']; ?>" class="button" style="padding: 5px 10px; font-size: 12px;">View & Review</a>
                                    <?php elseif ($row['status'] === 'Processing') : ?>
                                        <a href="cancel_order.php?order_id=<?php echo $row['orderId']; ?>" class="button" style="padding: 5px 10px; font-size: 12px; background-color: #dc3545;" onclick="return confirm('Are you sure you want to cancel this order? This cannot be undone.');">
                                            Cancel Order
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center">You have not placed any orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="/XiCiTest1/index.php" class="button mt-3">Continue Shopping</a>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
mysqli_stmt_close($products_stmt);
include('../includes/footer.php');
?>