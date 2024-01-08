<?php
    $hostname = 'localhost';
    
    //  for server (Golden Munch Online Ordering)
    $user     = 'vbxqsvvqad';
    $password = 'TR7JN9kKCr';
    $database = 'vbxqsvvqad';
     
     //  for localhost (Golden Munch Online Ordering)
    //  $user     = 'root';
    //  $password = '';
    //  $database = 'vbxqsvvqad';

    //  for server for Golden Munch Pay
    // $user     = 'enfmsnhxye';
    // $password = 'v4WbcYUwVh';
    // $database = 'enfmsnhxye';
    
    //  for localhost for Golden Munch Pay
    // $user     = 'root';
    // $password = '';
    // $database = 'enfmsnhxye';

    $conn = mysqli_connect($hostname, $user, $password, $database);
    
    if ( ! $conn )
    {
        echo "<script> alert('Error connecting to the database.'); </script>";
    }
