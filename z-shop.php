<?php 
require_once 'code/session-start.php'; 
require_once 'code/database-connect.php';

$title = 'Ready-Made Products';
require_once 'template/header.php';

// Fetch categories for the dropdown or checkbox
$categoryQuery = "SELECT * FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

// Initialize filter and sorting variables
$selectedCategory = $_GET['category'] ?? [];
$searchQuery = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

// SQL to fetch products
$productSql = "SELECT * FROM productreadymade WHERE 1";

// Filter by category
if (!empty($selectedCategory)) {
    $categoryFilter = implode(",", array_map('intval', $selectedCategory));
    $productSql .= " AND productreadymadeid IN (SELECT productreadymadeid FROM productcategory WHERE categoryid IN ($categoryFilter))";
}

// Search functionality
if (!empty($searchQuery)) {
    $productSql .= " AND name LIKE '%$searchQuery%'";
}

// Sorting functionality
if ($sort == 'name_asc') {
    $productSql .= " ORDER BY name ASC";
} elseif ($sort == 'price_asc') {
    $productSql .= " ORDER BY price ASC";
} elseif ($sort == 'price_desc') {
    $productSql .= " ORDER BY price DESC";
}

$productResult = mysqli_query($conn, $productSql);

?>
<style>
    .card-body {
        display: flex;
        flex-direction: column;
        height: auto;
    }
    .product-name, .product-description, .product-action {
        margin-bottom: 10px;
    }
    
    .product-name {
        font-weight: bold;
    }
    
    .product-name, .product-description {
        height: 60px; /* Adjust as needed */
        overflow: hidden;
    }

    .product-image {
        height: 250px;
        width: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .no-image {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
    }
</style>

<div class="container mt-4">
    <h1 class="text-center my-5">Ready-Made Products</h1>
    
    <div class="row">
        <!-- Category Filter -->
        <div class="col-lg-3 mb-3">
            <h5>Categories</h5>
            <form action="shop.php" method="GET">
                <?php while ($category = mysqli_fetch_assoc($categoryResult)): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="category[]" value="<?= $category['categoryid']; ?>" <?= (in_array($category['categoryid'], $selectedCategory) ? 'checked' : '') ?>>
                        <label class="form-check-label" for="category[]">
                            <?= htmlspecialchars($category['name']); ?>
                        </label>
                    </div>
                <?php endwhile; ?>
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
                <a href="shop.php" class="btn btn-secondary mt-2">Clear</a>
            </form>
        </div>

        <!-- Products Display -->
        <div class="col-lg-9">
            <!-- Sorting & Search -->
            <div class="mb-3">
                <form action="shop.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search products" name="search" value="<?= htmlspecialchars($searchQuery); ?>">
                        <select class="form-select" name="sort">
                            <option value="name_asc" <?= ($sort == 'name_asc') ? 'selected' : '' ?>>Sort by Name (A-Z)</option>
                            <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Sort by Price (Lowest-Highest)</option>
                            <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Sort by Price (Highest-Lowest)</option>
                        </select>
                        </select>
                        <button class="btn btn-outline-secondary" type="submit">Search & Sort</button>
                        <a href="shop.php" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php
                            $imagePath = 'img/upload/readymadeproducts/' . htmlspecialchars($product['imagefilename']);
                            if (!file_exists($imagePath) || empty($product['imagefilename'])) {
                                $imagePath = 'img/no-image-available.png'; // Path to your no-image file
                            }
                            ?>
                            <div class="product-image" style="background-image: url('<?= $imagePath; ?>');">
                                <?php if (empty($product['imagefilename'])): ?>
                                    <div class="no-image"></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="product-name"><?= htmlspecialchars($product['name']); ?></div>
                                <div class="product-description"><?= htmlspecialchars($product['description']); ?></div>
                                <div>
                                    <div><strong>&#x20B1;<?= number_format($product['price'], 2); ?></strong></div>
                                    <?php if (isset($_SESSION['customerid'])): ?>
                                        <form action="code/cart-readymade-add.php" method="POST">
                                            <input type="number" class="form-control mb-2" name="quantity" min="1" value="1">
                                            <input type="hidden" name="productid" value="<?= $product['productreadymadeid']; ?>">
                                            <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
