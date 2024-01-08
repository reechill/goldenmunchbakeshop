<?php
require_once 'sms-send.php';
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnRegister'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $contactnumber = mysqli_real_escape_string($conn, $_POST['contactnumber']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Check for duplicate username or contact number
    $checkUser = "SELECT * FROM customer WHERE username='$username'";
    $resultCheck = mysqli_query($conn, $checkUser);
    if (mysqli_num_rows($resultCheck) > 0) {
        echo "<script> alert('Username already exists.'); window.history.back(); </script>";
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO customer (name, username, password, contactnumber, address) VALUES ('$name', '$username', '$passwordHash', '$contactnumber', '$address')";

    if (mysqli_query($conn, $sql)) {
        $message = "Welcome to GOLDEN MUNCH BAKESHOP.\n\nHere are your details:\nName: $name\nUsername: $username\nAddress: $address";
        sendSMS($contactnumber, $message);
        echo "<script> alert('Registration successful.'); window.location.href = '../customer-login.php'; </script>";
    } else {
        echo "<script> alert('Error: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
