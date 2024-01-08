<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['id'])) {
    $cartProductCustomizedId = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete the item from the cart
    $deleteQuery = "DELETE FROM cartproductcustomized WHERE cartproductcustomizedid = '$cartProductCustomizedId'";

    if (mysqli_query($conn, $deleteQuery)) {
        //echo "<script> alert('Item removed from cart successfully.'); window.location.href = '../cart-customized-list.php'; </script>";
        header('Location: ../cart-customized-list.php');
    } else {
        echo "<script> alert('Error removing item from cart.'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.history.back(); </script>";
}

require_once 'database-close.php';
?>
