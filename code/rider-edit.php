<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnUpdateRider'])) {
    $riderid = mysqli_real_escape_string($conn, $_POST['riderid']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $contactnumber = mysqli_real_escape_string($conn, $_POST['contactnumber']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Check for existing username
    $checkUsernameQuery = "SELECT * FROM rider WHERE username = '$username' AND riderid != $riderid";
    $checkUsernameResult = mysqli_query($conn, $checkUsernameQuery);
    if (mysqli_num_rows($checkUsernameResult) > 0) {
        echo "<script>alert('Username already exists.'); window.history.back();</script>";
        exit;
    }

    // Check for existing contact number
    $checkContactNumberQuery = "SELECT * FROM rider WHERE contactnumber = '$contactnumber' AND riderid != $riderid";
    $checkContactNumberResult = mysqli_query($conn, $checkContactNumberQuery);
    if (mysqli_num_rows($checkContactNumberResult) > 0) {
        echo "<script>alert('Contact number already exists.'); window.history.back();</script>";
        exit;
    }

    // Build the update query
    $updateQuery = "UPDATE rider SET name = '$name', contactnumber = '$contactnumber', username = '$username' ";
    if ($password) {
        $updateQuery .= ", password = '$password'";
    }
    $updateQuery .= " WHERE riderid = $riderid";

    // Execute the update
    if (mysqli_query($conn, $updateQuery)) {
        header('Location: ../rider-list.php');
    } else {
        echo "<script>alert('Error updating rider details.'); window.history.back();</script>";
    }
}

require_once 'database-close.php';
?>
