<?php
require_once 'session-start.php';
require_once 'database-connect.php';

if (isset($_POST['btnLogin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM customer WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['customerid'] = $row['customerid'];
            $_SESSION['name'] = $row['name'];
            
            // Check if redirect parameter is set
            //$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../cart-readymade-list.php';
            $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '../order-list.php';
            header("Location: $redirect"); // Redirect to the specified page
        } else {
            echo "<script> alert('Incorrect password.'); window.history.back(); </script>";
        }
    } else {
        echo "<script> alert('Username does not exist.'); window.history.back(); </script>";
    }
}

require_once 'database-close.php';
?>
