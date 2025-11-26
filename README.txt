A guide for the XiCiTest1 project.

Files to be mainly used
------------------------
- index.php            : Homepage. Search, list and sort products.
- product.php          : Product details page with image gallery and reviews.
- item/                : Product CRUD interface (create, store, edit, delete, list).
- user/                : Authentication and profile (register, login, profile, my orders).
- user/review/         : Review creation and editing (write_review.php, submit_review.php).
- cart/                : Shopping cart and checkout flow (cart_update.php, view_cart.php, checkout_confirm.php, process_order.php).
- admin/               : Admin dashboard, orders, and user management (orders.php, orderDetails.php, updateOrder.php, users.php, user_crud/).
- includes/config.php  : Database connection settings.
- db_sales.sql         : Database schema and initial data.
- vendor/phpmailer     : PHPMailer library used to send notification emails (Mailtrap sandbox used in code).

Xici projects requirements checklists
------------------------------------------------------------------------
MP1 — Product / Service CRUD
- Create / Read / Update / Delete
  Implementation: files in `item/`.
  What to check: use `item/create.php` to add items, `item/index.php` to list and edit, `item/delete.php` to remove.

- Upload single and multiple images
  Implementation: `item/create.php` and `item/store.php` accept file uploads; multiple images are stored in the `item_images` table and displayed in `product.php` gallery. `item/edit.php` allows adding/removing images.
  What to check: add an item with several image files, then open `product.php?id={item_id}`.

MP2 — User CRUD and profile
- User registration and login
  Implementation: `user/register.php` and `user/store.php` (passwords hashed with `sha1`), `user/login.php` handles authentication via email.

- Profile update and photo upload
  Implementation: `user/profile.php` (users) and `admin/user_crud/view_edit_profile.php` (admin editing user profile) allow saving profile details and uploading profile pictures to `uploads/profile_pictures/`.

- Admin controls (deactivate, change role)
  Implementation: `admin/users.php`, `admin/toggle_user_status.php` (activate/deactivate), and `admin/update_user_role.php` (change role)
  What to check: login as admin, go to `admin/users.php`, toggle status and change a role.

MP3 — Authentication & Authorization
- Email login, session handling
  Implementation: `user/login.php` logs users in by email; session variables store `user_id`, `email`, and `role`.

- Restrict access to admin pages
  Implementation: admin pages check `$_SESSION['role'] === 'admin'`. Non-admins are redirected to `user/login.php` with a message.

- Redirect unauthenticated users
  Implementation: pages requiring login check `$_SESSION['user_id']` and set a message then redirect to login. Examples: `user/profile.php`, `cart/checkout_confirm.php`, `user/myorders.php`.

MP4 — Reviews
- CRUD for reviews
  Implementation: `user/review/write_review.php`, `user/review/submit_review.php`, `user/review/review_order.php`.
  The code prevents duplicate reviews and lets users update their review.

- Display reviews on product page
  Implementation: `product.php` reads reviews from the `review` table for the displayed item.

- Foul-word filtering
  Implementation: `user/review/submit_review.php` uses a function with a regex to replace listed foul words with `****`.

MP5 — User interface
- Styling
  Files: `style/style.css` and Bootstrap classes used in many pages.
  What to check: open main pages (homepage, product, profile, admin) to see layout and styling.

Orders, transactions, and email notifications
---------------------------------------------
- Transactions and prepared statements
  Key files: `cart/process_order.php` (creates orders and updates stock using prepared statements and transactions), `admin/updateorder.php`.
  What to check: place an order and confirm the `orderinfo` and `orderline` rows, and that `stock` is decreased.

- Admin update order status and email notifications
  Implementation: `admin/orderDetails.php` (form to change status) and `admin/updateorder.php` (updates DB and sends email via PHPMailer). Mailtrap is referenced in code for the SMTP host and credentials.
  What to check: as admin update order status — email content includes items, subtotals and grand total; check Mailtrap inbox if configured.

Database and view
-----------------
- Schema and normalization
  File: `db_sales.sql` contains table definitions. Verify that data is split across `users`, `customer`, `item`, `item_images`, `stock`, `orderinfo`, `orderline`.

- View for order summary
  The code references a view `salesperorder` used by `admin/orders.php` and `user/myorders.php`. Inspect `db_sales.sql` to find the `CREATE VIEW` statement.

Verification checklist
------------------------------
1) Import `db_sales.sql`.
2) Start local server and open `index.php`.
3) Register a user and complete profile (upload a profile picture).
4) As admin (create an admin user if needed), open `admin/users.php` and test deactivate/activate and role change.
5) Add a product with multiple images via `item/create.php`; open product detail page and verify image gallery.
6) Add items to cart, go through checkout (use profile addresses), place order and verify `orderinfo` and `orderline` records.
7) As admin, update order status in `admin/orderDetails.php` to `Delivered` and check Mailtrap for the notification email.
8) After a delivered order, go to `user/myorders.php` → `review/review_order.php` and write/edit a review including a foul word to verify masking.