<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

$title = 'Add Payment';
$orderId = $_GET['orderid'] ?? 0; // Get order ID from the URL

// Check if the user is logged in as customer or as admin/staff/rider
$isCustomer = isset($_SESSION['customerid']);
$isAdmin = isset($_SESSION['adminid']);
$isStaff = isset($_SESSION['staffid']);
$isRider = isset($_SESSION['riderid']);

// Redirect to the appropriate login page if not logged in
if (!$isCustomer && !$isAdmin && !$isStaff && !$isRider) {
    $loginPage = $isCustomer ? 'customer-login.php' : 'store-login.php';
    $redirectUrl = urlencode("../payment-add.php?orderid=$orderId");
    header("Location: $loginPage?redirect=$redirectUrl");
    exit();
}

// Fetch order and payment details
$orderQuery = "SELECT * FROM `order` WHERE orderid = '$orderId'";
$orderResult = mysqli_query($conn, $orderQuery);
$orderInfo = mysqli_fetch_assoc($orderResult);

$paymentQuery = "SELECT SUM(amount) AS total_paid FROM orderpayment WHERE orderid = '$orderId' AND iscancelled != 'y'";
$paymentResult = mysqli_query($conn, $paymentQuery);
$paymentInfo = mysqli_fetch_assoc($paymentResult);

$totalPaid = $paymentInfo['total_paid'] ?? 0;
$balance = $orderInfo['total'] - $totalPaid;
$paidPercentage = $orderInfo['total'] > 0 ? ($totalPaid / $orderInfo['total']) * 100 : 0;

require_once 'template/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Add Payment</h1>

    <!-- Order Summary -->
    <div class="mb-4">
        <h3>Order Summary</h3>
        <p>Order ID: <?= htmlspecialchars($orderId); ?></p>
        <p>Total Amount: &#x20B1;<?= number_format($orderInfo['total'], 2); ?></p>
        <p>Amount Paid: &#x20B1;<?= number_format($totalPaid, 2); ?> (<?= number_format($paidPercentage, 2); ?>%)</p>
        <p>Balance: &#x20B1;<?= number_format($balance, 2); ?></p>
    </div>


    <form action="code/payment-add.php" method="POST">
        <input type="hidden" name="orderid" value="<?= $orderId; ?>">

        <div class="mb-3">
            <label for="method" class="form-label">Payment Method:</label>
            <select class="form-control" id="method" name="method" required>
                <option value="">-Select Method-</option>
                <?php if ($isAdmin || $isStaff || $isRider) : ?>
                <option value="cash">Cash</option>
                <?php endif; ?>
                <?php if ($isCustomer) : ?>
                <option value="gcash">Gcash</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="additionalnote" class="form-label">Notes:</label>
            <textarea class="form-control" id="additionalnote" name="additionalnote" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Payment</button>
    </form>
</div>

<?php 
    require_once 'template/footer.php'; 
?>
