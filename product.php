<?php
session_start();
include('includes/header.php');
include('includes/config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container' style='padding: 20px;'><p>Invalid product ID.</p></div>";
    include('includes/footer.php');
    exit();
}
$item_id = intval($_GET['id']);

// Fetch Core Product Details
$sql_item = "SELECT i.item_id, i.description, i.long_description, i.sell_price, s.quantity 
             FROM item i LEFT JOIN stock s ON i.item_id = s.item_id
             WHERE i.item_id = ? LIMIT 1";
$stmt_item = mysqli_prepare($conn, $sql_item);
mysqli_stmt_bind_param($stmt_item, 'i', $item_id);
mysqli_stmt_execute($stmt_item);
$result_item = mysqli_stmt_get_result($stmt_item);
$item = mysqli_fetch_assoc($result_item);

if (!$item) {
    echo "<div class='container' style='padding: 20px;'><p>Product not found.</p></div>";
    include('includes/footer.php');
    exit();
}

// Fetch Product Images
$sql_images = "SELECT image_path FROM item_images WHERE item_id = ?";
$stmt_images = mysqli_prepare($conn, $sql_images);
mysqli_stmt_bind_param($stmt_images, 'i', $item_id);
mysqli_stmt_execute($stmt_images);
$result_images = mysqli_stmt_get_result($stmt_images);
$images = [];
while($row = mysqli_fetch_assoc($result_images)) {
    $images[] = $row['image_path'];
}
// Use a placeholder if no images are found
if (empty($images)) { 
    $images[] = 'images/placeholder.png'; 
}

// Fetch Product Reviews
$sql_reviews = "SELECT r.rating, r.comment, r.review_date, c.fname 
                FROM review r 
                JOIN users u ON r.user_id = u.userId
                JOIN customer c ON u.userId = c.userId 
                WHERE r.item_id = ? 
                ORDER BY r.review_date DESC";
$stmt_reviews = mysqli_prepare($conn, $sql_reviews);
mysqli_stmt_bind_param($stmt_reviews, 'i', $item_id);
mysqli_stmt_execute($stmt_reviews);
$reviews = mysqli_stmt_get_result($stmt_reviews);
?>

<div class="products-container">
    <!-- Back Button -->
    <a href="javascript:history.back()" class="button" style="margin-bottom: 20px; display: inline-block;">&laquo; Back</a>

    <div class="row">
        <!-- Image Gallery -->
        <div class="col-md-6">
            <div class="product-gallery-container">
                <div class="gallery-main">
                    <?php foreach ($images as $index => $img_path): ?>
                        <img src="/XiCiTest1/item/<?php echo htmlspecialchars($img_path); ?>" 
                             class="gallery-main-image <?php if ($index === 0) echo 'active'; ?>" 
                             alt="Product image <?php echo $index + 1; ?>">
                    <?php endforeach; ?>
                </div>
                <button class="gallery-nav prev" aria-label="Previous image">&lt;</button>
                <button class="gallery-nav next" aria-label="Next image">&gt;</button>
                <div class="gallery-counter"></div>
                <div class="gallery-thumbnails">
                     <?php foreach ($images as $index => $img_path): ?>
                        <img src="/XiCiTest1/item/<?php echo htmlspecialchars($img_path); ?>" 
                             class="gallery-thumb <?php if ($index === 0) echo 'active'; ?>" 
                             data-index="<?php echo $index; ?>"
                             alt="Thumbnail <?php echo $index + 1; ?>">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="product-title"><?php echo htmlspecialchars($item['description']); ?></h1>
            <p class="product-price-large">₱<?php echo number_format($item['sell_price'], 2); ?></p>
            <p class="product-stock-status">
                <?php if ($item['quantity'] > 0): ?>
                    <span class="badge bg-success">In Stock (<?php echo $item['quantity']; ?> available)</span>
                <?php else: ?>
                    <span class="badge bg-danger">Out of Stock</span>
                <?php endif; ?>
            </p>
            <p class="product-long-description mt-3">
                <?php echo nl2br(htmlspecialchars($item['long_description'])); ?>
            </p>

            <!-- Add to Cart Form -->
            <?php if ($item['quantity'] > 0): ?>
                <form action="/XiCiTest1/cart/cart_update.php" method="POST" class="mt-4">
                    <div class="input-group" style="max-width: 250px;">
                        <input type="number" name="item_qty" value="1" min="1" max="<?php echo $item['quantity']; ?>" class="form-control" aria-label="Quantity">
                        <button type="submit" class="button">Add to Cart</button>
                    </div>
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <input type="hidden" name="type" value="add">
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <hr class="my-5">

    <!-- Customer Review Section -->
    <div class="row">
        <!-- Display reviews -->
        <div class="col-md-7">
            <h3>Customer Reviews</h3>
            <?php if ($reviews && mysqli_num_rows($reviews) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($review['fname']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                Rating: <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                            </h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            <small class="text-muted">Reviewed on <?php echo date('F j, Y', strtotime($review['review_date'])); ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet for this product.</p>
            <?php endif; ?>
        </div>

        <!-- Call to action to write a review -->
        <div class="col-md-5">
            <div class="card bg-light">
                <div class="card-body">
                    <h4 class="card-title">Want to review this item?</h4>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <p class="card-text">You can review items you've purchased once the order is delivered.</p>
                        <a href="/XiCiTest1/user/myorders.php" class="button">Go to My Orders</a>
                    <?php else: ?>
                        <p class="card-text">Please log in to see if you can write a review for this item.</p>
                        <a href="/XiCiTest1/user/login.php" class="button">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Image Gallery -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mainImages = document.querySelectorAll('.gallery-main-image');
    if (mainImages.length === 0) return;

    const thumbnails = document.querySelectorAll('.gallery-thumb');
    const prevButton = document.querySelector('.gallery-nav.prev');
    const nextButton = document.querySelector('.gallery-nav.next');
    const counter = document.querySelector('.gallery-counter');
    let currentIndex = 0;
    const totalImages = mainImages.length;

    function showImage(index) {
        if (index >= totalImages) index = 0;
        if (index < 0) index = totalImages - 1;

        mainImages.forEach(img => img.classList.remove('active'));
        mainImages[index].classList.add('active');

        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        if(thumbnails[index]) thumbnails[index].classList.add('active');
        
        if(counter) counter.textContent = `${index + 1} / ${totalImages}`;
        currentIndex = index;
    }

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => showImage(parseInt(thumb.dataset.index)));
    });

    if(nextButton) nextButton.addEventListener('click', () => showImage(currentIndex + 1));
    if(prevButton) prevButton.addEventListener('click', () => showImage(currentIndex - 1));

    showImage(0);
});
</script>

<?php include('includes/footer.php'); ?>