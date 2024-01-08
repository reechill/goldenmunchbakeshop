<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (!isset($_SESSION['customerid'])) {
    // Redirect to login page or show an error message
    echo "<script>alert('Please log in first.'); window.location.href = 'login.php';</script>";
    exit;
}

if (isset($_POST['productid']) && isset($_POST['quantity'])) {
    $customerId = $_SESSION['customerid'];
    $productId = $_POST['productid'];
    $quantity = $_POST['quantity'];

    // Check if product already in cart
    $checkCartSql = "SELECT * FROM cart WHERE customerid = '$customerId' AND productreadymadeid = '$productId'";
    $checkCartResult = mysqli_query($conn, $checkCartSql);

    if (mysqli_num_rows($checkCartResult) > 0) {
        // Update existing cart item
        $updateCartSql = "UPDATE cart SET quantity = quantity + $quantity WHERE customerid = '$customerId' AND productreadymadeid = '$productId'";
        mysqli_query($conn, $updateCartSql);
    } else {
        // Insert new cart item
        $addToCartSql = "INSERT INTO cart (customerid, productreadymadeid, quantity) VALUES ('$customerId', '$productId', '$quantity')";
        mysqli_query($conn, $addToCartSql);
    }

    // Redirect back to shop or to cart page
    header("Location: shop.php");
} else {
    // Redirect back or show an error
    echo "<script>alert('Invalid product details.'); window.history.back();</script>";
}

require_once 'database-close.php';
?>
