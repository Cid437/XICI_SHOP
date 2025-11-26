<?php
session_start();
include('../../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    header('Location: ../myorders.php');
    exit();
}

$item_id = $_GET['item_id'];
$user_id = $_SESSION['user_id'];

$sql_item = "SELECT 
                 i.description, 
                 MIN(ii.image_path) AS first_image 
             FROM item i
             LEFT JOIN item_images ii ON i.item_id = ii.item_id
             WHERE i.item_id = ?
             GROUP BY i.item_id, i.description";

$stmt_item = mysqli_prepare($conn, $sql_item);
mysqli_stmt_bind_param($stmt_item, 'i', $item_id);
mysqli_stmt_execute($stmt_item);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_item));

if (!$item) {
    include('../../includes/header.php');
    echo "<div class='container'><div class='alert alert-danger'>Item not found.</div></div>";
    include('../../includes/footer.php');
    exit();
}

$sql_review = "SELECT * FROM review WHERE item_id = ? AND user_id = ? LIMIT 1";
$stmt_review = mysqli_prepare($conn, $sql_review);
mysqli_stmt_bind_param($stmt_review, 'ii', $item_id, $user_id);
mysqli_stmt_execute($stmt_review);
$user_review = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_review));

include('../../includes/header.php');
?>

<div class="container" style="padding: 24px 0;">
    <div class="cart-view-table-back">
        <h2 class="mb-4"><?php echo $user_review ? 'Edit Your Review' : 'Write a Review'; ?> for <?php echo htmlspecialchars($item['description']); ?></h2>
        
        <img src="/XiCiTest1/item/<?php echo htmlspecialchars($item['first_image']); ?>" alt="<?php echo htmlspecialchars($item['description']); ?>" style="max-width: 150px; border-radius: 8px; margin-bottom: 20px;">

        <form action="submit_review.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            
            <?php if ($user_review) : ?>
                <input type="hidden" name="review_id" value="<?php echo $user_review['review_id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="rating" class="form-label">Your Rating (1-5 stars)</label>
                <select name="rating" id="rating" class="form-select" required>
                    <option value="" disabled <?php if (!$user_review) echo 'selected'; ?>>Select a rating</option>
                    <option value="5" <?php if ($user_review && $user_review['rating'] == 5) echo 'selected'; ?>>5 Stars (Excellent)</option>
                    <option value="4" <?php if ($user_review && $user_review['rating'] == 4) echo 'selected'; ?>>4 Stars (Good)</option>
                    <option value="3" <?php if ($user_review && $user_review['rating'] == 3) echo 'selected'; ?>>3 Stars (Average)</option>
                    <option value="2" <?php if ($user_review && $user_review['rating'] == 2) echo 'selected'; ?>>2 Stars (Fair)</option>
                    <option value="1" <?php if ($user_review && $user_review['rating'] == 1) echo 'selected'; ?>>1 Star (Poor)</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="comment" class="form-label">Your Comment</label>
                <textarea name="comment" id="comment" rows="5" class="form-control" placeholder="Tell us what you think about the product..." required><?php echo $user_review ? htmlspecialchars($user_review['comment']) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo $user_review ? 'Update Review' : 'Submit Review'; ?></button>
            <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
include('../../includes/footer.php');
?>