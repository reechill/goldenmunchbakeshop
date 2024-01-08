<?php require_once 'code/session-start.php'; ?>

<?php
// Redirect to store dashboard if logged in as either admin, staff, or rider
if (isset($_SESSION['adminid']) || isset($_SESSION['staffid']) || isset($_SESSION['riderid'])) {    
    header('Location: store-dashboard.php');
    exit(); // Ensure no further code is executed before redirect happens
}
else if (isset($_SESSION['customerid'])) {    
    header('Location: cart-readymade-list.php');
    exit(); // Ensure no further code is executed before redirect happens
}
?>

<?php $title = 'Welcome to Golden Munch Bakeshop'; ?>

<?php require_once 'template/header.php'; ?>

<style>
  body {
    background-image: url('img/homep.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    margin: 0;
    padding: 0;
  }

  .dashboard-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh; /* This ensures the container takes the full height of the viewport */
  }

  .dashboard-content {
    padding: 20px;
  }

  /* Add the following style for the heading */
  h1 {
    font-weight: bold;
    text-shadow: 2px 2px 4px black;
  }

</style>

<div class="row">
    <div class="col-lg-12 text-center">
        <h1 class="text-center my-5"><b>Welcome to Golden Munch Bakeshop</h1>
        <div class="text-center">
            <a href="customer-login.php" class="btn btn-primary">Customer Login</a> 
            <a href="store-login.php" class="btn btn-warning">Store Login</a>
        </div>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
