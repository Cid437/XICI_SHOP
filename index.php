<?php
session_start();

include('./includes/header.php');
include('./includes/config.php');

// --- NEW SEARCH LOGIC ---
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "";

// Base query
$base_sql = "SELECT i.item_id AS itemId, description, img_path, sell_price FROM item i INNER JOIN stock s USING (item_id)";

if ($keyword !== '') {
    // If there's a search keyword, add a WHERE clause
    $sql = $base_sql . " WHERE LOWER(i.description) LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    $search_param = "%" . strtolower($keyword) . "%";
    mysqli_stmt_bind_param($stmt, 's', $search_param);
    mysqli_stmt_execute($stmt);
    $results = mysqli_stmt_get_result($stmt);
} else {
    // If no keyword, get all items
    $sql = $base_sql . " ORDER BY i.item_id ASC";
    $results = mysqli_query($conn, $sql);
}
// --- END OF NEW LOGIC ---


if ($results && mysqli_num_rows($results) > 0) {
    $products_item = '<div class="products-container"><ul class="products">';

    while ($row = mysqli_fetch_assoc($results)) {
        $desc = htmlspecialchars($row['description']);
        $img = htmlspecialchars($row['img_path']);
        $price = htmlspecialchars($row['sell_price']);
        $itemId = htmlspecialchars($row['itemId']);

        // This part remains the same
        $products_item .= "<li class=\"product\">\n" .
            "  <form method=\"POST\" action=\"cart/cart_update.php\">\n" .
            "    <div class=\"product-content\">\n" .
            "      <h3><a href='product.php?id={$itemId}'>{$desc}</a></h3>\n" .
            "      <div class=\"product-thumb\"><a href='product.php?id={$itemId}'><img src=\"./item/{$img}\"></a></div>\n" .
            "      <div class=\"product-details\">\n" .
            "        <div class='product-price'>₱" . number_format($price, 2) . "</div>\n" .
            "        <fieldset>\n" .
            "          <label>\n" .
            "            <span>Quantity</span>\n" .
            "            <input type=\"number\" class='form-control form-control-sm' size=\"2\" maxlength=\"2\" name=\"item_qty\" value=\"1\" />\n" .
            "          </label>\n" .
            "        </fieldset>\n" .
            "        <input type=\"hidden\" name=\"item_id\" value=\"{$itemId}\" />\n" .
            "        <input type=\"hidden\" name=\"type\" value=\"add\" />\n" .
            "        <div align=\"center\" style='margin-top: 10px;'><button type=\"submit\" class=\"add_to_cart\">Add to Cart</button></div>\n" .
            "      </div>\n" .
            "    </div>\n" .
            "  </form>\n" .
            "</li>\n";
    }

    $products_item .= '</ul></div>';
    echo $products_item;
} else {
    // Show a message if no results were found for the search
    echo "<div class='container text-center py-5'><h4>No products found matching your search.</h4></div>";
}

include('./includes/footer.php');
?>