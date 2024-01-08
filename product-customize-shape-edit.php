<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Edit Shape';
require_once 'template/header.php';

// Fetch the shape data
$shape_id = $_GET['id'] ?? null; 
if (!$shape_id) {
    echo "<script> alert('Shape not found.'); window.location = 'product-customize-shape-list.php'; </script>";
    exit;
}

// Fetch shape data
$shape_query = "SELECT * FROM shape WHERE shapeid = '$shape_id'";
$shape_result = mysqli_query($conn, $shape_query);
$shape = mysqli_fetch_assoc($shape_result);

if (!$shape) {
    echo "<script> alert('Shape not found.'); window.location = 'product-customize-shape-list.php'; </script>";
    exit;
}
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Shape</h1>
        <form action="code/product-customize-shape-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="shape_id" value="<?= htmlspecialchars($shape_id) ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Shape Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($shape['name']) ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($shape['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($shape['price']) ?>">
            </div>
            
            <button type="submit" class="btn btn-success w-100" name="editShape">Update Shape</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
