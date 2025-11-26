<?php
session_start();
include("../../includes/config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You do not have permission to access this page.";
    header("Location: ../../user/login.php");
    exit();
}

$user_id_to_edit = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$user_id_to_edit) {
    $_SESSION['message'] = "Invalid user specified for editing.";
    header("Location: ../users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $lname = trim($_POST['lname']);
        $fname = trim($_POST['fname']);
        $title = trim($_POST['title']);
        $address = trim($_POST['address']);
        $town = trim($_POST['town']);
        $zipcode = trim($_POST['zipcode']);
        $phone = trim($_POST['phone']);

        $check_sql = "SELECT customer_id, profile_picture FROM customer WHERE userId = ?";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt_check, 'i', $user_id_to_edit);
        mysqli_stmt_execute($stmt_check);
        $existing_customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check));
        $newImagePath = $existing_customer['profile_picture'] ?? null;

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $uploadDir = '../../uploads/profile_pictures/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                $newImagePath = 'uploads/profile_pictures/' . $fileName;
            }
        }

        if ($existing_customer) {
            $sql = "UPDATE customer SET title=?, lname=?, fname=?, addressline=?, town=?, zipcode=?, phone=?, profile_picture=? WHERE userId = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssssssssi', $title, $lname, $fname, $address, $town, $zipcode, $phone, $newImagePath, $user_id_to_edit);
        } else {
            $sql = "INSERT INTO customer (title, lname, fname, addressline, town, zipcode, phone, userId, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssssssis', $title, $lname, $fname, $address, $town, $zipcode, $phone, $user_id_to_edit, $newImagePath);
        }
        $result = mysqli_stmt_execute($stmt);
        $_SESSION[$result ? 'success' : 'message'] = $result ? 'Profile updated successfully!' : 'Error updating profile.';
    } elseif ($action === 'add_address') {
        $sql = "INSERT INTO addresses (user_id, recipient_name, address_line1, city, province, zip_code, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'issssss', $user_id_to_edit, $_POST['recipient_name'], $_POST['address_line1'], $_POST['city'], $_POST['province'], $_POST['zip_code'], $_POST['phone_number']);
        $result = mysqli_stmt_execute($stmt);
        $_SESSION[$result ? 'success' : 'message'] = $result ? 'New address added!' : 'Error adding address.';
    } elseif ($action === 'delete_address') {
        $address_id_to_delete = filter_input(INPUT_POST, 'address_id', FILTER_VALIDATE_INT);
        $sql = "DELETE FROM addresses WHERE address_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $address_id_to_delete, $user_id_to_edit);
        $result = mysqli_stmt_execute($stmt);
        $_SESSION[$result ? 'success' : 'message'] = $result ? 'Address deleted!' : 'Error deleting address.';
    } elseif ($action === 'change_password') {
        if (!empty($_POST['new_password']) && $_POST['new_password'] === $_POST['confirm_password']) {
            $hashed_password = sha1($_POST['new_password']);
            $sql = "UPDATE users SET password = ? WHERE userId = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'si', $hashed_password, $user_id_to_edit);
            $result = mysqli_stmt_execute($stmt);
            $_SESSION[$result ? 'success' : 'message'] = $result ? 'Password updated successfully!' : 'Error updating password.';
        } else {
            $_SESSION['message'] = "Passwords cannot be empty and must match.";
        }
    }

    header("Location: view_edit_profile.php?id=" . $user_id_to_edit);
    exit();
}

$sql_fetch_user = "SELECT u.email, c.* FROM users u LEFT JOIN customer c ON u.userId = c.userId WHERE u.userId = ?";
$stmt_fetch_user = mysqli_prepare($conn, $sql_fetch_user);
mysqli_stmt_bind_param($stmt_fetch_user, 'i', $user_id_to_edit);
mysqli_stmt_execute($stmt_fetch_user);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_fetch_user));

if (!$user_data) {
    header("Location: ../users.php");
    exit();
}

$addresses = [];
$sql_fetch_addr = "SELECT * FROM addresses WHERE user_id = ?";
$stmt_fetch_addr = mysqli_prepare($conn, $sql_fetch_addr);
mysqli_stmt_bind_param($stmt_fetch_addr, 'i', $user_id_to_edit);
mysqli_stmt_execute($stmt_fetch_addr);
$result_addr = mysqli_stmt_get_result($stmt_fetch_addr);
while ($row = mysqli_fetch_assoc($result_addr)) {
    $addresses[] = $row;
}

$profile_pic = !empty($user_data['profile_picture']) ? '/XiCiTest1/' . htmlspecialchars($user_data['profile_picture']) : 'http://bootdey.com/img/Content/avatar/avatar1.png';

