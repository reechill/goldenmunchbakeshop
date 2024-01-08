<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Customized Products';
require_once 'template/header.php';

// Fetch flavors, shapes, sizes for radio buttons
$flavorsQuery = "SELECT * FROM flavor";
$shapesQuery = "SELECT * FROM shape";
$sizesQuery = "SELECT * FROM size";

$flavorsResult = mysqli_query($conn, $flavorsQuery);
$shapesResult = mysqli_query($conn, $shapesQuery);
$sizesResult = mysqli_query($conn, $sizesQuery);

// Check if the customer is logged in
$customerId = $_SESSION['customerid'] ?? 0;
$isLoggedIn = isset($_SESSION['customerid']);
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Customized Products</h1>

    <div class="row justify-content-center">
        <div class="<?= $isLoggedIn ? 'col-lg-4' : 'col-lg-12'; ?>">
            <form action="code/cart-customized-add.php" method="POST">
            <div class="text-center"> <!-- Center the content -->
                <img src="img/chloe.png" style="height: 200px; width: 200px;" class="mx-auto d-block">
            </div>
                <?php displayOptions($flavorsResult, 'flavorid', 'Flavor', 'flavor', $isLoggedIn); ?>
                <?php displayOptions($shapesResult, 'shapeid', 'Shape', 'shape', $isLoggedIn); ?>
                <?php displayOptions($sizesResult, 'sizeid', 'Size', 'size', $isLoggedIn); ?>

                <?php if ($isLoggedIn): ?>
                    <div class="mb-3">
                        <label for="additionalnote" class="form-label">Additional Note:</label>
                        <textarea class="form-control" id="additionalnote" name="additionalnote" rows="3"></textarea>
                    </div>
                    <div class="row gx-2 mb-3">
                        <div class="col">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1">
                        </div>
                        <div class="col">
                            <label class="form-label d-block">&nbsp;</label> <!-- Empty label for alignment -->
                            <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($isLoggedIn): ?>
            <div class="col-lg-8">
                <?php
                $cartQuery = "SELECT cartproductcustomized.*, flavor.name as flavorName, flavor.price as flavorPrice, shape.name as shapeName, shape.price as shapePrice, size.name as sizeName, size.price as sizePrice, (flavor.price + shape.price + size.price) as itemPrice, cartproductcustomized.additionalnote FROM cartproductcustomized JOIN flavor ON cartproductcustomized.flavorid = flavor.flavorid JOIN shape ON cartproductcustomized.shapeid = shape.shapeid JOIN size ON cartproductcustomized.sizeid = size.sizeid WHERE cartproductcustomized.customerid = $customerId";
                $cartResult = mysqli_query($conn, $cartQuery);
                ?>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Customization</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Note</th>
                            <th class="text-end">Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = mysqli_fetch_assoc($cartResult)) {
                            $subtotal = $item['quantity'] * $item['itemPrice'];
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <ul>
                                        <li>Flavor: <?= htmlspecialchars($item['flavorName']) . " - &#x20B1;" . number_format($item['flavorPrice'], 2); ?></li>
                                        <li>Shape: <?= htmlspecialchars($item['shapeName']) . " - &#x20B1;" . number_format($item['shapePrice'], 2); ?></li>
                                        <li>Size: <?= htmlspecialchars($item['sizeName']) . " - &#x20B1;" . number_format($item['sizePrice'], 2); ?></li>
                                    </ul>
                                </td>
                                <td>&#x20B1;<?= number_format($item['itemPrice'], 2); ?></td>
                                <td>
                                    <form action="code/cart-customized-edit.php" method="POST">
                                        <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" class="form-control">
                                        <input type="hidden" name="id" value="<?= $item['cartproductcustomizedid']; ?>">
                                </td>
                                <td>
                                    <textarea name="additionalnote" class="form-control"><?= htmlspecialchars($item['additionalnote']); ?></textarea>
                                </td>
                                <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                                <td>
                                        <button type="submit" class="btn btn-info btn-sm">Update</button>
                                    </form>
                                    <a href="code/cart-customized-delete.php?id=<?= $item['cartproductcustomizedid']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">&#x20B1;<?= number_format($total, 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-end d-none">
                    <a href="order-checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';

function displayOptions($result, $name, $label, $folder, $isLoggedIn) {
    echo "<div class='mb-4'>";
    echo "<h4 class='mb-3'>$label</h4>";
    while ($row = mysqli_fetch_assoc($result)) {
        $imagePath = 'img/upload/customizeproducts/' . $folder . '/' . $row['imagefilename'];
        
            
        
        echo "<div class='form-check form-check-inline'>";
        // Display radio button only if user is logged in
        if ($isLoggedIn) {
            echo "<input class='form-check-input' type='radio' name='$name' id='{$row['name']}' value='{$row[$name]}' required>";
        }
        echo "<label class='form-check-label' for='{$row['name']}'>";
        
        echo "<div>" . htmlspecialchars($row['name']) . " - &#x20B1;" . number_format($row['price'], 2) . "</div>";
        echo "</label>";
        echo "</div>";
    }
    echo "</div>";
}
?>
