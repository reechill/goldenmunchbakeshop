<?php $title = 'Customer Registration'; ?>
<?php require_once 'template/header.php'; ?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Customer Registration</h1>
        <form action="code/customer-register.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required autofocus>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="contactnumber" class="form-label">Contact Number:</label>
                <input type="text" class="form-control" id="contactnumber" name="contactnumber" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <textarea class="form-control" id="address" name="address" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="btnRegister">Register</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
