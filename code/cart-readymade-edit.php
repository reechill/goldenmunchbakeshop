<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['customerid'], $_POST['id'], $_POST['quantity'])) {
        $cartProductReadymadeId = $_POST['id'];
        $quantity = $_POST['quantity'];

        // Update the quantity of the cart item
        $updateSql = "UPDATE cartproductreadymade SET quantity = $quantity WHERE cartproductreadymadeid = $cartProductReadymadeId";
        mysqli_query($conn, $updateSql);

        header('Location: ../cart-readymade-list.php');
    } else {
        echo "Invalid request";
    }
} else {
    echo "Method not allowed";
}

require_once 'database-close.php';
?>
