<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XiCi Instruments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/includes/style/style.css" type="text/css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="/">
                <img src="/images/xici-logo.png" alt="XiCi Instruments Logo">
                <span>XiCi Instruments</span>
            </a>

            <button class="nav-toggle" aria-label="Toggle navigation"><i class="fa fa-bars"></i></button>

            <nav class="main-nav" id="main-nav">
                <ul class="nav-list">
                    <li><a href="/"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                    <li class="has-dropdown">
                        <button class="drop-btn"><i class="fa-solid fa-guitar"></i><span>Products</span><i class="fa fa-caret-down caret"></i></button>
                        <ul class="dropdown">
                            <li><a href="/category.php?cat=guitars">Guitars</a></li>
                            <li><a href="/category.php?cat=keyboards">Keyboards</a></li>
                            <li><a href="/category.php?cat=drums">Drums & Percussion</a></li>
                            <li><a href="/category.php?cat=studio">Studio Equipment</a></li>
                        </ul>
                    </li>
                    <li><a href="/cart/view_cart.php"><i class="fa-solid fa-cart-shopping"></i><span>Cart</span></a></li>
                    <?php if (isset($_SESSION['user_id'])) : ?>
                        <li><a href="/user/myorders.php"><i class="fa-solid fa-box-archive"></i><span>My Orders</span></a></li>
                    <?php endif; ?>
                </ul>

                <div class="nav-right">
                    <a href="/about.php" class="nav-link-right"><i class="fa-solid fa-info-circle"></i><span>About Us</span></a>
                    <div class="auth-links">
                        <?php if (!isset($_SESSION['user_id'])) : ?>
                            <a href="/user/login.php" class="nav-link-right"><i class="fa-solid fa-right-to-bracket"></i><span>Login</span></a>
                        <?php else : ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                                <div class="has-dropdown user-menu">
                                    <button class="drop-btn"><span>Admin Menu</span><i class="fa fa-user-shield" style="margin-left: 8px;"></i></button>
                                    <ul class="dropdown">
                                        <li><a href="/item/index.php">Item Management</a></li>
                                        <li><a href="/admin/orders.php">Order Management</a></li>
                                        <li><a href="/admin/users.php">User Management</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a href="/user/logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a></li>
                                    </ul>
                                </div>
                            <?php else : ?>
                                <div class="has-dropdown user-menu">
                                    <button class="drop-btn"><i class="fa fa-user-circle"></i></button>
                                    <ul class="dropdown">
                                        <li><a href="/user/profile.php">Profile</a></li>
                                        <li><a href="/user/logout.php" onclick="return confirm('Are you sure you want to log out?');">Logout</a></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <script>
        (function() {
            const toggle = document.querySelector('.nav-toggle');
            const nav = document.getElementById('main-nav');
            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    nav.classList.toggle('open');
                });
            }

            document.addEventListener('click', function(e) {
                document.querySelectorAll('.has-dropdown.open').forEach(function(dropdown) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('open');
                    }
                });
            });

            document.querySelectorAll('.has-dropdown .drop-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    const parent = e.target.closest('.has-dropdown');
                    document.querySelectorAll('.has-dropdown.open').forEach(function(dropdown) {
                        if (dropdown !== parent) {
                            dropdown.classList.remove('open');
                        }
                    });
                    parent.classList.toggle('open');
                    e.stopPropagation();
                });
            });
        })();
    </script>
</body>
</html>