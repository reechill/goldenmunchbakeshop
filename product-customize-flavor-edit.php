<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Edit Flavor';
require_once 'template/header.php';

// Fetch the flavor data
$flavor_id = $_GET['id'] ?? null; 
if (!$flavor_id) {
    echo "<script> alert('Flavor not found.'); window.location = 'product-customize-flavor-list.php'; </script>";
    exit;
}

// Fetch flavor data
$flavor_query = "SELECT * FROM flavor WHERE flavorid = '$flavor_id'";
$flavor_result = mysqli_query($conn, $flavor_query);
$flavor = mysqli_fetch_assoc($flavor_result);

if (!$flavor) {
    echo "<script> alert('Flavor not found.'); window.location = 'product-customize-flavor-list.php'; </script>";
    exit;
}
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Flavor</h1>
        <form action="code/product-customize-flavor-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="flavor_id" value="<?= htmlspecialchars($flavor_id) ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Flavor Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($flavor['name']) ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($flavor['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($flavor['price']) ?>">
            </div>
            
            <button type="submit" class="btn btn-success w-100" name="editFlavor">Update Flavor</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
