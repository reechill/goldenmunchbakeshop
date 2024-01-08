<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['editSize'])) {
    $size_id = $_POST['size_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../img/upload/customizeproducts/size/";
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9]+/", "-", $name);
        $uniqueSuffix = time();
        $newImageFileName = $sanitizedFileName . "-" . $uniqueSuffix . "." . $fileExtension;
        $target_file = $target_dir . $newImageFileName;

        // Remove old image file
        $oldImageQuery = "SELECT imagefilename FROM size WHERE sizeid = '$size_id'";
        $oldImageResult = mysqli_query($conn, $oldImageQuery);
        $oldImageRow = mysqli_fetch_assoc($oldImageResult);
        if ($oldImageRow['imagefilename']) {
            unlink($target_dir . $oldImageRow['imagefilename']);
        }

        // Upload new image
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script> alert('Failed to upload image.'); window.history.back(); </script>";
            exit;
        }

        $update_query = "UPDATE size SET name='$name', description='$description', price='$price', imagefilename='$newImageFileName' WHERE sizeid='$size_id'";
    } else {
        $update_query = "UPDATE size SET name='$name', description='$description', price='$price' WHERE sizeid='$size_id'";
    }

    // Update size
    if (mysqli_query($conn, $update_query)) {
        echo "<script> alert('Size updated successfully'); window.location = '../product-customize-size-list.php'; </script>";
    } else {
        echo "<script> alert('Error updating size: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
