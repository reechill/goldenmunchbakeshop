<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['addFlavor'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $imageFileName = '';

    // Check for duplicate flavor
    $check_query = "SELECT * FROM flavor WHERE name = '$name'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script> alert('Flavor already exists.'); window.history.back(); </script>";
        exit;
    }

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../img/upload/customizeproducts/flavor/";
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $sanitizedFileName = preg_replace("/[^a-zA-Z0-9]+/", "-", $name);
        $uniqueSuffix = time();
        $imageFileName = $sanitizedFileName . "-" . $uniqueSuffix . "." . $fileExtension;
        $target_file = $target_dir . $imageFileName;

        // File upload error handling
        if ($_FILES['image']['error'] != 0) {
            echo "<script> alert('Error uploading file.'); window.history.back(); </script>";
            exit;
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "<script> alert('Failed to upload image.'); window.history.back(); </script>";
            exit;
        }
    }

    // Insert flavor into database
    $insert_query = "INSERT INTO flavor (name, description, price, imagefilename) VALUES ('$name', '$description', '$price', '$imageFileName')";
    if (mysqli_query($conn, $insert_query)) {
        echo "<script> alert('Flavor added successfully'); window.location = '../product-customize-flavor-list.php'; </script>";
    } else {
        echo "<script> alert('Error adding flavor: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
