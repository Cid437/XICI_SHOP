php
<?php
session_start();
include("../includes/config.php");
include("../includes/header.php");

// CORRECTED: Use 'userId' and select the 'role' column
$sql = "SELECT userId, email, role, is_active FROM users ORDER BY userId ASC";
$result = mysqli_query($conn, $sql);
$itemCount = ($result) ? mysqli_num_rows($result) : 0;

?>

<div class="container" style="padding:24px;">
    <h2 class="mb-4">User Management</h2>
    <?php include("../includes/alert.php"); // To display success/error messages ?>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="width: 35%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $itemCount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = htmlspecialchars($row['userId']);
                        $email = htmlspecialchars($row['email']);
                        $role = htmlspecialchars($row['role']);
                        $isActive = $row['is_active'] == 1;

                        // --- Status Display ---
                        $statusLabel = $isActive ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';

                        // --- Action Links/Forms ---
                        $toggleActionText = $isActive ? 'Deactivate' : 'Activate';
                        $toggleActionClass = $isActive ? 'btn btn-warning btn-sm' : 'btn btn-success btn-sm';
                        $toggleActionLink = "<a href='toggle_user_status.php?userId={$userId}' class='{$toggleActionClass}'>{$toggleActionText}</a>";
                        
                        echo "<tr>";
                        echo "<td>{$userId}</td>";
                        echo "<td>{$email}</td>";
                        echo "<td>" . ucfirst($role) . "</td>"; // Display current role
                        echo "<td>{$statusLabel}</td>";
                        
                        // --- Actions Cell ---
                        echo "<td>";
                        
                        // Form for updating the role
                        echo "<form action='update_user_role.php' method='POST' class='d-inline-flex align-items-center'>";
                        echo "<input type='hidden' name='userId' value='{$userId}'>";
                        echo "<select name='role' class='form-select form-select-sm me-2' style='width: auto;'>";
                        echo "<option value='user'" . ($role === 'user' ? ' selected' : '') . ">User</option>";
                        echo "<option value='admin'" . ($role === 'admin' ? ' selected' : '') . ">Admin</option>";
                        echo "</select>";
                        echo "<button type='submit' class='btn btn-primary btn-sm'>Update Role</button>";
                        echo "</form>";

                        // Link for deactivating/activating
                        echo "&nbsp;&nbsp;" . $toggleActionLink;

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