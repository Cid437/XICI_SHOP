<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You must be an admin to access this page.";
    header("Location: ../user/login.php");
    exit();
}

include("../includes/config.php");
include("../includes/header.php");

$sql = "SELECT userId, email, role, is_active FROM users ORDER BY userId ASC";
$result = mysqli_query($conn, $sql);
$itemCount = ($result) ? mysqli_num_rows($result) : 0;
?>

<div class="container" style="padding:24px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>User Management</h2>
        <a href="user_crud/create_user.php" class="btn btn-primary">Add New User</a>
    </div>

    <?php include("../includes/alert.php"); ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="min-width: 400px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $itemCount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = htmlspecialchars($row['userId']);
                        $email = htmlspecialchars($row['email']);
                        $role = htmlspecialchars($row['role']);
                        $isActive = ($row['is_active'] == 1);

                        $statusLabel = $isActive ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                        $toggleActionText = $isActive ? 'Deactivate' : 'Activate';
                        $toggleActionClass = $isActive ? 'btn btn-warning btn-sm' : 'btn btn-info btn-sm';
                        // Cyrus: ayos na donn
                        $toggleActionLink = "<a href='toggle_user_status.php?userId={$userId}' class='{$toggleActionClass} ms-2'>{$toggleActionText}</a>";

                        echo "<tr>";
                        echo "<td>{$userId}</td>";
                        echo "<td><a href='user_crud/view_edit_profile.php?id={$userId}' title='Edit user profile'>{$email}</a></td>";
                        echo "<td>" . ucfirst($role) . "</td>";
                        echo "<td>{$statusLabel}</td>";
                        echo "<td>";
                        echo "<form action='update_user_role.php' method='POST' class='d-inline-flex align-items-center'>";
                        echo "<input type='hidden' name='userId' value='{$userId}'>";
                        echo "<select name='role' class='form-select form-select-sm me-2' style='width: auto;'>";
                        echo "<option value='user'" . ($role === 'user' ? ' selected' : '') . ">User</option>";
                        echo "<option value='admin'" . ($role === 'admin' ? ' selected' : '') . ">Admin</option>";
                        echo "</select>";
                        echo "<button type='submit' class='btn btn-primary btn-sm'>Update Role</button>";
                        echo "</form>";
                        echo "<a href='user_crud/edit_user.php?id={$userId}' class='btn btn-secondary btn-sm ms-2' title='Edit email/password'>Edit Login</a>";
                        echo $toggleActionLink;
                        echo "<a href='user_crud/delete_user.php?id={$userId}' class='btn btn-danger btn-sm ms-2' onclick=\"return confirm('Are you sure you want to permanently delete this user?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No users found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>