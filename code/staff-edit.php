<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnUpdateStaff'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Start the SQL statement
    $sql = "UPDATE staff SET name = '$name', username = '$username'";

    // Check if a new password was provided
    if (!empty($_POST['password'])) {
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = '$password_hash'";
    }

    // Finalize the SQL statement
    $sql .= " WHERE staffid = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Staff updated successfully.'); window.location.href = '../staff-list.php';</script>";
    } else {
        echo "<script>alert('Error updating staff: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Access denied.'); window.history.back();</script>";
}

require_once 'database-close.php';
?>
