<?php require_once 'code/session-start.php'; ?>

<?php $title = 'Add Staff'; ?>

<?php require_once 'template/header.php'; ?>

<div class="row">
    <div class="col-lg-4 m-auto">
        <h1 class="text-center my-5">Add Staff</h1>
        <form action="code/staff-add.php" method="POST">
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
            <button type="submit" class="btn btn-success w-100" name="btnAddStaff">Add Staff</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
