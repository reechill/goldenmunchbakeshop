<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['editProduct'])) {
    $product_id = $_POST['product_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);

    // Handle the file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../img/upload/readymadeproducts/";
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9]+/", "-", $name);
        $uniqueSuffix = time();
        $newImageFileName = $sanitizedFileName . "-" . $uniqueSuffix . "." . $fileExtension;
        $target_file = $target_dir . $newImageFileName;

        // Remove the old image file
        $oldImageQuery = "SELECT imagefilename FROM productreadymade WHERE productreadymadeid = '$product_id'";
        $oldImageResult = mysqli_query($conn, $oldImageQuery);
        $oldImageRow = mysqli_fetch_assoc($oldImageResult);
        if ($oldImageRow['imagefilename']) {
            unlink($target_dir . $oldImageRow['imagefilename']);
        }

        // Upload the new image
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $update_query = "UPDATE productreadymade SET name='$name', description='$description', price='$price', imagefilename='$newImageFileName' WHERE productreadymadeid='$product_id'";
        } else {
            echo "<script> alert('Failed to upload image.'); window.history.back(); </script>";
            exit;
        }
    } else {
        $update_query = "UPDATE productreadymade SET name='$name', description='$description', price='$price' WHERE productreadymadeid='$product_id'";
    }

    // Update product info
    if (mysqli_query($conn, $update_query)) {
        // Update categories by removing all and adding new selections
        $delete_categories_query = "DELETE FROM productcategory WHERE productreadymadeid = '$product_id'";
        mysqli_query($conn, $delete_categories_query);

        if (!empty($_POST['categories'])) {
            foreach ($_POST['categories'] as $category_id) {
                $category_insert_query = "INSERT INTO productcategory (productreadymadeid, categoryid) VALUES ('$product_id', '$category_id')";
                mysqli_query($conn, $category_insert_query);
            }
        }

        echo "<script> alert('Product updated successfully'); window.location = '../product-readymade-list.php'; </script>";
    } else {
        echo "<script> alert('Error updating product: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
