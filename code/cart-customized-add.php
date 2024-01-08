<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['flavorid'], $_POST['shapeid'], $_POST['sizeid'], $_POST['quantity'])) {
    $customerId = $_SESSION['customerid'] ?? 0;
    $flavorId = mysqli_real_escape_string($conn, $_POST['flavorid']);
    $shapeId = mysqli_real_escape_string($conn, $_POST['shapeid']);
    $sizeId = mysqli_real_escape_string($conn, $_POST['sizeid']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $additionalNote = mysqli_real_escape_string($conn, $_POST['additionalnote'] ?? '');

    // Check if the same combination of flavor, shape, size, and note already exists in the cart for this customer
    $checkQuery = "SELECT * FROM cartproductcustomized WHERE customerid = '$customerId' AND flavorid = '$flavorId' AND shapeid = '$shapeId' AND sizeid = '$sizeId' AND additionalnote = '$additionalNote'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Item already exists in the cart
        echo "<script> alert('This customized product is already in your cart.'); window.history.back(); </script>";
    } else {
        // Insert new item into the cart
        $insertQuery = "INSERT INTO cartproductcustomized (customerid, flavorid, shapeid, sizeid, quantity, additionalnote) VALUES ('$customerId', '$flavorId', '$shapeId', '$sizeId', '$quantity', '$additionalNote')";
        
        if (mysqli_query($conn, $insertQuery)) {
            //echo "<script> alert('Customized product added to cart successfully.'); window.location.href = '../cart-customized-list.php'; </script>";
            header('Location: ../cart-customized-list.php');
        } else {
            echo "<script> alert('Error adding product to cart.'); window.history.back(); </script>";
        }
    }
} else {
    echo "<script> alert('Invalid product details.'); window.history.back(); </script>";
}

require_once 'database-close.php';
?>
