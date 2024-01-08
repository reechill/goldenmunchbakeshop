<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';
$title = 'Order List';

$isStaffOrAdmin = isset($_SESSION['adminid']) || isset($_SESSION['staffid']);
$isCustomer = isset($_SESSION['customerid']);
$isRider = isset($_SESSION['riderid']);

// Determine the SQL query based on the user type
if ($isStaffOrAdmin) {
    // Admin or staff - show all orders with total payments (excluding cancelled payments)
    $sql = "SELECT `order`.*, COALESCE(SUM(CASE WHEN orderpayment.iscancelled = 'n' THEN orderpayment.amount ELSE 0 END), 0) AS amount_paid
            FROM `order`
            LEFT JOIN orderpayment ON `order`.orderid = orderpayment.orderid
            GROUP BY `order`.orderid
            ORDER BY `order`.datetime ASC";
} elseif ($isCustomer) {
    // Customer - show only their orders with total payments (excluding cancelled payments)
    $customerId = $_SESSION['customerid'];
    $sql = "SELECT `order`.*, COALESCE(SUM(CASE WHEN orderpayment.iscancelled = 'n' THEN orderpayment.amount ELSE 0 END), 0) AS amount_paid
            FROM `order`
            LEFT JOIN orderpayment ON `order`.orderid = orderpayment.orderid
            WHERE `order`.customerid = '$customerId'
            GROUP BY `order`.orderid
            ORDER BY `order`.datetime ASC";
} elseif ($isRider) {
    $riderId = $_SESSION['riderid'];

    // Query for rider to show 'Out For Delivery' and 'Delivered' orders handled by them,
    // as well as all 'Ready For Delivery' and 'Failed Delivery' orders
    $sql = "SELECT `order`.*, COALESCE(SUM(CASE WHEN orderpayment.iscancelled = 'n' THEN orderpayment.amount ELSE 0 END), 0) AS amount_paid
            FROM `order`
            LEFT JOIN orderpayment ON `order`.orderid = orderpayment.orderid
            LEFT JOIN (
                SELECT orderid, MAX(datetime) as latest
                FROM orderhistory
                GROUP BY orderid
            ) as latestOrderHistory ON `order`.orderid = latestOrderHistory.orderid
            LEFT JOIN orderhistory AS oh ON `order`.orderid = oh.orderid AND latestOrderHistory.latest = oh.datetime
            WHERE (`order`.status IN ('Ready For Delivery', 'Failed Delivery') 
                   OR (`order`.status = 'Out For Delivery' AND oh.person = '$riderId' AND oh.role = 'rider')
                   OR (`order`.status = 'Delivered' AND oh.person = '$riderId' AND oh.role = 'rider'))
            GROUP BY `order`.orderid
            ORDER BY `order`.datetime ASC";
} else {
    // No valid session, redirect to login
    header('Location: index.php');
    exit();
}


$result = mysqli_query($conn, $sql);

require_once 'template/header.php';
?>

<div class="container mt-5">
    <h1 class="text-center my-5">Order List</h1>
    <?php 
    $statuses = ['Waiting For Confirmation', 'For Payment', 'For Processing', 'On Queue', 'Currently Processing', 'Ready For Pick-Up', 'Ready For Delivery', 'Out For Delivery', 'Picked-Up', 'Delivered', 'Failed Delivery', 'Cancelled'];
    foreach ($statuses as $status) {
        // Check if there are orders in this status
        $hasOrders = false;
        mysqli_data_seek($result, 0);
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] == $status) {
                $hasOrders = true;
                break;
            }
        }

        if ($hasOrders) {
            echo "<h3>" . ucfirst($status) . "</h3>";
            echo "<table class='table'>";
            echo "<thead><tr><th>Order ID</th><th>Customer Name</th><th>Date & Time</th><th class='text-end'>Grand Total</th>";
            if ($status !== 'Waiting For Confirmation') {
                echo "<th class='text-end'>Amount Paid</th><th class='text-end'>Balance</th>";
            }
            echo "<th>Actions</th></tr></thead>";
            echo "<tbody>";

            mysqli_data_seek($result, 0);
            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['status'] == $status) {
                    $balance = $row['total'] - $row['amount_paid'];
                    $paidPercentage = $row['total'] > 0 ? ($row['amount_paid'] / $row['total']) * 100 : 0;

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['orderid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['customer']) . "</td>";
                    echo "<td>" . (new DateTime($row['datetime']))->format('F j, Y, g:i A') . "</td>";
                    echo "<td class='text-end'>&#x20B1;" . number_format($row['total'], 2) . "</td>";
                    if ($status !== 'Waiting For Confirmation') {
                        echo "<td class='text-end'>&#x20B1;" . number_format($row['amount_paid'], 2) . " (" . number_format($paidPercentage, 2) . "%)</td>";
                        echo "<td class='text-end'>&#x20B1;" . number_format($balance, 2) . "</td>";
                    }
                    echo "<td><a href='order-view.php?id=" . $row['orderid'] . "' class='btn btn-primary'>View</a></td>";
                    echo "</tr>";
                }
            }
            echo "</tbody></table>";
        }
    }
    ?>

</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
