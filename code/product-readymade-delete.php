<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Retrieve the image filename
    $query = "SELECT imagefilename FROM productreadymade WHERE productreadymadeid = '$product_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $imageFileName = $row['imagefilename'];

    // Delete category associations
    $delete_category_query = "DELETE FROM productcategory WHERE productreadymadeid = '$product_id'";
    mysqli_query($conn, $delete_category_query);

    // Delete the product
    $delete_product_query = "DELETE FROM productreadymade WHERE productreadymadeid = '$product_id'";
    if (mysqli_query($conn, $delete_product_query)) {
        // Check if file exists and delete the image file
        if (!empty($imageFileName)) {
            $filePath = "../img/upload/readymadeproducts/" . $imageFileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        echo "<script> alert('Product deleted successfully'); window.location = '../product-readymade-list.php'; </script>";
    } else {
        echo "<script> alert('Error deleting product: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.location = '../product-readymade-list.php'; </script>";
}

require_once 'database-close.php';
?>
