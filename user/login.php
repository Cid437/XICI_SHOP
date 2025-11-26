<?php
session_start();
include("../includes/config.php");

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = sha1(trim($_POST['password']));

    $sql = "SELECT userId, email, role, is_active FROM users WHERE email=? AND password=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $pass);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $email, $role, $is_active);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_fetch($stmt);

        if ($is_active == 1) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['success'] = 'You have successfully logged in!';
            header("Location: ../index.php");
            exit();
        } else {
            $_SESSION['message'] = 'Your account has been deactivated. Please contact an administrator.';
            header("Location: login.php");
            exit(); //Donn: nilagay kolang d2 yung nawawalang exit()
        }
    } else {
        $_SESSION['message'] = 'Wrong email or password';
        header("Location: login.php");
        exit();
    }
}

include("../includes/header.php");
?>

<div class="row col-md-8 mx-auto ">
    <?php include("../includes/alert.php"); ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <div class="form-outline mb-4">
            <label class="form-label" for="form2Example1">Email address</label>
            <input type="email" id="form2Example1" class="form-control" name="email" required />
        </div>

        <div class="form-outline mb-4">
            <label class="form-label" for="form2Example2">Password</label>
            <input type="password" id="form2Example2" class="form-control" name="password" required />
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4" name="submit">Sign in</button>

        <div class="text-center">
            <p>Not a member? <a href="register.php">Register</a></p>
        </div>
    </form>
</div>

<?php
include("../includes/footer.php");
?>