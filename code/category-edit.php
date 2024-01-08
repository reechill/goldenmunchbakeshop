<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['editCategory'])) {
    $categoryid = mysqli_real_escape_string($conn, $_POST['categoryid']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = !empty($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;

    // Check if category with the same name exists and is not the current category
    $checkQuery = "SELECT * FROM category WHERE name = '$name' AND categoryid != '$categoryid'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script> alert('Another category with this name already exists.'); window.history.back(); </script>";
    } else {
        $query = "UPDATE category SET name = '$name', description = ".($description ? "'$description'" : "NULL")." WHERE categoryid = '$categoryid'";

        if (mysqli_query($conn, $query)) {
            echo "<script> alert('Category updated successfully.'); window.location.href = '../category-list.php'; </script>";
        } else {
            echo "<script> alert('Error updating category: " . mysqli_error($conn) . "'); window.history.back(); </script>";
        }
    }
}

require_once 'database-close.php';
?>
