<?php
require_once 'code/session-start.php';
$title = 'Add Category';
require_once 'template/header.php';
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Add New Category</h1>
        <form action="code/category-add.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100" name="addCategory">Add Category</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
