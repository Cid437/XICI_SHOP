<?php
session_start();
// The two includes below must be relative to the 'user' folder
include('../includes/header.php'); 
include('../includes/config.php');

// SECURITY: If user is not logged in, redirect them to the login page
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = 'You must be logged in to view your orders.';
    header('Location: login.php');
    exit();
}

// Prepare the SQL query to get all orders for the current user.
// This joins the existing salesperorder VIEW with the customer table to filter by the logged-in user.
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
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

?>

<div class="container" style="padding: 24px 0;">
    <div class="cart-view-table-back">
        <h2 class="mb-4">My Orders</h2>

        <?php 
        // This will display the success message after a checkout
        include('../includes/alert.php'); 
        ?>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date Placed</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['orderId']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($row['date_placed'])); ?></td>
                                <td>₱<?php echo number_format($row['total'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status = htmlspecialchars($row['status']);
                                    $badge_class = 'bg-secondary'; // Default badge
                                    if ($status === 'Delivered') {
                                        $badge_class = 'bg-success';
                                    } elseif ($status === 'Processing') {
                                        $badge_class = 'bg-info';
                                    } elseif ($status === 'Canceled') {
                                        $badge_class = 'bg-danger';
                                    }
                                    echo "<span class='badge {$badge_class}'>" . ucfirst($status) . "</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">You have not placed any orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="/XiCiTest1/index.php" class="button mt-3">Continue Shopping</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>