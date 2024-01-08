<?php 
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

// For added security, check if the user is already logged in as an admin
// and redirect them if they're not. Remove this if you want the page to be accessible without login.

$title = 'Add Admin';
require_once 'template/header.php'; 
?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Add Admin</h1>
        <form action="code/admin-add.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnAddAdmin">Add Admin</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
