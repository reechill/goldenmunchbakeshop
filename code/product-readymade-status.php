<?php
require_once 'session-start.php';
require_once 'database-connect.php';

// Check if the product ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script> alert('No product specified.'); window.location.href = '../product-readymade-list.php'; </script>";
    exit;
}

// Sanitize the input
$product_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch the current availability status
$query = "SELECT isavailable FROM productreadymade WHERE productreadymadeid = '$product_id'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $currentStatus = $row['isavailable'];

    // Determine the new status
    $newStatus = ($currentStatus === 'y') ? 'n' : 'y';

    // Update the product's availability
    $update_query = "UPDATE productreadymade SET isavailable = '$newStatus' WHERE productreadymadeid = '$product_id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script> alert('Product availability updated successfully'); window.location.href = '../product-readymade-list.php'; </script>";
    } else {
        echo "<script> alert('Error updating product availability: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Product not found.'); window.history.back(); </script>";
}

require_once 'database-close.php';
?>
