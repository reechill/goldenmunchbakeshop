<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Checkout';
require_once 'template/header.php';

// Fetch cart items for the logged-in customer
$customerId = $_SESSION['customerid'] ?? 0;

// Fetch Ready-Made Cart Items
$readyMadeQuery = "SELECT cartproductreadymade.*, productreadymade.name, productreadymade.price, productreadymade.imagefilename FROM cartproductreadymade JOIN productreadymade ON cartproductreadymade.productreadymadeid = productreadymade.productreadymadeid WHERE cartproductreadymade.customerid = $customerId";
$readyMadeResult = mysqli_query($conn, $readyMadeQuery);

// Fetch Customized Cart Items
// Fetch Customized Cart Items with Individual Prices
$customizedQuery = "SELECT cartproductcustomized.*, 
                           flavor.name as flavorName, flavor.price as flavorPrice, 
                           shape.name as shapeName, shape.price as shapePrice, 
                           size.name as sizeName, size.price as sizePrice, 
                           (flavor.price + shape.price + size.price) as itemPrice,
                           cartproductcustomized.additionalnote 
                    FROM cartproductcustomized 
                    JOIN flavor ON cartproductcustomized.flavorid = flavor.flavorid 
                    JOIN shape ON cartproductcustomized.shapeid = shape.shapeid 
                    JOIN size ON cartproductcustomized.sizeid = size.sizeid 
                    WHERE cartproductcustomized.customerid = $customerId";
$customizedResult = mysqli_query($conn, $customizedQuery);

$readyMadeTotal = $customizedTotal = 0;

// getting customer information
$customerQuery = "SELECT name, contactnumber, address FROM customer WHERE customerid = $customerId";
$customerResult = mysqli_query($conn, $customerQuery);
$customerInfo = mysqli_fetch_assoc($customerResult);
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Checkout</h1>

    <!-- Ready-Made Products Section -->
    <div class="card mb-4">
        <div class="card-header">Ready-Made Products</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($readyMadeResult)) {
                        $imagePath = !empty($item['imagefilename']) ? 'img/upload/readymadeproducts/' . $item['imagefilename'] : 'img/no-image-available.png';
                        $subtotal = $item['quantity'] * $item['price'];
                        $readyMadeTotal += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($item['name']); ?>" height="150px" width="150px"> 
                                <?= htmlspecialchars($item['name']); ?>
                            </td>
                            <td>&#x20B1;<?= number_format($item['price'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><a href="cart-readymade-list.php" class="btn btn-success">Review Cart</a></td>
                        <td colspan="2" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">&#x20B1;<?= number_format($readyMadeTotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Customized Products Section -->
    <div class="card mb-4">
        <div class="card-header">Customized Products</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customization</th>
                        <th>Additional Note</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($customizedResult)) {
                        $subtotal = $item['quantity'] * $item['itemPrice'];
                        $customizedTotal += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <ul>
                                    <li>Flavor: <?= htmlspecialchars($item['flavorName']) . " - &#x20B1;" . number_format($item['flavorPrice'], 2); ?></li>
                                    <li>Shape: <?= htmlspecialchars($item['shapeName']) . " - &#x20B1;" . number_format($item['shapePrice'], 2); ?></li>
                                    <li>Size: <?= htmlspecialchars($item['sizeName']) . " - &#x20B1;" . number_format($item['sizePrice'], 2); ?></li>
                                </ul>
                            </td>
                            <td><?= htmlspecialchars($item['additionalnote']); ?></td>
                            <td>&#x20B1;<?= number_format($item['itemPrice'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><a href="cart-customized-list.php" class="btn btn-success">Review Cart</a></td>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">&#x20B1;<?= number_format($customizedTotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Overall Total -->
    <div class="text-end mb-4">
        <h3>Overall Total: &#x20B1;<?= number_format($readyMadeTotal + $customizedTotal, 2); ?></h3>
    </div>

    <!-- Order Information Form -->
    <form action="code/order.php" method="POST" onsubmit="return confirm('Are you sure you want to place the order now?');">
        <h4>Order Information</h4>
        <div class="mb-3">
            <label for="customerName" class="form-label">Name:</label>
            <input type="text" class="form-control" id="customerName" name="customerName" value="<?= htmlspecialchars($customerInfo['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="contactNumber" class="form-label">Contact Number:</label>
            <input type="text" class="form-control" id="contactNumber" name="contactNumber" value="<?= htmlspecialchars($customerInfo['contactnumber']); ?>" required>
        </div>

        <!-- Date and Time Needed (if applicable) -->
        <div class="mb-3">
            <label for="neededDateTime" class="form-label">Date and Time the Order is Needed (leave blank if applicable):</label>
            <input type="datetime-local" class="form-control" id="neededDateTime" name="neededDateTime">
        </div>

        <!-- Additional Notes -->
        <div class="mb-3">
            <label for="additionalNotes" class="form-label">Additional Notes:</label>
            <textarea class="form-control" id="additionalNotes" name="additionalNotes" rows="3"></textarea>
        </div>

        <!-- Delivery Option -->
        <div class="mb-3">
            <label for="deliveryOption" class="form-label">Delivery Option:</label>
            <select class="form-control" id="deliveryOption" name="deliveryOption" required>
                <option value="">-Select-</option>
                <option value="pick-up">Pick-Up at Store</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="deliveryAddress" class="form-label">Delivery Address:</label>
            <textarea class="form-control" id="deliveryAddress" name="deliveryAddress" rows="3"><?= htmlspecialchars($customerInfo['address']); ?></textarea>
        </div>

        <!-- Notice for Possible Delivery Fee -->
        <div class="mb-4">
            <h5>Delivery Fee Information</h5>
            <ul>
                <li>City Proper - 50 pesos</li>
                <li>Lorenzo Tan to Pangabuan - starts at 50 pesos</li>
                <li>Maquilao - 40 pesos</li>
                <li>Baga - 50 pesos</li>
                <li>Balatacan - 150 pesos</li>
            </ul>
            <p class="text-muted">* Delivery fees will be paid upon delivery.</p>
        </div>

        <!-- Submit Order Button -->
        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>
</div>

<?php 
    require_once 'code/database-close.php';
    require_once 'template/footer.php'; 
?>
