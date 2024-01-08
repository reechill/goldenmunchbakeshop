<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Edit Size';
require_once 'template/header.php';

// Fetch the size data
$size_id = $_GET['id'] ?? null; 
if (!$size_id) {
    echo "<script> alert('Size not found.'); window.location = 'product-customize-size-list.php'; </script>";
    exit;
}

// Fetch size data
$size_query = "SELECT * FROM size WHERE sizeid = '$size_id'";
$size_result = mysqli_query($conn, $size_query);
$size = mysqli_fetch_assoc($size_result);

if (!$size) {
    echo "<script> alert('Size not found.'); window.location = 'product-customize-size-list.php'; </script>";
    exit;
}
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Size</h1>
        <form action="code/product-customize-size-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="size_id" value="<?= htmlspecialchars($size_id) ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Size Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($size['name']) ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($size['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($size['price']) ?>">
            </div>
            
            <button type="submit" class="btn btn-success w-100" name="editSize">Update Size</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
