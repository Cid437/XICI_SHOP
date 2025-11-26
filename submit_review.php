<?php
session_start();
include('includes/config.php');

// Security: Ensure user is logged in and form was submitted via POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// 1. Get and Validate Form Data
$item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0; // For updates

if ($item_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    $_SESSION['message'] = 'Please fill out all fields correctly.';
    header('Location: product.php?id=' . $item_id);
    exit();
}

// 2. Requirement 4: Filter Foul Words using Regex
function filter_foul_words($text) {
    // Add more foul words to this array as needed
    $foul_words = ['badword', 'curse', 'offensive']; 
    
    // Create a regex pattern: \b(word1|word2|...)\b/i
    // \b ensures we match whole words only. /i makes it case-insensitive.
    $pattern = '/\b(' . implode('|', $foul_words) . ')\b/i';
    
    // Replace matched words with asterisks
    return preg_replace($pattern, '****', $text);
}

$clean_comment = filter_foul_words($comment);

// 3. Get the logged-in user's customer_id
$sql_customer = "SELECT customer_id FROM customer WHERE userId = ? LIMIT 1";
$stmt_customer = mysqli_prepare($conn, $sql_customer);
mysqli_stmt_bind_param($stmt_customer, 'i', $_SESSION['user_id']);
mysqli_stmt_execute($stmt_customer);
$result_customer = mysqli_stmt_get_result($stmt_customer);
$customer_row = mysqli_fetch_assoc($result_customer);
$customer_id = $customer_row['customer_id'];

if (empty($customer_id)) {
    $_SESSION['message'] = 'Could not find your customer profile.';
    header('Location: product.php?id=' . $item_id);
    exit();
}


// 4. Decide whether to INSERT (new review) or UPDATE (existing review)
if ($review_id > 0) {
    // UPDATE existing review (Requirement 3)
    // Extra security: Make sure the review being updated belongs to the logged-in user
    $sql = "UPDATE reviews SET rating = ?, comment = ? WHERE review_id = ? AND customer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isii', $rating, $clean_comment, $review_id, $customer_id);
    
} else {
    // INSERT new review
    $sql = "INSERT INTO reviews (item_id, customer_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiis', $item_id, $customer_id, $rating, $clean_comment);
}

// 5. Execute and Redirect
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = 'Your review has been submitted successfully!';
} else {
    $_SESSION['message'] = 'There was an error submitting your review.';
}

header('Location: product.php?id=' . $item_id);
exit();
?>