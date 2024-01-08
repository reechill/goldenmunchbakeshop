<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Category List';
require_once 'template/header.php';

// Fetch categories
$query = "SELECT * FROM category";
$result = mysqli_query($conn, $query);
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="text-center my-5">Category List</h1>
        <a href="category-add.php" class="btn btn-success mb-3">Add Category</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($category['categoryid']); ?></td>
                    <td><?= htmlspecialchars($category['name']); ?></td>
                    <td><?= htmlspecialchars($category['description']); ?></td>
                    <td>
                        <a href="category-edit.php?categoryid=<?= $category['categoryid']; ?>" class="btn btn-primary">Edit</a>
                        <a href="code/category-delete.php?categoryid=<?= $category['categoryid']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
