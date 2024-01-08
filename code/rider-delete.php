<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['riderid'])) {
    $riderid = mysqli_real_escape_string($conn, $_GET['riderid']);

    // Execute the delete query
    $deleteQuery = "DELETE FROM rider WHERE riderid = $riderid";
    if (mysqli_query($conn, $deleteQuery)) {
        header('Location: ../rider-list.php');
    } else {
        echo "<script>alert('Error deleting rider.'); window.history.back();</script>";
    }
}

require_once 'database-close.php';
?>
