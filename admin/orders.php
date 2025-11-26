<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/config.php');

$sql = "SELECT * FROM `salesperorder` ORDER BY orderId DESC";
$result = mysqli_query($conn, $sql);
$itemCount = mysqli_num_rows($result);
?>

<div class="container" style="padding: 20px 0;">
    <h2>Order Management (<?php echo $itemCount; ?> Total)</h2>
    
    <?php include("../includes/alert.php"); ?>

    <table class="table table-striped table-bordered mt-4">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Total Value</th>
                <th>Status</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['orderId']); ?></td>
                    <td>₱<?php echo number_format($row['total'], 2); ?></td>
                    <?php
                    if ($row['status'] === 'Delivered') {
                        echo "<td style='color: green;'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td><i class='fa-regular fa-eye' style='color: gray;'></i></td>";
                    } else {
                        $status_color = ($row['status'] === 'Processing') ? 'orange' : 'red';
                        echo "<td style='color: " . $status_color . ";'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td><a href='orderDetails.php?id=" . htmlspecialchars($row['orderId']) . "'><i class='fa-regular fa-eye' style='color: blue;'></i></a></td>";
                    }
                    ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
include('../includes/footer.php');
?>