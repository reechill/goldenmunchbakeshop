<?php 
require_once 'code/session-start.php'; 
require_once 'code/database-connect.php';

$title = 'Ready-Made Products';
require_once 'template/header.php';

// Fetch categories for filters
$categoryQuery = "SELECT * FROM category";
$categoryResult = mysqli_query($conn, $categoryQuery);

// Initialize filter and sorting variables
$selectedCategory = $_GET['category'] ?? [];
$searchQuery = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

// SQL to fetch products
$productSql = "SELECT * FROM productreadymade WHERE isavailable='y' ";

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

// Fetch cart items for the logged-in customer
$customerId = $_SESSION['customerid'] ?? 0;
$cartQuery = "SELECT cartproductreadymade.*, productreadymade.name, productreadymade.price, productreadymade.imagefilename FROM cartproductreadymade JOIN productreadymade ON cartproductreadymade.productreadymadeid = productreadymade.productreadymadeid WHERE cartproductreadymade.customerid = $customerId";
$cartResult = mysqli_query($conn, $cartQuery);

?>
<style>
    .card-body {
        display: flex;
        flex-direction: column;
        height: auto;
    }
    .product-name, .product-description, .product-action {
        margin-bottom: 10px;
        font-size: 12px; /* Set the desired font size */
    }
    
    .product-name {
        font-weight: bold;
    }
    
    .product-name, .product-description {
        height: auto; /* Adjust as needed */
        overflow:hidden;
    }

    .product-image {
        height: 150px;
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
        <!-- Category Filter & Sorting -->
        <div class="col-lg-2 mb-3">
            <!-- Category Filter Section -->
            <h5>Categories</h5>
            <form action="cart-readymade-list.php" method="GET">
                <!-- Category checkboxes -->
                <?php while ($category = mysqli_fetch_assoc($categoryResult)): ?>
                    <div class="form-check">
                        <input class="form-check-input" 
                            type="checkbox" 
                            name="category[]" 
                            id="category<?= $category['categoryid']; ?>" 
                            value="<?= $category['categoryid']; ?>" 
                            <?= in_array($category['categoryid'], $selectedCategory) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="category<?= $category['categoryid']; ?>">
                            <?= htmlspecialchars($category['name']); ?>
                        </label>
                    </div>
                <?php endwhile; ?>

                <!-- Hidden inputs for preserving search and sort -->
                <input type="hidden" name="search" value="<?= htmlspecialchars($searchQuery); ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort); ?>">

                <button type="submit" class="btn btn-primary mt-2">Filter</button>
                <a href="cart-readymade-list.php" class="btn btn-secondary mt-2">Clear</a>
            </form>
        </div>

        <!-- Products Display -->
        <div class="<?php if (isset($_SESSION['customerid'])) { echo "col-lg-5"; } else { echo "col-lg-10"; } ?>">
            <!-- Sorting & Search -->
            <div class="mb-3">
                <form action="cart-readymade-list.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search products" name="search" value="<?= htmlspecialchars($searchQuery); ?>">
                        <select class="form-select" name="sort">
                            <option value="name_asc" <?= ($sort == 'name_asc') ? 'selected' : '' ?>>Sort by Name (A-Z)</option>
                            <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Sort by Price (Lowest-Highest)</option>
                            <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Sort by Price (Highest-Lowest)</option>
                        </select>
                        <button class="btn btn-outline-secondary" type="submit">Search & Sort</button>
                    </div>

                    <!-- Hidden inputs for preserving category filters -->
                    <?php foreach ($selectedCategory as $selectedCat): ?>
                        <input type="hidden" name="category[]" value="<?= htmlspecialchars($selectedCat); ?>">
                    <?php endforeach; ?>
                </form>
            </div>

            <!-- Products Grid -->
            <div class="row">
                   <!-- Product cards loop -->
                   <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                    <div class="col-md-5 mb-4">
                    <div class="card h-100" style="max-width: 200px;"> <!-- Adjust the max-width as needed -->
                            <!-- Image -->
                            <div class="product-image" style="background-image: url('<?= !empty($product['imagefilename']) ? 'img/upload/readymadeproducts/' . htmlspecialchars($product['imagefilename']) : 'img/no-image-available.png'; ?>');">
                                <?php if (empty($product['imagefilename'])): ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <!-- Product name and description -->
                                <div class="product-name"><?= htmlspecialchars($product['name']); ?>
                                    <div>
                                        <?php
                                        $rating = floor($product['rating']); // Round down the rating
                                        for ($i = 0; $i < $rating; $i++) {
                                            echo '★'; // Display star for each point
                                        }
                                        ?>
                                    </div>
                                </div>
                            <div class="product-description"><?= htmlspecialchars($product['description']); ?></div>
                                <div>
                                    <div><strong>&#x20B1;<?= number_format($product['price'], 2); ?></strong></div>
                                    <?php if (isset($_SESSION['customerid'])): ?>
                                        <!-- Add to Cart Form -->
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

        <?php if (isset($_SESSION['customerid'])): ?>
        <div class="col-lg-5">
        <!-- Cart Section -->
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-end">Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                while ($item = mysqli_fetch_assoc($cartResult)) {
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?= !empty($item['imagefilename']) ? 'img/upload/readymadeproducts/' . htmlspecialchars($item['imagefilename']) : 'img/no-image-available.png'; ?>" alt="<?= htmlspecialchars($item['name']); ?>" height="150px" width="150px"> 
                            <?= htmlspecialchars($item['name']); ?>
                        </td>
                        <td>&#x20B1;<?= number_format($item['price'], 2); ?></td>
                        <td>
                            <form action="code/cart-readymade-edit.php" method="POST">
                                <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" class="form-control">
                                <input type="hidden" name="id" value="<?= $item['cartproductreadymadeid']; ?>">
                        </td>
                        <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                        <td>
                            <button type="submit" class="btn btn-info btn-sm">Update</button>
                            </form>
                            <a href="code/cart-readymade-delete.php?id=<?= $item['cartproductreadymadeid']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td class="text-end">&#x20B1;<?= number_format($total, 2); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="d-flex justify-content-end d-none">
            <a href="order-checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
