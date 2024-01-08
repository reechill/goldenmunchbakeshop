<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnUpdate'])) {
    $customerid = mysqli_real_escape_string($conn, $_POST['customerid']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $contactnumber = mysqli_real_escape_string($conn, $_POST['contactnumber']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $isAdmin = isset($_SESSION['adminid']);
    $isCustomer = isset($_SESSION['customerid']) && $_SESSION['customerid'] == $customerid;

    if (!$isAdmin && !$isCustomer) {
        echo "<script> alert('Unauthorized access.'); window.history.back(); </script>";
        exit();
    }

    $checkUser = "SELECT * FROM customer WHERE username='$username' AND customerid != '$customerid'";
    $resultCheck = mysqli_query($conn, $checkUser);
    if (mysqli_num_rows($resultCheck) > 0) {
        echo "<script> alert('Username already exists.'); window.history.back(); </script>";
        exit();
    }

    $updateQuery = "UPDATE customer SET username='$username', contactnumber='$contactnumber', address='$address'";
    if ($isAdmin) {
        $updateQuery .= ", name='$name'";
    }
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery .= ", password='$passwordHash'";
    }
    $updateQuery .= " WHERE customerid='$customerid'";

    if (mysqli_query($conn, $updateQuery)) {
        if ($isCustomer) {
            echo "<script> alert('Profile updated successfully.'); window.location.href = '../customer-edit.php'; </script>";
        }
        else if ($isAdmin) {
            echo "<script> alert('Profile updated successfully.'); window.location.href = '../customer-edit.php?id=$customerid'; </script>";
        }
    } else {
        echo "<script> alert('Error updating profile: " . mysqli_error($conn) . "'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
