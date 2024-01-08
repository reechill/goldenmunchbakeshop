<?php 
require_once 'code/session-start.php'; 

// Redirect to login if no valid session for admin, staff, or rider is found
if (!isset($_SESSION['adminid']) && !isset($_SESSION['staffid']) && !isset($_SESSION['riderid'])) {
    header('Location: store-login.php');
    exit(); // Ensure no further code is executed before redirect happens
}

$title = 'Store Dashboard'; 
require_once 'template/header.php'; 
?>

<style>
  body {
    background-image: url('img/background.jpg');
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
    height: 60vh; /* This ensures the container takes the full height of the viewport */
  }

  .dashboard-content {
    padding: 20px;
  }

</style>

<div class="col-lg-12 m-auto text-center dashboard-container">
    <div class="dashboard-content">
        <!-- Add your content on top of the background image -->
        <h1 class="text-center my-5" style="color: black; border: 2px solid white; background-color: orange; padding: 1px;">Store Dashboard</h1> 
    </div>
</div>


<?php require_once 'template/footer.php'; ?>
