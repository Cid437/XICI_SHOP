<?php
session_start();
include("../../includes/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

$user_id_to_edit = $_GET['id'] ?? null;
if (!$user_id_to_edit) {
    header("Location: ../users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "UPDATE users SET email = ?";
    $params = [$email];
    $types = "s";

    if (!empty($password)) {
        $hashed_password = sha1($password);
        $sql .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }

    $sql .= " WHERE userId = ?";
    $params[] = $user_id_to_edit;
    $types .= "i";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'User updated successfully!';
        } else {
            $_SESSION['message'] = 'Error updating user.';
        }
        mysqli_stmt_close($stmt);
    }
    header("Location: ../users.php");
    exit();
}

$sql_fetch = "SELECT email FROM users WHERE userId = ?";
$stmt_fetch = mysqli_prepare($conn, $sql_fetch);
mysqli_stmt_bind_param($stmt_fetch, "i", $user_id_to_edit);
mysqli_stmt_execute($stmt_fetch);
$result = mysqli_stmt_get_result($stmt_fetch);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['message'] = "User not found.";
    header("Location: ../users.php");
    exit();
}

include("../../includes/header.php");
?>

<div class="container" style="padding:24px;">
    <h2>Edit User (ID: <?php echo htmlspecialchars($user_id_to_edit); ?>)</h2>

    <div class="card">
        <div class="card-body">
            <form action="edit_user.php?id=<?php echo htmlspecialchars($user_id_to_edit); ?>" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="../users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>