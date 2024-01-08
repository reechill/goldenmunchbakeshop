<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['id'])) {
    $flavor_id = mysqli_real_escape_string($conn, $_GET['id']);

    $delete_query = "DELETE FROM flavor WHERE flavorid = '$flavor_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script> alert('Flavor deleted successfully'); window.location = '../product-customize-flavor-list.php'; </script>";
    } else {
        echo "<script> alert('Error deleting flavor: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.location = '../product-customize-flavor-list.php'; </script>";
}

require_once 'database-close.php';
?>
