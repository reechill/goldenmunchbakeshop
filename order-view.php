<?php
require_once 'code/session-start.php';
require_once 'code/database-connect.php';

// Ensure there's an order ID parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php"); // Redirect to an error page or to the orders list
    exit;
}

$title = 'Order View';
require_once 'template/header.php';

// Fetch order details
$orderId = $_GET['id'];

// Fetch Order Main Details
$orderQuery = "SELECT * FROM `order` WHERE orderid = '$orderId'";
$orderResult = mysqli_query($conn, $orderQuery);
$orderInfo = mysqli_fetch_assoc($orderResult);

// Fetch Ready-Made Order Items
$readyMadeQuery = "SELECT orderdetailreadymade.*, productreadymade.name, productreadymade.imagefilename 
                   FROM orderdetailreadymade 
                   JOIN productreadymade ON orderdetailreadymade.productreadymadeid = productreadymade.productreadymadeid 
                   WHERE orderdetailreadymade.orderid = '$orderId'";
$readyMadeResult = mysqli_query($conn, $readyMadeQuery);

// Fetch Customized Order Items
$customizedQuery = "SELECT orderdetailcustomized.*, 
                           flavor.name as flavorName, flavor.price as flavorPrice, 
                           shape.name as shapeName, shape.price as shapePrice, 
                           size.name as sizeName, size.price as sizePrice 
                    FROM orderdetailcustomized 
                    JOIN flavor ON orderdetailcustomized.flavorid = flavor.flavorid 
                    JOIN shape ON orderdetailcustomized.shapeid = shape.shapeid 
                    JOIN size ON orderdetailcustomized.sizeid = size.sizeid 
                    WHERE orderdetailcustomized.orderid = '$orderId'";
$customizedResult = mysqli_query($conn, $customizedQuery);

// Replace these with the actual checks for admin and staff users in your application
$isAdmin = isset($_SESSION['adminid']);
$isStaff = isset($_SESSION['staffid']);
$isRider = isset($_SESSION['riderid']);

// Check if the user is an admin or staff
$isAdminOrStaff = $isAdmin || $isStaff;
// Check if current user is a customer
$isCustomer = isset($_SESSION['customerid']) && $_SESSION['customerid'] == $orderInfo['customerid'];

// Fetch Order History
$orderHistoryQuery = "SELECT * FROM orderhistory WHERE orderid = " . $orderId . " ORDER BY datetime DESC";
$orderHistoryResult = mysqli_query($conn, $orderHistoryQuery);

// Payment Information
$paymentQuery = "SELECT * FROM orderpayment WHERE orderid = " . $orderId . " ORDER BY datetime DESC";
$paymentResult = mysqli_query($conn, $paymentQuery);
// Calculate total amount paid and balance
$totalAmountPaid = 0;
foreach ($paymentResult as $payment) {
    if ($payment['iscancelled'] != 'y') {
        $totalAmountPaid += $payment['amount'];
    }
}
$totalBalance = $orderInfo['total'] - $totalAmountPaid;
$paidPercentage = $orderInfo['total'] > 0 ? ($totalAmountPaid / $orderInfo['total']) * 100 : 0;


// Check order status and user role to determine which buttons to display
$nextStatusActions = [
    'Waiting For Confirmation' => 'For Payment',
    'For Payment' => 'For Processing',
    'For Processing' => ['On Queue', 'Currently Processing'],
    'On Queue' => 'Currently Processing',
    'Currently Processing' => $orderInfo['deliveryoption'] === 'delivery' ? 'Ready For Delivery' : 'Ready For Pick-Up',
    'Ready For Pick-Up' => 'Picked-Up',
    'Ready For Delivery' => 'Out For Delivery',
    'Out For Delivery' => ['Delivered', 'Failed Delivery'],
    'Failed Delivery' => ['Out For Delivery']
];
?>

