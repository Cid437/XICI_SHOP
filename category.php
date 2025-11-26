<?php
session_start();
include('./includes/header.php');
include('./includes/alert.php');
include('./includes/config.php');

$category = trim($_GET['cat'] ?? '');
$results = false;

if (!empty($category)) {
    $sql = "SELECT 
                i.item_id AS itemId, 
                i.description, 
                i.sell_price, 
                MIN(ii.image_path) AS first_image
            FROM item i
            LEFT JOIN item_images ii ON i.item_id = ii.item_id
            WHERE i.category = ? 
            GROUP BY i.item_id 
            ORDER BY i.item_id ASC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $category);
    mysqli_stmt_execute($stmt);
    $results = mysqli_stmt_get_result($stmt);
}
?>

<div class="products-container">
    <a href="index.php" class="button-filter mb-4">&laquo; Back to All Products</a>

    <h2 class="section-title">
        Products in: <?php echo htmlspecialchars(ucfirst($category)); ?>
    </h2>

    <?php
    if ($results && mysqli_num_rows($results) > 0) {
        $products_item = '<ul class="products">';

        while ($row = mysqli_fetch_assoc($results)) {
            $desc = htmlspecialchars($row['description']);
            $img_filename = htmlspecialchars($row['first_image'] ?? 'images/placeholder.png');
            $price = htmlspecialchars($row['sell_price']);
            $itemId = htmlspecialchars($row['itemId']);
            $image_path = "item/" . $img_filename;

            // This structure is kept identical to your index.php for consistency
            $products_item .= "<li class=\"product\">\n" .
                "  <form method=\"POST\" action=\"cart/cart_update.php\">\n" .
                "    <div class=\"product-content\">\n" .
                "      <div class=\"product-thumb\"><a href='product.php?id={$itemId}'><img src=\"{$image_path}\" alt=\"{$desc}\"></a></div>\n" .
                "      <div class=\"product-details\">\n" .
                "        <h3><a href='product.php?id={$itemId}'>{$desc}</a></h3>\n" .
                "        <div class='product-price'>₱" . number_format((float)$price, 2) . "</div>\n" .
                "        <div class='add-to-cart-form'>\n" .
                "          <label><span>Quantity</span><input type=\"number\" class='form-control form-control-sm' name=\"item_qty\" value=\"1\" /></label>\n" .
                "          <input type=\"hidden\" name=\"item_id\" value=\"{$itemId}\" />\n" .
                "          <input type=\"hidden\" name=\"type\" value=\"add\" />\n" .
                "          <button type=\"submit\" class=\"add_to_cart\">Add to Cart</button>\n" .
                "        </div>\n" .
                "      </div>\n" .
                "    </div>\n" .
                "  </form>\n" .
                "</li>\n";
        }

        $products_item .= '</ul>';
        echo $products_item;
    } else {
        echo "<div class='text-center py-5'><h4>No products found in this category.</h4></div>";
    }
    ?>
</div>

<?php
include('./includes/footer.php');
?>