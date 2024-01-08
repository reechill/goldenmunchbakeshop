<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['addCategory'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = !empty($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;

    // Check if category already exists
    $checkQuery = "SELECT * FROM category WHERE name = '$name'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script> alert('A category with this name already exists.'); window.history.back(); </script>";
    } else {
        $query = "INSERT INTO category (name, description) VALUES ('$name', '$description')";

        if (mysqli_query($conn, $query)) {
            echo "<script> alert('Category added successfully.'); window.location.href = '../category-list.php'; </script>";
        } else {
            echo "<script> alert('Error adding category: " . mysqli_error($conn) . "'); window.history.back(); </script>";
        }
    }
}

require_once 'database-close.php';
?>
