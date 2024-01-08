<?php
require_once 'code/session-start.php';
$title = 'Add Flavor';
require_once 'template/header.php';
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Add New Flavor</h1>
        <form action="code/product-customize-flavor-add.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Flavor Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100" name="addFlavor">Add Flavor</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
