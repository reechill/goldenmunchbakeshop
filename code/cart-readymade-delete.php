<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_SESSION['customerid'], $_GET['id'])) {
        $cartProductReadymadeId = $_GET['id'];

        // Delete the item from the cart
        $deleteSql = "DELETE FROM cartproductreadymade WHERE cartproductreadymadeid = $cartProductReadymadeId";
        mysqli_query($conn, $deleteSql);

        header('Location: ../cart-readymade-list.php');
    } else {
        echo "Invalid request";
    }
} else {
    echo "Method not allowed";
}

require_once 'database-close.php';
?>
