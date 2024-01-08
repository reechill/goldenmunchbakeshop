<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnAddAdmin'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Check if username already exists
    $checkUsername = "SELECT username FROM admin WHERE username = '$username'";
    $checkResult = mysqli_query($conn, $checkUsername);
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Username already exists. Choose a different one.'); window.history.back();</script>";
        exit;
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new admin into database
    $sql = "INSERT INTO admin (name, username, password) VALUES ('$name', '$username', '$password_hash')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('New admin has been added successfully.'); window.location.href = '../store-dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding admin: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Access denied.'); window.history.back();</script>";
}

require_once 'database-close.php';
?>
