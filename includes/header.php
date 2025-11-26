<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="includes/style/style.css" rel="stylesheet" type="text/css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/XiCiTest1/includes/style/style.css" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>My Shop</title>
</head>

<body>
  <header class="site-header">
    <div class="container">
      <a class="brand" href="/XiCiTest1">XiciTestshop</a>

      <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
        <i class="fa fa-bars"></i>
      </button>

      <nav class="main-nav" id="main-nav">
        <ul class="nav-list">
          <li><a href="/XiCiTest1">Home</a></li>
          <li><a href="/XiCiTest1/cart/view_cart.php">Cart</a></li>
          
          <?php // STEP 3: Only show "Items" link to admin users ?>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="/XiCiTest1/item/index.php">Items</a></li>
          <?php endif; ?>

          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <!-- Admin Menu -->
              <li class="has-dropdown">
                <button class="drop-btn">Admin Menu <i class="fa fa-caret-down"></i></button>
                <ul class="dropdown">
                  <li><a href="/XiCiTest1/item/index.php">Item Management</a></li>
                  <li><a href="/XiCiTest1/admin/orders.php">Orders</a></li>
                  <li><a href="/XiCiTest1/admin/users.php">Users</a></li>
                </ul>
              </li>
            <?php else: ?>
              <!-- Regular user: direct link to My Orders -->
              <li><a href="/XiCiTest1/user/myorders.php">My Orders</a></li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>

        <form action="/XiCiTest1/index.php" method="GET" class="search-form">
          <input type="search" name="search" placeholder="Search">
          <button type="submit"><i class="fa fa-search"></i></button>
        </form>

        <div class="auth-links">
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a class="btn btn-link" href="/XiCiTest1/user/login.php">Login</a>
          <?php else: ?>
            <div class="has-dropdown user-menu">
              <button class="drop-btn" aria-expanded="false"><i class="fa fa-user-circle"></i></button>
              <ul class="dropdown">
                <?php // STEP 2: Add "Profile" to the dropdown ?>
                <li><a href="/XiCiTest1/user/profile.php">Profile</a></li>
                <li><a href="/XiCiTest1/user/logout.php">Logout</a></li>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      </nav>
    </div>
  </header>

  <script>
    // Simple nav toggle for small screens
    (function(){
      const toggle = document.querySelector('.nav-toggle');
      const nav = document.getElementById('main-nav');
      if (!toggle || !nav) return;
      toggle.addEventListener('click', function(){
        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', String(!expanded));
        nav.classList.toggle('open');
      });
      // dropdown behavior
      document.querySelectorAll('.has-dropdown .drop-btn').forEach(btn => {
        btn.addEventListener('click', e => {
          const parent = e.target.closest('.has-dropdown');
          parent.classList.toggle('open');
        });
      });
    })();
  </script>
</body>

</html>