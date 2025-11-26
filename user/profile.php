<?php
// Profile page - view and edit customer profile
include("../includes/header.php");
include("../includes/config.php");

// Fetch existing customer for logged-in user
$customer = [];
$userId = $_SESSION['user_id'] ?? 0;
if ($userId) {
    $csql = "SELECT * FROM customer WHERE userId = ? LIMIT 1";
    if ($cstmt = mysqli_prepare($conn, $csql)) {
        mysqli_stmt_bind_param($cstmt, 'i', $userId);
        mysqli_stmt_execute($cstmt);
        $cres = mysqli_stmt_get_result($cstmt);
        if ($cres) {
            $customer = mysqli_fetch_assoc($cres) ?: [];
        }
        mysqli_stmt_close($cstmt);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect text inputs
    $title = trim($_POST['title'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $town = trim($_POST['town'] ?? '');
    $zipcode = trim($_POST['zipcode'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Handle file upload robustly
    $newImagePath = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
        if (in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
            
            $uploadDir = '../uploads/profile_pictures/';
            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Create a unique filename to prevent overwrites
            $fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
            $targetPath = $uploadDir . $fileName;

            // Move the file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
                // Store the web-accessible path
                $newImagePath = 'uploads/profile_pictures/' . $fileName;
            }
        }
    }

    // Perform UPDATE or INSERT using prepared statements
    $ok = false;
    if (!empty($customer)) {
        // Existing customer: UPDATE
        if ($newImagePath !== null) {
            $usql = "UPDATE customer SET title=?, lname=?, fname=?, addressline=?, town=?, zipcode=?, phone=?, profile_picture=? WHERE userId=?";
            if ($ustmt = mysqli_prepare($conn, $usql)) {
                mysqli_stmt_bind_param($ustmt, 'ssssssssi', $title, $lname, $fname, $address, $town, $zipcode, $phone, $newImagePath, $userId);
                $ok = mysqli_stmt_execute($ustmt);
                mysqli_stmt_close($ustmt);
            }
        } else {
            $usql = "UPDATE customer SET title=?, lname=?, fname=?, addressline=?, town=?, zipcode=?, phone=? WHERE userId=?";
            if ($ustmt = mysqli_prepare($conn, $usql)) {
                mysqli_stmt_bind_param($ustmt, 'sssssssi', $title, $lname, $fname, $address, $town, $zipcode, $phone, $userId);
                $ok = mysqli_stmt_execute($ustmt);
                mysqli_stmt_close($ustmt);
            }
        }
    } else {
        // New customer: INSERT
        $isql = "INSERT INTO customer (title, lname, fname, addressline, town, zipcode, phone, userId, profile_picture) VALUES (?,?,?,?,?,?,?,?,?)";
        if ($istmt = mysqli_prepare($conn, $isql)) {
            mysqli_stmt_bind_param($istmt, 'sssssssis', $title, $lname, $fname, $address, $town, $zipcode, $phone, $userId, $newImagePath);
            $ok = mysqli_stmt_execute($istmt);
            mysqli_stmt_close($istmt);
        }
    }

    if ($ok) {
        $_SESSION['success'] = 'Profile saved successfully!';
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['message'] = 'Error: Failed to save profile.';
        header('Location: profile.php');
        exit;
    }
}

?>

<div class="container-xl px-4 mt-4">
    <?php include("../includes/alert.php"); ?>
    <!-- Account page navigation-->
    <nav class="nav nav-borders">
        <a class="nav-link active ms-0" href="#">Profile</a>
    </nav>
    <hr class="mt-0 mb-4">

    <!-- THE FORM TAG NOW WRAPS EVERYTHING -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile picture card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Profile Picture</div>
                    <div class="card-body text-center">
                        <!-- Profile picture image-->
                        <img class="img-account-profile rounded-circle mb-2" src="<?php echo !empty($customer['profile_picture']) ? '/XiCiTest1/' . htmlspecialchars($customer['profile_picture']) : 'http://bootdey.com/img/Content/avatar/avatar1.png'; ?>" alt="Profile Picture">
                        <!-- Profile picture help block-->
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <!-- Profile picture upload input (NOW INSIDE THE FORM)-->
                        <div class="mb-3">
                            <input class="form-control" type="file" name="profile_picture" accept="image/jpeg, image/png">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <!-- Account details card-->
                <div class="card mb-4">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputFirstName">First name</label>
                                    <input class="form-control" id="inputFirstName" type="text" placeholder="Enter your first name" name="fname" value="<?php echo htmlspecialchars($customer['fname'] ?? ''); ?>">
                                </div>
                                <!-- Form Group (last name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputLastName">Last name</label>
                                    <input class="form-control" id="inputLastName" type="text" placeholder="Enter your last name" name="lname" value="<?php echo htmlspecialchars($customer['lname'] ?? ''); ?>">
                                </div>
                            </div>
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (address)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="address">Address</label>
                                    <input class="form-control" id="address" type="text" placeholder="Enter your address" name="address" value="<?php echo htmlspecialchars($customer['addressline'] ?? ''); ?>">
                                </div>
                                <!-- Form Group (town)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="town">Town</label>
                                    <input class="form-control" id="town" type="text" placeholder="Enter your town" name="town" value="<?php echo htmlspecialchars($customer['town'] ?? ''); ?>">
                                </div>
                            </div>
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (zip code)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="zip">Zip code</label>
                                    <input class="form-control" id="zip" type="tel" placeholder="Enter zipcode" name="zipcode" value="<?php echo htmlspecialchars($customer['zipcode'] ?? ''); ?>">
                                </div>
                                <!-- Form Group (title)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="title">Title</label>
                                    <input class="form-control" id="title" type="text" name="title" value="<?php echo htmlspecialchars($customer['title'] ?? ''); ?>">
                                </div>
                            </div>
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (phone number)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputPhone">Phone number</label>
                                    <input class="form-control" id="inputPhone" type="tel" placeholder="Enter your phone number" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <!-- Save changes button-->
                            <button class="btn btn-primary" type="submit">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form> <!-- THE FORM TAG NOW ENDS HERE -->
</div>

<?php include("../includes/footer.php"); ?>