<?php
session_start();
include('includes/header.php');
include('includes/config.php');

// 1. Get Item ID from URL and validate it
if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    echo "<div class='container'><p>Invalid product ID.</p></div>";
    include('includes/footer.php');
    exit();
}
$item_id = intval($_GET['id']);

// 2. Fetch Product Details
$sql_item = "SELECT * FROM item WHERE item_id = ? LIMIT 1";
$stmt_item = mysqli_prepare($conn, $sql_item);
mysqli_stmt_bind_param($stmt_item, 'i', $item_id);
mysqli_stmt_execute($stmt_item);
$result_item = mysqli_stmt_get_result($stmt_item);
$item = mysqli_fetch_assoc($result_item);

if (!$item) {
    echo "<div class='container'><p>Product not found.</p></div>";
    include('includes/footer.php');
    exit();
}

// 3. Fetch Existing Reviews for this Item (Requirement 2)
$sql_reviews = "SELECT r.*, c.fname, c.lname FROM reviews r 
                JOIN customer c ON r.customer_id = c.customer_id 
                WHERE r.item_id = ? ORDER BY r.review_date DESC";
$stmt_reviews = mysqli_prepare($conn, $sql_reviews);
mysqli_stmt_bind_param($stmt_reviews, 'i', $item_id);
mysqli_stmt_execute($stmt_reviews);
$reviews = mysqli_stmt_get_result($stmt_reviews);


// 4. LOGIC FOR REVIEW FORM: Check if the user can review this item
$can_review = false;
$user_review = null; // To hold the user's existing review if it exists
if (isset($_SESSION['user_id'])) {
    // Get the logged-in user's customer_id
    $sql_customer = "SELECT customer_id FROM customer WHERE userId = ? LIMIT 1";
    $stmt_customer = mysqli_prepare($conn, $sql_customer);
    mysqli_stmt_bind_param($stmt_customer, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_customer);
    $result_customer = mysqli_stmt_get_result($stmt_customer);
    if ($customer_row = mysqli_fetch_assoc($result_customer)) {
        $customer_id = $customer_row['customer_id'];

        // Requirement 1: Check if this customer has purchased this item
        $sql_purchased = "SELECT COUNT(*) as purchase_count FROM orderline ol
                          JOIN orderinfo oi ON ol.orderinfo_id = oi.orderinfo_id
                          WHERE ol.item_id = ? AND oi.customer_id = ?";
        $stmt_purchased = mysqli_prepare($conn, $sql_purchased);
        mysqli_stmt_bind_param($stmt_purchased, 'ii', $item_id, $customer_id);
        mysqli_stmt_execute($stmt_purchased);
        $result_purchased = mysqli_stmt_get_result($stmt_purchased);
        $purchase = mysqli_fetch_assoc($result_purchased);

        if ($purchase['purchase_count'] > 0) {
            $can_review = true;

            // Requirement 3: Check if the user has ALREADY reviewed this item
            $sql_user_review = "SELECT * FROM reviews WHERE item_id = ? AND customer_id = ? LIMIT 1";
            $stmt_user_review = mysqli_prepare($conn, $sql_user_review);
            mysqli_stmt_bind_param($stmt_user_review, 'ii', $item_id, $customer_id);
            mysqli_stmt_execute($stmt_user_review);
            $user_review = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_user_review));
        }
    }
}
?>

<div class="container" style="padding: 24px;">
    <!-- Display Product Details -->
    <div class="row">
        <div class="col-md-6">
            <img src="/XiCiTest1/item/<?php echo htmlspecialchars($item['img_path']); ?>" alt="<?php echo htmlspecialchars($item['description']); ?>" class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($item['description']); ?></h2>
            <p class="product-price">₱<?php echo number_format($item['sell_price'], 2); ?></p>
            <!-- Add to cart form can go here if you want -->
        </div>
    </div>
    
    <hr class="my-5">

    <!-- Display Reviews Section -->
    <div class="row">
        <div class="col-md-7">
            <h3>Customer Reviews</h3>
            <?php if (mysqli_num_rows($reviews) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($review['fname'] . ' ' . $review['lname']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                Rating: <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                            </h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            <small class="text-muted">Reviewed on <?php echo date('F j, Y', strtotime($review['review_date'])); ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review this product!</p>
            <?php endif; ?>
        </div>

        <!-- Review Form Section -->
        <div class="col-md-5">
            <?php if ($can_review): ?>
                <?php if ($user_review): ?>
                    <h4>Update Your Review</h4>
                <?php else: ?>
                    <h4>Write a Review</h4>
                <?php endif; ?>
                
                <form action="submit_review.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                    <?php if ($user_review): // If updating, include the review_id ?>
                        <input type="hidden" name="review_id" value="<?php echo $user_review['review_id']; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="rating" class="form-label">Your Rating</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">Select a Rating</option>
                            <option value="5" <?php echo ($user_review && $user_review['rating'] == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
                            <option value="4" <?php echo ($user_review && $user_review['rating'] == 4) ? 'selected' : ''; ?>>4 - Good</option>
                            <option value="3" <?php echo ($user_review && $user_review['rating'] == 3) ? 'selected' : ''; ?>>3 - Average</option>
                            <option value="2" <?php echo ($user_review && $user_review['rating'] == 2) ? 'selected' : ''; ?>>2 - Fair</option>
                            <option value="1" <?php echo ($user_review && $user_review['rating'] == 1) ? 'selected' : ''; ?>>1 - Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea name="comment" id="comment" rows="5" class="form-control" required><?php echo $user_review ? htmlspecialchars($user_review['comment']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="button"><?php echo $user_review ? 'Update Review' : 'Submit Review'; ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>