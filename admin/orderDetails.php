<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/config.php');

$orderId = $_GET['id'];

$sql_customer = "SELECT lname, fname, addressline, town, zipcode, phone, orderinfo_id, status FROM `orderdetails` WHERE orderinfo_id = $orderId LIMIT 1";
$result_customer = mysqli_query($conn, $sql_customer);
$customer = mysqli_fetch_assoc($result_customer);


$sql_items = "SELECT description, quantity, sell_price FROM `orderdetails` WHERE orderinfo_id = $orderId";
$items = mysqli_query($conn, $sql_items);
?>

<div class="container" style="padding: 20px 0;">
    <h3>Order Details: #<?php echo htmlspecialchars($customer['orderinfo_id']); ?></h3>
    
    <div class="card my-4">
        <div class="card-header">
            Customer Information
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($customer['fname']) . ' ' . htmlspecialchars($customer['lname']); ?></h5>
            <p class="card-text">
                <?php echo htmlspecialchars($customer['addressline']); ?><br>
                <?php echo htmlspecialchars($customer['town']) . ', ' . htmlspecialchars($customer['zipcode']); ?><br>
                Phone: <?php echo htmlspecialchars($customer['phone']); ?>
            </p>
        </div>
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grandTotal = 0;
            while ($row = mysqli_fetch_assoc($items)) :
                $total = $row['sell_price'] * $row['quantity'];
                $grandTotal += $total;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td>₱<?php echo number_format($row['sell_price'], 2); ?></td>
                    <td>₱<?php echo number_format($total, 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="table-info">
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong>₱<?php echo number_format($grandTotal, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <h4>Update Order Status</h4>
        <form action="updateorder.php" method="POST" class="d-flex" style="max-width: 400px;">
            <input type="hidden" name="orderId" value="<?php echo $orderId; ?>">
            <select class="form-select me-2" name="status" required>
                <option value="">Choose new status...</option>
                <option value="Processing">Processing</option>
                <option value="Delivered">Delivered</option>
                <option value="Canceled">Canceled</option>
            </select>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php
include('../includes/footer.php');
?>