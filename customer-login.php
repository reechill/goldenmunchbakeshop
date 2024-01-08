<?php $title = 'Customer Login'; ?>
<?php require_once 'template/header.php'; ?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Customer Login</h1>
        <form action="code/customer-login.php" method="POST">
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
            <button type="submit" class="btn btn-primary w-100" name="btnLogin">Login</button>
            <a href="customer-register.php" class="btn btn-success w-100 my-1" name="btnRegister">Register</a>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
