<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Cart';
require_once 'template/header.php';

// Fetch cart items for the logged-in customer
$customerId = $_SESSION['customerid'] ?? 0;
$cartQuery = "SELECT cartproductreadymade.*, productreadymade.name, productreadymade.price, productreadymade.imagefilename FROM cartproductreadymade JOIN productreadymade ON cartproductreadymade.productreadymadeid = productreadymade.productreadymadeid WHERE cartproductreadymade.customerid = $customerId";
$cartResult = mysqli_query($conn, $cartQuery);
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Cart</h1>
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

                // Image handling
                $imagePath = 'img/upload/readymadeproducts/' . $item['imagefilename']; // Update with your actual image path
                if (!file_exists($imagePath) || empty($item['imagefilename'])) {
                    $imagePath = 'img/no-image-available.png'; // Path to a default image
                }
                ?>
                <tr>
                    <td>
                        <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($item['name']); ?>" height="150px" width="150px"> 
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
    <div class="d-flex justify-content-end">
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
