<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

if (!isset($_GET['categoryid']) || empty($_GET['categoryid'])) {
    echo "<script> alert('Error: Category ID is required.'); window.history.back(); </script>";
    exit;
}

$categoryid = $_GET['categoryid'];
$query = "SELECT * FROM category WHERE categoryid = '$categoryid'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) != 1) {
    echo "<script> alert('Error: Category not found.'); window.history.back(); </script>";
    exit;
}

$category = mysqli_fetch_assoc($result);

$title = 'Edit Category';
require_once 'template/header.php';
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Category</h1>
        <form action="code/category-edit.php" method="POST">
            <input type="hidden" name="categoryid" value="<?= $category['categoryid']; ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= !empty($category['description']) ? htmlspecialchars($category['description']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100" name="editCategory">Update Category</button>
        </form>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
