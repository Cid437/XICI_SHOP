<?php
session_start();
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["product_qty"]) && is_array($_POST["product_qty"])) {
        foreach ($_POST["product_qty"] as $key => $value) {
            $item_id = filter_var($key, FILTER_VALIDATE_INT);
            $new_qty = filter_var($value, FILTER_VALIDATE_INT);
            if ($item_id && isset($_SESSION["cart_products"][$item_id])) {
                if ($new_qty > 0) {
                    $_SESSION["cart_products"][$item_id]["item_qty"] = $new_qty;
                } else {
                    unset($_SESSION["cart_products"][$item_id]);
                }
            }
        }
    }

    if (isset($_POST["remove_code"]) && is_array($_POST["remove_code"])) {
        foreach ($_POST["remove_code"] as $key) {
            $item_id = filter_var($key, FILTER_VALIDATE_INT);
            if ($item_id) {
                unset($_SESSION["cart_products"][$item_id]);
            }
        }
    }

    header("Location: view_cart.php");
    exit;
}

include('../includes/header.php');
?>

<div class="container-xl px-4 mt-4 mb-5">
    <div class="cart-view-table-back">
        <h2 class="mb-4">Your Shopping Cart</h2>

        <?php if (empty($_SESSION["cart_products"])) : ?>
            <div class="alert alert-info">Your shopping cart is empty.</div>
            <a href="/XiCiTest1/index.php" class="btn btn-primary">Continue Shopping</a>
        <?php else : ?>
            <form method="POST" action="view_cart.php">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Product</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($_SESSION["cart_products"] as $cart_itm) {
                                $product_name = htmlspecialchars($cart_itm["item_name"]);
                                $product_qty = $cart_itm["item_qty"];
                                $product_price = $cart_itm["item_price"];
                                $product_code = $cart_itm["item_id"];
                                $subtotal = ($product_price * $product_qty);
                                $total += $subtotal;

                                echo '<tr>';
                                echo '<td>' . $product_name . '</td>';
                                echo '<td class="text-end">₱' . number_format($product_price, 2) . '</td>';
                                echo '<td class="text-center" style="width: 15%;"><input type="number" class="form-control mx-auto" style="max-width: 100px;" name="product_qty[' . $product_code . ']" value="' . $product_qty . '" min="1" /></td>';
                                echo '<td class="text-end">₱' . number_format($subtotal, 2) . '</td>';
                                echo '<td class="text-center"><input type="checkbox" class="form-check-input" name="remove_code[]" value="' . $product_code . '" /></td>';
                                echo '</tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="5" class="text-end fs-5 fw-bold pt-3">Amount Payable: <?php echo '₱' . number_format($total, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="/XiCiTest1/index.php" class="btn btn-secondary">Continue Shopping</a>
                    <div>
                        <button type="submit" class="btn btn-info">Update Cart</button>
                        <!-- Donn: cy, binago ko part nato para sa tamang link na sya pumunta -->
                        <a href="checkout_confirm.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>