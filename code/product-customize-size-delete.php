<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['id'])) {
    $size_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Optional: Check if the size is being used in any product before deleting
    // $usage_check_query = "SELECT * FROM product WHERE sizeid = '$size_id'";
    // $usage_check_result = mysqli_query($conn, $usage_check_query);
    // if (mysqli_num_rows($usage_check_result) > 0) {
    //     echo "<script> alert('Cannot delete size as it is being used in products.'); window.location = '../product-customize-size-list.php'; </script>";
    //     exit;
    // }

    // Retrieve and delete the image file associated with the size
    $image_query = "SELECT imagefilename FROM size WHERE sizeid = '$size_id'";
    $image_result = mysqli_query($conn, $image_query);
    if ($image_row = mysqli_fetch_assoc($image_result)) {
        $image_filename = $image_row['imagefilename'];
        if ($image_filename) {
            unlink("../img/upload/customizeproducts/size/" . $image_filename);
        }
    }

    // Delete the size record from the database
    $delete_query = "DELETE FROM size WHERE sizeid = '$size_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script> alert('Size deleted successfully'); window.location = '../product-customize-size-list.php'; </script>";
    } else {
        echo "<script> alert('Error deleting size: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.location = '../product-customize-size-list.php'; </script>";
}

require_once 'database-close.php';
?>
