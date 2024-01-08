<?php
require_once 'session-start.php';
require_once 'database-connect.php';

// Check if the staff ID is set in the URL
if (!isset($_GET['id'])) {
    echo "<script>alert('No staff ID provided.'); window.history.back();</script>";
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Prepare the delete statement
$sql = "DELETE FROM staff WHERE staffid = '$id'";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Staff deleted successfully.'); window.location.href = '../staff-list.php';</script>";
} else {
    echo "<script>alert('Error deleting staff: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

require_once 'database-close.php';
?>
