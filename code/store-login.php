<?php
    require_once 'session-start.php';
    require_once 'database-connect.php';
    
    if (isset($_POST['btnLogin'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $role = $_POST['role'];
        
        $userIdField = ''; // This will store the user ID field name based on role
        $table = ''; // This will store the table name to check against
        
        // Determine the table and user ID field based on the role
        if ($role === 'admin') {
            $table = 'admin';
            $userIdField = 'adminid';
        } elseif ($role === 'staff') {
            $table = 'staff';
            $userIdField = 'staffid';
        } elseif ($role === 'rider') {
            $table = 'rider';
            $userIdField = 'riderid';
        }
        
        if ($table) {
            $sql = "SELECT * FROM $table WHERE username='$username'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                
                $id = $row[$userIdField];
                $password_hash = $row['password'];
                
                if (password_verify($password, $password_hash)) {
                    // start session with role-based ID and common name
                    $_SESSION[$userIdField] = $id;
                    $_SESSION['name'] = $row['name'];

                    // include the contact number of the rider
                    if ($role === 'rider') {
                        $_SESSION['contactnumber'] = $row['contactnumber'];
                    }
                    
                    // Check for redirect URL
                    $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '../store-dashboard.php';
                    header("Location: $redirect"); // Redirect to the provided URL or default page
                    exit();
                } else {
                    echo "<script> alert('Invalid password.'); window.history.back(); </script>";
                }
            } else {
                echo "<script> alert('Username does not exist.'); window.history.back(); </script>";
            }
        } else {
            echo "<script> alert('Invalid role selected.'); window.history.back(); </script>";
        }
    } else {
        echo "<script> alert('Access denied.'); window.history.back(); </script>";
    }
    
    require_once 'database-close.php';
?>
