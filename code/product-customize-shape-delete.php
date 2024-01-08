<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_GET['id'])) {
    $shape_id = mysqli_real_escape_string($conn, $_GET['id']);

    // First, get the filename of the image to delete it from the server
    $query = "SELECT imagefilename FROM shape WHERE shapeid = '$shape_id'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $imageFileName = $row['imagefilename'];
        if (!empty($imageFileName)) {
            // Delete the image file from the server
            unlink("../img/upload/customizeproducts/shape/" . $imageFileName);
        }
    }

    // Now, delete the shape record from the database
    $delete_query = "DELETE FROM shape WHERE shapeid = '$shape_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script> alert('Shape deleted successfully'); window.location = '../product-customize-shape-list.php'; </script>";
    } else {
        echo "<script> alert('Error deleting shape: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
} else {
    echo "<script> alert('Invalid request.'); window.location = '../product-customize-shape-list.php'; </script>";
}

require_once 'database-close.php';
?>