include("../../includes/header.php");
?>

<div class="container-xl px-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Editing Profile: <span class="text-primary"><?php echo htmlspecialchars($user_data['email']); ?></span></h3>
        <a href="../users.php" class="btn btn-secondary">Back to User List</a>
    </div>

    <?php include("../../includes/alert.php"); ?>

    <nav class="nav nav-tabs">
        <a class="nav-link active" data-bs-toggle="tab" href="#profile">Profile</a>
        <a class="nav-link" data-bs-toggle="tab" href="#addresses">Shipping Addresses</a>
        <a class="nav-link" data-bs-toggle="tab" href="#password">Change Password</a>
    </nav>
    <hr class="mt-0 mb-4">

    <div class="tab-content">
        <div class="tab-pane fade show active" id="profile">
            <form action="view_edit_profile.php?id=<?php echo $user_id_to_edit; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">
                                <img class="img-account-profile rounded-circle mb-2" src="<?php echo $profile_pic; ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                <input class="form-control" type="file" name="profile_picture" accept="image/jpeg, image/png">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header">Account Details (Primary / Billing)</div>
                            <div class="card-body">
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">First name</label><input class="form-control" type="text" name="fname" value="<?php echo htmlspecialchars($user_data['fname'] ?? ''); ?>"></div>
                                    <div class="col-md-6"><label class="small mb-1">Last name</label><input class="form-control" type="text" name="lname" value="<?php echo htmlspecialchars($user_data['lname'] ?? ''); ?>"></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Address</label><input class="form-control" type="text" name="address" value="<?php echo htmlspecialchars($user_data['addressline'] ?? ''); ?>"></div>
                                    <div class="col-md-6"><label class="small mb-1">Town</label><input class="form-control" type="text" name="town" value="<?php echo htmlspecialchars($user_data['town'] ?? ''); ?>"></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Zip code</label><input class="form-control" type="text" name="zipcode" value="<?php echo htmlspecialchars($user_data['zipcode'] ?? ''); ?>"></div>
                                    <div class="col-md-6"><label class="small mb-1">Title</label><input class="form-control" type="text" name="title" value="<?php echo htmlspecialchars($user_data['title'] ?? ''); ?>"></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Phone number</label><input class="form-control" type="tel" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"></div>
                                </div>
                                <button class="btn btn-primary" type="submit">Save Profile Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="addresses">
            <div class="card">
                <div class="card-header">Manage Shipping Addresses</div>
                <div class="card-body">
                    <?php if (empty($addresses)) : ?>
                        <p>This user has no saved shipping addresses.</p>
                    <?php else : ?>
                        <?php foreach ($addresses as $address) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong><?php echo htmlspecialchars($address['recipient_name']); ?></strong><br>
                                    <?php echo htmlspecialchars($address['address_line1']); ?><br>
                                    <?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['province']) . ' ' . htmlspecialchars($address['zip_code']); ?><br>
                                    Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                                    <form action="view_edit_profile.php?id=<?php echo $user_id_to_edit; ?>" method="POST" class="float-end">
                                        <input type="hidden" name="action" value="delete_address">
                                        <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this address?');">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <hr>
                    <h5 class="mt-4">Add a New Shipping Address</h5>
                    <form action="view_edit_profile.php?id=<?php echo $user_id_to_edit; ?>" method="POST">
                        <input type="hidden" name="action" value="add_address">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-12"><label class="small mb-1">Recipient's Full Name</label><input class="form-control" name="recipient_name" type="text" required></div>
                        </div>
                        <div class="mb-3"><label class="small mb-1">Address Line 1</label><input class="form-control" name="address_line1" type="text" required></div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6"><label class="small mb-1">City</label><input class="form-control" name="city" type="text" required></div>
                            <div class="col-md-6"><label class="small mb-1">Province</label><input class="form-control" name="province" type="text" required></div>
                        </div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6"><label class="small mb-1">ZIP Code</label><input class="form-control" name="zip_code" type="text" required></div>
                            <div class="col-md-6"><label class="small mb-1">Phone Number</label><input class="form-control" name="phone_number" type="tel" required></div>
                        </div>
                        <button class="btn btn-primary" type="submit">Save New Address</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password">
            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form action="view_edit_profile.php?id=<?php echo $user_id_to_edit; ?>" method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="mb-3"><label class="small mb-1">New Password</label><input class="form-control" name="new_password" type="password" required></div>
                        <div class="mb-3"><label class="small mb-1">Confirm New Password</label><input class="form-control" name="confirm_password" type="password" required></div>
                        <button class="btn btn-primary" type="submit">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>