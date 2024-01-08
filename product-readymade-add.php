<?php
require_once 'code/session-start.php';
$title = 'Add Ready-Made Product';
require_once 'template/header.php';
require_once 'code/database-connect.php';

// Fetch categories for checkboxes
$query = "SELECT * FROM category ORDER BY name";
$categories_result = mysqli_query($conn, $query);
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Add New Ready-Made Product</h1>
        <form action="code/product-readymade-add.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.00" max="10000000" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image:</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="mb-3">
                <label class="form-label">Categories:</label>
                <?php 
                    while ($category = mysqli_fetch_assoc($categories_result)) {
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='checkbox' value='" . $category['categoryid'] . "' id='category" . $category['categoryid'] . "' name='categories[]'>";
                        echo "<label class='form-check-label' for='category" . $category['categoryid'] . "'>" . htmlspecialchars($category['name']) . "</label>";
                        echo "</div>";
                    }
                ?>
            </div>
            <button type="submit" class="btn btn-success w-100" name="addProduct">Add Product</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
