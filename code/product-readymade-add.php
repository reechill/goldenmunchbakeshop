<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['addProduct'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $imageFileName = '';

    // Check if the product already exists
    $check_query = "SELECT * FROM productreadymade WHERE name = '$name'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script> alert('Product already exists.'); window.history.back(); </script>";
        exit;
    }

    // Handle the file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../img/upload/readymadeproducts/";
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9]+/", "-", $name); // Sanitizing the product name
        $uniqueSuffix = time(); // Unique identifier
        $imageFileName = $sanitizedFileName . "-" . $uniqueSuffix . "." . $fileExtension;
        $target_file = $target_dir . $imageFileName;

        // File upload error handling
        if ($_FILES['image']['error'] != 0) {
            echo "<script> alert('Error uploading file.'); window.history.back(); </script>";
            exit;
        }

        // Additional file validations (size, type) can be added here

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script> alert('Failed to upload image.'); window.history.back(); </script>";
            exit;
        }
    }

    // Insert into productreadymade table
    $insert_query = "INSERT INTO productreadymade (name, description, price, imagefilename) VALUES ('$name', '$description', '$price', '$imageFileName')";
    if (mysqli_query($conn, $insert_query)) {
        $product_id = mysqli_insert_id($conn);

        // Handle categories
        if (!empty($_POST['categories'])) {
            foreach ($_POST['categories'] as $category_id) {
                $category_insert_query = "INSERT INTO productcategory (productreadymadeid, categoryid) VALUES ('$product_id', '$category_id')";
                mysqli_query($conn, $category_insert_query);
            }
        }

        echo "<script> alert('Product added successfully'); window.location = '../product-readymade-list.php'; </script>";
    } else {
        echo "<script> alert('Error adding product: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
