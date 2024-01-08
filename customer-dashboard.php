<?php 
require_once 'code/session-start.php'; 

// Redirect to login if no valid session for customer is found
if (!isset($_SESSION['customerid'])) {
    header('Location: customer-login.php');
    exit(); // Ensure no further code is executed before redirect happens
}

$title = 'Customer Dashboard'; 
require_once 'template/header.php'; 
?>

<div class="row">
    <div class="col-lg-12 m-auto">
        <h1 class="text-center my-5">Customer Dashboard</h1>
    </div>
</div>

<?php require_once 'template/footer.php'; ?>
