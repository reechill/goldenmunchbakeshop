<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnAddRider'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contactnumber = mysqli_real_escape_string($conn, $_POST['contactnumber']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // First, check for existing username
    $checkUsernameQuery = "SELECT * FROM rider WHERE username = '$username'";
    $checkUsernameResult = mysqli_query($conn, $checkUsernameQuery);

    if (mysqli_num_rows($checkUsernameResult) > 0) {
        // Found a duplicate username
        echo "<script>alert('Username already exists.'); window.history.back();</script>";
    } else {
        // Now, check for existing contact number
        $checkContactNumberQuery = "SELECT * FROM rider WHERE contactnumber = '$contactnumber'";
        $checkContactNumberResult = mysqli_query($conn, $checkContactNumberQuery);

        if (mysqli_num_rows($checkContactNumberResult) > 0) {
            // Found a duplicate contact number
            echo "<script>alert('Contact Number already exists.'); window.history.back();</script>";
        } else {
            // No duplicates found, proceed with insertion
            $query = "INSERT INTO rider (name, contactnumber, username, password) VALUES ('$name', '$contactnumber', '$username', '$password')";

            if (mysqli_query($conn, $query)) {
                header('Location: ../rider-list.php');
            } else {
                echo "<script>alert('Error adding new rider.'); window.history.back();</script>";
            }
        }
    }
}

require_once 'database-close.php';
?>
