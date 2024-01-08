<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['quantity'], $_POST['id'], $_POST['additionalnote'])) {
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $cartProductCustomizedId = mysqli_real_escape_string($conn, $_POST['id']);
    $additionalNote = mysqli_real_escape_string($conn, $_POST['additionalnote']);

    // Check if quantity is a positive integer
    if ($quantity > 0) {
        // Update the quantity and additional note in the cart
        $updateQuery = "UPDATE cartproductcustomized SET quantity = '$quantity', additionalnote = '$additionalNote' WHERE cartproductcustomizedid = '$cartProductCustomizedId'";

        if (mysqli_query($conn, $updateQuery)) {
            //echo "<script> alert('Cart updated successfully.'); window.location.href = '../cart-customized-list.php'; </script>";
            header('Location: ../cart-customized-list.php');
        } else {
            echo "<script> alert('Error updating cart.'); window.history.back(); </script>";
        }
    } else {
        echo "<script> alert('Invalid quantity.'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.history.back(); </script>";
}

require_once 'database-close.php';
?>
