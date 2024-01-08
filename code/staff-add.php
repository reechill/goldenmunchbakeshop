<?php
    require_once 'session-start.php';
    require_once 'database-connect.php';
    
    if ( isset($_POST['btnAddStaff']) )
    {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into staff table
        $sql = "INSERT INTO staff (name, username, password) VALUES ('$name', '$username', '$password_hash')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script> alert('Staff added successfully.'); window.location.href = '../staff-list.php'; </script>";
        } else {
            echo "<script> alert('Error adding staff: " . mysqli_error($conn) . "'); window.history.back(); </script>";
        }
    }
    else
    {
        echo "<script> alert('Access denied.'); window.history.back(); </script>";
    }
    
    require_once 'database-close.php';
?>
