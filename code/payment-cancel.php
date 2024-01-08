<?php
require_once 'session-start.php';
require_once 'database-connect.php';

// Check if admin or staff is logged in
if (!(isset($_SESSION['adminid']) || isset($_SESSION['staffid']) || isset($_SESSION['customerid']) || isset($_SESSION['riderid']))) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['orderid']) && isset($_GET['paymentid'])) {
    $orderId = $_GET['orderid'];
    $paymentId = $_GET['paymentid'];

    // Update the payment entry to mark as cancelled
    $cancelQuery = "UPDATE orderpayment SET iscancelled = 'y' WHERE orderpaymentid = '$paymentId'";
    if (mysqli_query($conn, $cancelQuery)) {
        // Redirect back to the order view page
        header("Location: ../order-view.php?id=$orderId");
    } else {
        echo "Error cancelling payment: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}

require_once 'database-close.php';
?>
