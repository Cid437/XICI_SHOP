<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../includes/config.php");

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must be logged in to view your profile.";
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'profile') {
    $title = trim($_POST['title'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $town = trim($_POST['town'] ?? '');
    $zipcode = trim($_POST['zipcode'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

   
    $csql_pic = "SELECT profile_picture FROM customer WHERE userId = ? LIMIT 1";
    $stmt_pic = mysqli_prepare($conn, $csql_pic);
    mysqli_stmt_bind_param($stmt_pic, 'i', $userId);
    mysqli_stmt_execute($stmt_pic);
    $current_pic_res = mysqli_stmt_get_result($stmt_pic);
    $current_pic_data = mysqli_fetch_assoc($current_pic_res);
    $newImagePath = $current_pic_data['profile_picture'] ?? null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
        if (in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
            $uploadDir = '../uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                $newImagePath = 'uploads/profile_pictures/' . $fileName;
            }
        }
    }

    $csql_check = "SELECT userId FROM customer WHERE userId = ? LIMIT 1";
    $stmt_check = mysqli_prepare($conn, $csql_check);
    mysqli_stmt_bind_param($stmt_check, 'i', $userId);
    mysqli_stmt_execute($stmt_check);
    $check_res = mysqli_stmt_get_result($stmt_check);
    $existing_customer = mysqli_fetch_assoc($check_res);

    $ok = false;
    if ($existing_customer) { // Cyrus: update pag sa existing costumer
        $usql = "UPDATE customer SET title=?, lname=?, fname=?, addressline=?, town=?, zipcode=?, phone=?, profile_picture=? WHERE userId=?";
        $ustmt = mysqli_prepare($conn, $usql);
        mysqli_stmt_bind_param($ustmt, 'ssssssssi', $title, $lname, $fname, $address, $town, $zipcode, $phone, $newImagePath, $userId);
        $ok = mysqli_stmt_execute($ustmt);
    } else { // Cyrus: Insert pag sa new customer
        $isql = "INSERT INTO customer (title, lname, fname, addressline, town, zipcode, phone, userId, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $istmt = mysqli_prepare($conn, $isql);
        mysqli_stmt_bind_param($istmt, 'sssssssis', $title, $lname, $fname, $address, $town, $zipcode, $phone, $userId, $newImagePath);
        $ok = mysqli_stmt_execute($istmt);
    }

    if ($ok) {
        $_SESSION['success'] = 'Profile saved successfully!';
    } else {
        $_SESSION['message'] = 'Error: Failed to save profile.';
    }
    header('Location: profile.php');
    exit;
}


include("../includes/header.php");

$customer = [];
$csql = "SELECT * FROM customer WHERE userId = ? LIMIT 1";
$cstmt = mysqli_prepare($conn, $csql);
mysqli_stmt_bind_param($cstmt, 'i', $userId);
mysqli_stmt_execute($cstmt);
$cres = mysqli_stmt_get_result($cstmt);
$customer = mysqli_fetch_assoc($cres) ?: [];

$addresses = [];
$asql = "SELECT * FROM addresses WHERE user_id = ?";
$astmt = mysqli_prepare($conn, $asql);
mysqli_stmt_bind_param($astmt, 'i', $userId);
mysqli_stmt_execute($astmt);
$ares = mysqli_stmt_get_result($astmt);
while ($row = mysqli_fetch_assoc($ares)) {
    $addresses[] = $row;
}
?>

<div class="container-xl px-4 mt-4 mb-5">
    <?php include("../includes/alert.php"); ?>

    <nav class="nav nav-tabs">
        <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile">Profile</a>
        <a class="nav-link" id="addresses-tab" data-bs-toggle="tab" href="#addresses">My Address</a>
        <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password">Change Password</a>
    </nav>
    <hr class="mt-0 mb-4">

    <div class="tab-content">
        <div class="tab-pane fade show active" id="profile">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="profile">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">
                                <img class="img-account-profile rounded-circle mb-2" src="<?php echo !empty($customer['profile_picture']) ? '/XiCiTest1/' . htmlspecialchars($customer['profile_picture']) : 'http://bootdey.com/img/Content/avatar/avatar1.png'; ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                <div class="mb-3">
                                    <input class="form-control" type="file" name="profile_picture" accept="image/jpeg, image/png">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">Account Details</div>
                            <div class="card-body">
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">First name</label><input class="form-control" type="text" placeholder="Enter your first name" name="fname" value="<?php echo htmlspecialchars($customer['fname'] ?? ''); ?>" required></div>
                                    <div class="col-md-6"><label class="small mb-1">Last name</label><input class="form-control" type="text" placeholder="Enter your last name" name="lname" value="<?php echo htmlspecialchars($customer['lname'] ?? ''); ?>" required></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Address</label><input class="form-control" type="text" placeholder="Enter your address" name="address" value="<?php echo htmlspecialchars($customer['addressline'] ?? ''); ?>" required></div>
                                    <div class="col-md-6"><label class="small mb-1">Town</label><input class="form-control" type="text" placeholder="Enter your town" name="town" value="<?php echo htmlspecialchars($customer['town'] ?? ''); ?>" required></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Zip code</label><input class="form-control" type="tel" placeholder="Enter zipcode" name="zipcode" value="<?php echo htmlspecialchars($customer['zipcode'] ?? ''); ?>" required></div>
                                    <div class="col-md-6"><label class="small mb-1">Title (e.g., Mr., Ms.)</label><input class="form-control" type="text" name="title" value="<?php echo htmlspecialchars($customer['title'] ?? ''); ?>"></div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6"><label class="small mb-1">Phone number</label><input class="form-control" type="tel" placeholder="Enter your phone number" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>" required></div>
                                </div>
                                <button class="btn btn-primary" type="submit">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="addresses">
            <div class="card">
                <div class="card-header">My Shipping Address</div>
                <div class="card-body">
                    <?php if (empty($addresses)) : ?>
                        <p>You have no saved shipping addresses.</p>
                    <?php else : ?>
                        <?php foreach ($addresses as $address) : ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <strong><?php echo htmlspecialchars($address['recipient_name']); ?></strong><br>
                                    <?php echo htmlspecialchars($address['address_line1']); ?><br>
                                    <?php echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['province']) . ' ' . htmlspecialchars($address['zip_code']); ?><br>
                                    Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                                    <form action="address.php" method="POST" class="float-end">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this address?');">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <hr>
                    <h5 class="mt-4">Add a New Address</h5>
                    <form action="address.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-12"><label class="small mb-1">Recipient's Full Name</label><input class="form-control" name="recipient_name" type="text" placeholder="Enter full name" required></div>
                        </div>
                        <div class="mb-3"><label class="small mb-1">Address Line 1</label><input class="form-control" name="address_line1" type="text" placeholder="Enter street address, building, etc." required></div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6"><label class="small mb-1">City</label><input class="form-control" name="city" type="text" placeholder="Enter city" required></div>
                            <div class="col-md-6"><label class="small mb-1">Province</label><input class="form-control" name="province" type="text" placeholder="Enter province" required></div>
                        </div>
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6"><label class="small mb-1">ZIP Code</label><input class="form-control" name="zip_code" type="text" placeholder="Enter ZIP code" required></div>
                            <div class="col-md-6"><label class="small mb-1">Phone Number</label><input class="form-control" name="phone_number" type="tel" placeholder="Enter phone number for delivery" required></div>
                        </div>
                        <button class="btn btn-primary" type="submit">Save Address</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="password">
            <div class="card">
                <div class="card-header">Change Password</div>
                <div class="card-body">
                    <form action="change_password.php" method="POST">
                        <div class="mb-3"><label class="small mb-1">Current Password</label><input class="form-control" name="current_password" type="password" placeholder="Enter current password" required></div>
                        <div class="mb-3"><label class="small mb-1">New Password</label><input class="form-control" name="new_password" type="password" placeholder="Enter new password" required></div>
                        <div class="mb-3"><label class="small mb-1">Confirm New Password</label><input class="form-control" name="confirm_new_password" type="password" placeholder="Confirm new password" required></div>
                        <button class="btn btn-primary" type="submit">Save Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>