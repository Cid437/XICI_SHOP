<?php
session_start();
include('./includes/header.php');
include('./includes/alert.php');
include('./includes/config.php');

$search_term = trim($_GET['search'] ?? '');
$sort_order = $_GET['sort'] ?? 'default';

$where_clause = "";
if (!empty($search_term)) {
    $where_clause = " WHERE i.description LIKE ?";
}

$order_by_clause = " ORDER BY i.item_id ASC";
switch ($sort_order) {
    case 'high_low':
        $order_by_clause = " ORDER BY i.sell_price DESC";
        break;
    case 'low_high':
        $order_by_clause = " ORDER BY i.sell_price ASC";
        break;
}

$products_per_page = 8;
$current_page = (int)($_GET['page'] ?? 1);
if ($current_page < 1) {
    $current_page = 1;
}

$sql_count = "SELECT COUNT(DISTINCT i.item_id) AS total FROM item i" . $where_clause;
$stmt_count = mysqli_prepare($conn, $sql_count);
if (!empty($search_term)) {
    $search_param = "%" . $search_term . "%";
    mysqli_stmt_bind_param($stmt_count, 's', $search_param);
}
mysqli_stmt_execute($stmt_count);
$total_products = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))['total'];
$total_pages = ceil($total_products / $products_per_page);
$offset = ($current_page - 1) * $products_per_page;

$sql = "SELECT
            i.item_id AS itemId, i.description, i.sell_price, MIN(ii.image_path) AS first_image
        FROM item i
        LEFT JOIN item_images ii ON i.item_id = ii.item_id
        {$where_clause}
        GROUP BY i.item_id
        {$order_by_clause}
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($search_term)) {
    $search_param = "%" . $search_term . "%";
    mysqli_stmt_bind_param($stmt, 'sii', $search_param, $products_per_page, $offset);
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $products_per_page, $offset);
}

mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);
?>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">DISCOVER YOUR NEXT FAVORITE THING</h1>
        <p class="hero-subtitle">Explore our curated collections and exclusive offers</p>
        <a href="#products" class="hero-button">Shop Now</a>
    </div>
</section>

<div class="products-container" id="products">
    <div class="controls-container">
        <div class="filter-controls">
            <span>Sort by price:</span>
            <a href="?sort=low_high&search=<?php echo urlencode($search_term); ?>#products" class="button-filter <?php if ($sort_order === 'low_high') echo 'active'; ?>">Lowest to Highest</a>
            <a href="?sort=high_low&search=<?php echo urlencode($search_term); ?>#products" class="button-filter <?php if ($sort_order === 'high_low') echo 'active'; ?>">Highest to Lowest</a>
        </div>
        <div class="search-controls">
            <form action="index.php#products" method="GET" class="inline-search-form">
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_order); ?>">
                <input type="search" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <?php
    if ($results && mysqli_num_rows($results) > 0) {
        $products_item = '<h2 class="section-title">Featured Products</h2><ul class="products">';

        while ($row = mysqli_fetch_assoc($results)) {
            $desc = htmlspecialchars($row['description']);
            $img_filename = htmlspecialchars($row['first_image'] ?? 'images/placeholder.png');
            $price = htmlspecialchars($row['sell_price']);
            $itemId = htmlspecialchars($row['itemId']);
            $image_path = "item/" . $img_filename;

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

        $query_params = "sort=" . urlencode($sort_order) . "&search=" . urlencode($search_term);
        $products_item .= '<div class="pagination">';
        if ($current_page > 1) {
            $prev_page = $current_page - 1;
            $products_item .= "<a href='index.php?page={$prev_page}&{$query_params}#products' class='button'>&laquo; Previous</a>";
        }
        if ($current_page < $total_pages) {
            $next_page = $current_page + 1;
            $products_item .= "<a href='index.php?page={$next_page}&{$query_params}#products' class='button'>Next &raquo;</a>";
        }
        $products_item .= '</div>';
        echo $products_item;
    } else {
        echo "<h2 class='section-title'>Products</h2><div class='text-center py-5'><h4>No products found matching your criteria.</h4></div>";
    }
    ?>
</div>

<?php
include('./includes/footer.php');
?>