<div class="container mt-4">
    <h1 class="text-center my-5">Order Details</h1>

    <!-- Order Main Information -->
    <div class="card mb-4">
        <div class="card-header">Order Information</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Order ID:</strong> <?= htmlspecialchars($orderInfo['orderid']); ?></p>
                    <p><strong>Date/Time Placed:</strong> <?= (new DateTime($orderInfo['datetime']))->format('F j, Y, g:i A'); ?></p>
                    <p><strong>Customer Name:</strong> <?= htmlspecialchars($orderInfo['customer']); ?></p>
                    <p><strong>Contact Number:</strong> <?= htmlspecialchars($orderInfo['contactnumber']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Delivery Option:</strong> <?= htmlspecialchars($orderInfo['deliveryoption']); ?></p>
                    <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($orderInfo['address'])); ?></p>
                    <p><strong>Date/Time Needed:</strong> <?= $orderInfo['datetimeneeded'] ? (new DateTime($orderInfo['datetimeneeded']))->format('F j, Y, g:i A') : 'N/A'; ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($orderInfo['status']); ?></p>
                </div>
            </div>
            <p><strong>Additional Note:</strong> <?= nl2br(htmlspecialchars($orderInfo['additionalnote'])); ?></p>
        </div>
    </div>

    <!-- Ready-Made Products Section -->
    <div class="card mb-4">
        <div class="card-header">Ready-Made Products</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end">Subtotal</th>
                        <?php if ($isCustomer && in_array($orderInfo['status'], ['Delivered', 'Picked-Up'])): ?>
                            <th>Rating (5-highest, 1-lowest)</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $readyMadeTotal = 0;
                    while ($item = mysqli_fetch_assoc($readyMadeResult)) {
                        $imagePath = !empty($item['imagefilename']) ? 'img/upload/readymadeproducts/' . $item['imagefilename'] : 'img/no-image-available.png';
                        $subtotal = $item['quantity'] * $item['priceatorder'];
                        $readyMadeTotal += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($item['name']); ?>" height="150px" width="150px"> 
                                <?= htmlspecialchars($item['name']); ?>
                            </td>
                            <td>&#x20B1;<?= number_format($item['priceatorder'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                            <?php
                                if ($isCustomer && in_array($orderInfo['status'], ['Delivered', 'Picked-Up'])) {
                                    $ratingValue = isset($item['rating']) ? $item['rating'] : ''; // Check if rating exists
                                    echo "<td>
                                            <form action='code/order-rate.php' method='post'>
                                                <input type='hidden' name='orderId' value='{$orderId}'>
                                                <input type='hidden' name='orderDetailId' value='{$item['orderdetailreadymadeid']}'>
                                                <input type='number' name='rating' value='{$ratingValue}' min='0' max='5' step='1' required>
                                                <button type='submit' class='btn btn-primary'>Rate</button>
                                            </form>
                                          </td>";
                                }
                            ?>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">&#x20B1;<?= number_format($readyMadeTotal, 2); ?></td>
                        <?php if ($isCustomer && in_array($orderInfo['status'], ['Delivered', 'Picked-Up'])): ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Customized Products Section -->
    <div class="card mb-4">
        <div class="card-header">Customized Products</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customization</th>
                        <th>Additional Note</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $customizedTotal = 0;
                    while ($item = mysqli_fetch_assoc($customizedResult)) {
                        $subtotal = $item['quantity'] * ($item['priceatorderflavor'] + $item['priceatordershape'] + $item['priceatordersize']);
                        $customizedTotal += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <ul>
                                    <li>Flavor: <?= htmlspecialchars($item['flavorName']) . " - &#x20B1;" . number_format($item['priceatorderflavor'], 2); ?></li>
                                    <li>Shape: <?= htmlspecialchars($item['shapeName']) . " - &#x20B1;" . number_format($item['priceatordershape'], 2); ?></li>
                                    <li>Size: <?= htmlspecialchars($item['sizeName']) . " - &#x20B1;" . number_format($item['priceatordersize'], 2); ?></li>
                                </ul>
                            </td>
                            <td><?= htmlspecialchars($item['additionalnote']); ?></td>
                            <td>&#x20B1;<?= number_format($subtotal / $item['quantity'], 2); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td class="text-end">&#x20B1;<?= number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end">&#x20B1;<?= number_format($customizedTotal, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Overall Total -->
    <div class="text-end mb-4">
        <h3>Overall Total: &#x20B1;<?= number_format($readyMadeTotal + $customizedTotal, 2); ?></h3>
    </div>

    <?php if ($orderInfo['status'] != "Waiting For Confirmation"): ?>
    <!-- Payment Information Section -->
    <div class="card my-4">
        <div class="card-header">Payment Information</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                        <th>Notes</th>
                        <?php if (($totalBalance > 0) && ($isRider && in_array($orderInfo['status'], ['Out For Delivery'])) || $isAdminOrStaff || ($isCustomer && $orderInfo['status'] == "For Payment")): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentResult as $payment): ?>
                        <tr <?php if ($payment['iscancelled'] == 'y') echo 'style="text-decoration: line-through;"'; ?>>
                            <td><?= (new DateTime($payment['datetime']))->format('F j, Y, g:i A'); ?></td>
                            <td><?= ucwords(htmlspecialchars($payment['method'])); ?></td>
                            <td class="text-end">&#x20B1;<?= number_format($payment['amount'], 2); ?></td>
                            <td><?= nl2br(htmlspecialchars($payment['additionalnote'])); ?></td>
                            <td>
                            <?php if ((($isAdminOrStaff && ! in_array($orderInfo['status'], ['Picked-Up', 'Delivered', 'Cancelled']) ) || ($isRider && in_array($orderInfo['status'], ['Out For Delivery'])) || ($isCustomer && $orderInfo['status'] == "For Payment")) && $payment['iscancelled'] != 'y' && ($totalBalance > 0) ): ?>
                                <a href="code/payment-cancel.php?orderid=<?= $orderId; ?>&paymentid=<?= $payment['orderpaymentid']; ?>" class="btn btn-warning" onclick="return confirm('Are you sure you want to cancel this payment?');">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <?php if (($totalBalance > 0) && ($orderInfo['status'] == "For Payment" || ($isRider && in_array($orderInfo['status'], ['Out For Delivery'])))): ?>
                        <?php /**<td colspan="5"><a href="payment-add.php?orderid=<?= $orderId; ?>" class="btn btn-success">Add Payment</a></td>**/ ?>
                        <td colspan="5"><a href="payment-add.php?orderid=<?= $orderId; ?>" class="btn btn-success">Add Payment</a></td>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Amount Paid and Balance -->
    <div class="text-end mb-4">
        <h3>Amount Paid (<?= number_format($paidPercentage, 2); ?>%): &#x20B1;<?= number_format($totalAmountPaid, 2); ?></h3>
        <h3>Balance: &#x20B1;<?= number_format($totalBalance, 2); ?></h3>
    </div>

    <!-- Order History Section -->
    <div class="card mt-4">
        <div class="card-header">Transaction</div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Status</th>
                        <?php if ($isAdminOrStaff || $isRider): ?>
                            <th>Handled By</th>
                            <th>Role</th>
                        <?php endif; ?>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    while ($history = mysqli_fetch_assoc($orderHistoryResult)) {
                        $handledByName = '';
                        switch ($history['role']) {
                            case 'customer':
                                // Fetch admin name
                                $customerQuery = "SELECT name FROM customer WHERE customerid = " . $history['person'];
                                $customerResult = mysqli_query($conn, $customerQuery);
                                $customer = mysqli_fetch_assoc($customerResult);
                                $handledByName = $customer['name'];
                                break;
                            case 'admin':
                                // Fetch admin name
                                $adminQuery = "SELECT name FROM admin WHERE adminid = " . $history['person'];
                                $adminResult = mysqli_query($conn, $adminQuery);
                                $admin = mysqli_fetch_assoc($adminResult);
                                $handledByName = $admin['name'];
                                break;
                            case 'staff':
                                // Fetch staff name
                                $staffQuery = "SELECT name FROM staff WHERE staffid = " . $history['person'];
                                $staffResult = mysqli_query($conn, $staffQuery);
                                $staff = mysqli_fetch_assoc($staffResult);
                                $handledByName = $staff['name'];
                                break;
                            case 'rider':
                                // Fetch rider name
                                $riderQuery = "SELECT name FROM rider WHERE riderid = " . $history['person'];
                                $riderResult = mysqli_query($conn, $riderQuery);
                                $rider = mysqli_fetch_assoc($riderResult);
                                $handledByName = $rider['name'];
                                break;
                        }
                        echo '<tr>';
                        echo '<td>' . (new DateTime($history['datetime']))->format('F j, Y, g:i A') . '</td>';
                        echo '<td>' . htmlspecialchars($history['status']) . '</td>';
                        if ($isAdminOrStaff || $isRider) {
                            echo '<td>' . ucfirst(htmlspecialchars($handledByName)) . '</td>';
                            echo '<td>' . ucfirst(htmlspecialchars($history['role'])) . '</td>';
                        }
                        echo '<td>';
                        echo nl2br(htmlspecialchars($history['additionalnote']));
                        // Check if status is 'Delivered' and display the image if available
                        if ($history['status'] == 'Delivered' && !empty($orderInfo['imagefilename'])) {
                            $deliveryImagePath = 'img/upload/delivery/' . htmlspecialchars($orderInfo['imagefilename']);
                            echo '<br><img src="' . $deliveryImagePath . '" alt="Delivery Proof" style="width: 250px; height: 250px;">';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Order Action Buttons -->
    <?php if (($isRider && in_array($orderInfo['status'], ['Ready For Delivery', 'Out For Delivery', 'Failed Delivery'])) || ($isAdminOrStaff && ! in_array($orderInfo['status'], ['Cancelled', 'Picked-Up', 'Delivered'])) || ($isCustomer && in_array($orderInfo['status'], ['Waiting For Confirmation', 'For Payment']))): ?>
    <div class="card mt-4">
        <div class="card-header">Action</div>
        <div class="card-body">
            <?php if ($isRider || $isAdminOrStaff && isset($nextStatusActions[$orderInfo['status']])): ?>
                <?php 
                $nextActions = (array) $nextStatusActions[$orderInfo['status']];
                foreach ($nextActions as $action): ?>
                    <hr>
                    <form action="code/order-status.php" method="post" enctype="multipart/form-data" class="my-4">
                        <input type="hidden" name="orderid" value="<?= $orderId; ?>">
                        <input type="hidden" name="currentstatus" value="<?= $orderInfo['status']; ?>">

                        <!-- Special cases with additional inputs -->
                        <?php if ($action === 'On Queue'): ?>
                            <?php 
                            $dateTimeNeeded = !empty($orderInfo['datetimeneeded']) ? (new DateTime($orderInfo['datetimeneeded'])) : null;
                            if ($dateTimeNeeded) {
                                $dateTimeNeeded->modify('-2 days');
                                $dateTimeForQueue = $dateTimeNeeded->format('Y-m-d\TH:i');
                            }
                            ?>
                            <input type="datetime-local" name="datetimeonqueue" value="<?= $dateTimeForQueue ?? '' ?>" required class="form-control mb-2">
                            <button type="submit" name="status" value="<?= $action; ?>" class="btn btn-primary">Set On Queue</button>
                            <?php elseif ($action === 'Picked-Up' || $action === 'Delivered' || $action === 'Failed Delivery'): ?>
                                <input type="datetime-local" name="datetimedelivered" required class="form-control mb-2">
                                <?php if ($action === 'Delivered'): ?>
                                    <input type="file" name="deliveryproof" required class="form-control mb-2">
                                <?php endif; ?>
                                <?php if ($action === 'Failed Delivery'): ?>
                                    <textarea name="deliveryfailurenote" placeholder="Reason for failed delivery" required class="form-control mb-2"></textarea>
                                <?php endif; ?>
                                <button type="submit" name="status" value="<?= $action; ?>" class="btn btn-primary"><?= $action; ?></button>
                            <?php else: ?>
                            <button type="submit" name="status" value="<?= $action; ?>" class="btn btn-primary"><?= $action; ?></button>
                        <?php endif; ?>
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (($isCustomer || $isAdminOrStaff) && ! in_array($orderInfo['status'], array('Ready For Pick-Up', 'Ready For Delivery', 'Picked-Up', 'Delivered', 'Failed Delivery', 'Cancelled'))): ?>
                <hr>
                <form action="code/order-status.php" method="post" class="my-4">
                    <input type="hidden" name="orderid" value="<?= $orderId; ?>">
                    <input type="hidden" name="currentstatus" value="<?= $orderInfo['status']; ?>">
                    <textarea name="cancellationreason" placeholder="Reason for cancellation" class="form-control mb-2" required></textarea>
                    <button type="submit" name="status" value="Cancelled" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php 
    require_once 'code/database-close.php';
    require_once 'template/footer.php'; 
?>
