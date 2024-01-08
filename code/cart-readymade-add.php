<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['customerid'], $_POST['productid'], $_POST['quantity'])) {
        $customerId = $_SESSION['customerid'];
        $productId = $_POST['productid'];
        $quantity = $_POST['quantity'];

        // Check if the product is already in the cart
        $checkCartSql = "SELECT * FROM cartproductreadymade WHERE customerid = $customerId AND productreadymadeid = $productId";
        $checkResult = mysqli_query($conn, $checkCartSql);

        if (mysqli_num_rows($checkResult) > 0) {
            // Update existing cart entry
            $cartItem = mysqli_fetch_assoc($checkResult);
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateSql = "UPDATE cartproductreadymade SET quantity = $newQuantity WHERE cartproductreadymadeid = {$cartItem['cartproductreadymadeid']}";
            mysqli_query($conn, $updateSql);
        } else {
            // Insert new cart entry
            $insertSql = "INSERT INTO cartproductreadymade (customerid, productreadymadeid, quantity) VALUES ($customerId, $productId, $quantity)";
            mysqli_query($conn, $insertSql);
        }

        header('Location: ../cart-readymade-list.php');
    } else {
        echo "Invalid request";
    }
} else {
    echo "Method not allowed";
}

require_once 'database-close.php';
?>
