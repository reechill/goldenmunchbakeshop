<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (!isset($_GET['categoryid']) || empty($_GET['categoryid'])) {
    echo "<script> alert('Error: Category ID is required.'); window.history.back(); </script>";
    exit;
}

$categoryid = $_GET['categoryid'];
$query = "DELETE FROM category WHERE categoryid = '$categoryid'";

if (mysqli_query($conn, $query)) {
    echo "<script> alert('Category deleted successfully.'); window.location.href = '../category-list.php'; </script>";
} else {
    echo "<script> alert('Error deleting category: " . mysqli_error($conn) . "'); window.history.back(); </script>";
}

require_once 'database-close.php';
?>
