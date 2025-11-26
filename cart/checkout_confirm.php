<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to proceed to checkout.";
    header("Location: ../user/login.php");
    exit;
}
if (empty($_SESSION['cart_products'])) {
    header("Location: view_cart.php");
    exit;
}

$userId = $_SESSION['user_id'];

$addresses = [];
$asql = "SELECT * FROM addresses WHERE user_id = ?";
$astmt = mysqli_prepare($conn, $asql);
mysqli_stmt_bind_param($astmt, 'i', $userId);
mysqli_stmt_execute($astmt);
$ares = mysqli_stmt_get_result($astmt);
while ($row = mysqli_fetch_assoc($ares)) {
    $addresses[] = $row;
}
mysqli_stmt_close($astmt);
?>

<div class="container-xl px-4 mt-4 mb-5">
    <h2 class="mb-4">Confirm Your Order</h2>

    <form action="process_order.php" method="POST">
        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">1. Select Shipping Address</div>
                    <div class="card-body">
                        <?php if (empty($addresses)) : ?>
                            <div class="alert alert-warning">
                                You have no saved addresses. Please <a href="../user/profile.php#addresses">add a shipping address</a> before proceeding.
                            </div>
                        <?php else : ?>
                            <?php foreach ($addresses as $index => $address) : ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="selected_address_id" id="address<?php echo $address['address_id']; ?>" value="<?php echo $address['address_id']; ?>" <?php echo $index == 0 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="address<?php echo $address['address_id']; ?>">
                                        <strong><?php echo htmlspecialchars($address['recipient_name']); ?></strong><br>
                                        <?php echo htmlspecialchars($address['address_line1']); ?><br>
                                        <?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['province']) . ' ' . htmlspecialchars($address['zip_code']); ?><br>
                                        Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">2. Select Mode of Payment</div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="payment_cod" value="cod" checked>
                            <label class="form-check-label" for="payment_cod">Cash on Delivery (COD)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="payment_card" value="card" disabled>
                            <label class="form-check-label text-muted" for="payment_card">Credit/Debit Card (Not Available)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="payment_paypal" value="paypal" disabled>
                            <label class="form-check-label text-muted" for="payment_paypal">PayPal (Not Available)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">3. Order Summary</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $total = 0;
                            foreach ($_SESSION["cart_products"] as $item) :
                                $subtotal = $item['item_price'] * $item['item_qty'];
                                $total += $subtotal;
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                    <span class="badge bg-secondary rounded-pill"><?php echo $item['item_qty']; ?></span>
                                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5">
                                Total
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary btn-lg" <?php echo empty($addresses) ? 'disabled' : ''; ?>>
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>