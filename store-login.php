<?php require_once 'code/session-start.php'; ?>

<?php
// Redirect to store dashboard if logged in as either admin, staff, or rider
if (isset($_SESSION['adminid']) || isset($_SESSION['staffid']) || isset($_SESSION['riderid'])) {    
    header('Location: store-dashboard.php');
    exit(); // Ensure no further code is executed before redirect happens
}
?>

<?php $title = 'Store Login'; ?>

<?php require_once 'template/header.php'; ?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Store Login</h1>
        <form action="code/store-login.php" method="POST">
            <!-- Hidden field for redirect -->
            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect']); ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="role" id="admin" value="admin" required>
                    <label class="form-check-label" for="admin">Admin</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="role" id="staff" value="staff">
                    <label class="form-check-label" for="staff">Staff</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="role" id="rider" value="rider">
                    <label class="form-check-label" for="rider">Rider</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnLogin">Login</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
