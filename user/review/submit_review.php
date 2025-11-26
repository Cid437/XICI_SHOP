<?php
session_start();
include('../../includes/config.php');

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

$item_id = $_POST['item_id'];
$user_id = $_SESSION['user_id'];
$rating = $_POST['rating'];
$comment = trim($_POST['comment']);
$review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;

if (empty($item_id) || empty($rating) || empty($comment)) {
    $_SESSION['message'] = "Please fill out all fields.";
    header("Location: review_form.php?item_id=" . $item_id);
    exit();
}

function filter_foul_words($text) {
    $foul_words = [
    'pangit', 'panget', 'paNgit', 'p@ngit',
    'bulok', 'buloK', 'buLok', 'buloq',
    'baliw', 'bal1w', 'b@liw',
    'loko', 'l0ko', 'l0k0',
    'tanga', 'tang@', 't4nga', 'bobo', 'b0b0', 'b0b0h',
    'ulol', 'ul0l', 'ul0l',
    'hayop', 'hAyop', 'hay0p',
    'gago', 'g@go', 'gaGo', 'g4g0', 'gag0',
    'bastos', 'b4stos',
    'taksil', 'takSil',
    'scammer', 'scam', 'fraud', 'con', 'cheater',
    'idiot', 'stupid', 'dumb', 'loser',
    'moron', 'jerk', 'asshole',
    'crazy', 'insane', 'nuts', 'fool',
    'sc@mmer', 'sc4mmer', 'fr@ud', 'che@ter',
    'idi0t', 'dumb0', 'l0ser', 'm0r0n',
    'jerk0', 'a$$hole', 'craZy', 'f00l'
];
    $pattern = '/(' . implode('|', $foul_words) . ')/i';
    return preg_replace($pattern, '****', $text);
}

$clean_comment = filter_foul_words($comment);

if ($review_id > 0) {
    $sql = "UPDATE review SET rating = ?, comment = ? WHERE review_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'isii', $rating, $clean_comment, $review_id, $user_id);
    $success_message = "Your review has been updated!";
} else {
    $checkSql = "SELECT review_id FROM review WHERE user_id = ? AND item_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, 'ii', $user_id, $item_id);
    mysqli_stmt_execute($checkStmt);
    if (mysqli_stmt_get_result($checkStmt)->num_rows > 0) {
        $_SESSION['message'] = "You have already reviewed this item. You can edit your existing review.";
        header("Location: ../myorders.php");
        exit();
    }

    $sql = "INSERT INTO review (item_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'iiis', $item_id, $user_id, $rating, $clean_comment);
    $success_message = "Thank you for your review!";
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = $success_message;
} else {
    $_SESSION['message'] = "Sorry, there was an error submitting your review.";
}

header("Location: ../myorders.php");
exit();
?>