<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';
$title = 'Sales Report';

// Default date range: from the start date to the end of that day
$startDate = $_GET['start'] ?? date('Y-m-01');
$endDate = $_GET['end'] ?? date('Y-m-t');

// If start date and end date are the same, adjust the end date to include the entire day
if ($startDate == $endDate) {
    $endDate .= ' 23:59:59';
}

// SQL Query
$sql = "SELECT `order`.orderid, `order`.datetime, `order`.status, `order`.total,
               COALESCE(SUM(CASE WHEN orderpayment.method = 'cash' AND orderpayment.iscancelled = 'n' THEN orderpayment.amount ELSE 0 END), 0) AS cash_paid,
               COALESCE(SUM(CASE WHEN orderpayment.method = 'gcash' AND orderpayment.iscancelled = 'n' THEN orderpayment.amount ELSE 0 END), 0) AS gcash_paid
        FROM `order`
        LEFT JOIN orderpayment ON `order`.orderid = orderpayment.orderid
        WHERE `order`.status IN ('Delivered', 'Picked-Up')
        AND `order`.datetime BETWEEN '$startDate' AND '$endDate'
        GROUP BY `order`.orderid
        ORDER BY `order`.datetime ASC";

$result = mysqli_query($conn, $sql);

$totalOrders = 0;
$totalCash = 0;
$totalGCash = 0;
$totalPayments = 0;

require_once 'template/header.php';
?>

<div class='container mt-5'>
    <h1 class='text-center my-5'>Sales Report</h1>
    
    <!-- Date Selection Form -->
    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="row mb-4">
            <div class="col">
                <input type="date" class="form-control" name="start" value="<?php echo $startDate; ?>">
            </div>
            <div class="col">
                <input type="date" class="form-control" name="end" value="<?php echo $endDate; ?>">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary d-print-none">Filter</button>
                <a class="btn btn-success d-print-none" onclick="window.print();return false;">Print</a>
            </div>
        </div>
    </form>

    <!-- Report Table -->
    <table class='table table-sm'>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th class='text-end'>Total</th>
                <th class='text-end'>Cash Paid</th>
                <th class='text-end'>GCash Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['orderid']); ?></td>
                    <td><?php echo (new DateTime($row['datetime']))->format('F j, Y, g:i A'); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td class='text-end'>&#x20B1;<?php echo number_format($row['total'], 2); ?></td>
                    <td class='text-end'>&#x20B1;<?php echo number_format($row['cash_paid'], 2); ?></td>
                    <td class='text-end'>&#x20B1;<?php echo number_format($row['gcash_paid'], 2); ?></td>
                </tr>
                <?php
                $totalOrders += $row['total'];
                $totalCash += $row['cash_paid'];
                $totalGCash += $row['gcash_paid'];
                $totalPayments += $row['cash_paid'] + $row['gcash_paid'];
                ?>
            <?php } ?>
        </tbody>
    </table>

    <!-- Payment Summary -->
    <div class="row mt-4">
        <div class="col">
            <h4 class="mb-4">Total Orders: &#x20B1;<?php echo number_format($totalOrders, 2); ?></h4>
            <h4 class="mb-4">Total Payments: &#x20B1;<?php echo number_format($totalPayments, 2); ?></h4>
            <p>Cash: &#x20B1;<?php echo number_format($totalCash, 2); ?></p>
            <p>GCash: &#x20B1;<?php echo number_format($totalGCash, 2); ?></p>
        </div>
    </div>
</div>

<?php
require_once 'template/footer.php';
require_once 'code/database-close.php';
?>
