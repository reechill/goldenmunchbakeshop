<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Edit Ready-Made Product';
require_once 'template/header.php';

// Fetch the product data
$product_id = $_GET['id'] ?? null; // or use a more secure method to get the id
if (!$product_id) {
    echo "<script> alert('Product not found.'); window.location = 'product-readymade-list.php'; </script>";
    exit;
}

// Fetch product data
$product_query = "SELECT * FROM productreadymade WHERE productreadymadeid = '$product_id'";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

// Fetch categories for checkboxes
$category_query = "SELECT * FROM category ORDER BY name";
$category_result = mysqli_query($conn, $category_query);

// Fetch selected categories
$selected_categories_query = "SELECT categoryid FROM productcategory WHERE productreadymadeid = '$product_id'";
$selected_categories_result = mysqli_query($conn, $selected_categories_query);
$selected_categories = mysqli_fetch_all($selected_categories_result, MYSQLI_ASSOC);
$selected_category_ids = array_column($selected_categories, 'categoryid');
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <h1 class="text-center my-5">Edit Ready-Made Product</h1>
        <form action="code/product-readymade-edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name:</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="number" step="0.01" min="0.00" max="10000000.00" class="form-control" id="price" name="price" required value="<?= htmlspecialchars($product['price']) ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image:</label>
                <?php if ($product['imagefilename']): ?>
                    <img src="img/upload/readymadeproducts/<?= htmlspecialchars($product['imagefilename']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 100px; width: 100px; object-fit: cover;">
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            <div class="mb-3">
                <label class="form-label">Categories:</label>
                <?php 
                    while ($category = mysqli_fetch_assoc($category_result)) {
                        $checked = in_array($category['categoryid'], $selected_category_ids) ? 'checked' : '';
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='checkbox' value='" . $category['categoryid'] . "' id='category" . $category['categoryid'] . "' name='categories[]' $checked>";
                        echo "<label class='form-check-label' for='category" . $category['categoryid'] . "'>" . htmlspecialchars($category['name']) . "</label>";
                        echo "</div>";
                    }
                ?>
            </div>
            <button type="submit" class="btn btn-success w-100" name="editProduct">Update Product</button>
        </form>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
