<?php
require_once 'sms-send.php';
require_once 'session-start.php';
require_once 'database-connect.php';

// Check if the customer is logged in
if (!isset($_SESSION['customerid'])) {
    header("Location: login.php");
    exit;
}

$customerId = $_SESSION['customerid'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Calculate the total for ready-made products
    $readyMadeTotalQuery = "SELECT SUM(productreadymade.price * cartproductreadymade.quantity) AS total 
                            FROM cartproductreadymade 
                            JOIN productreadymade ON cartproductreadymade.productreadymadeid = productreadymade.productreadymadeid 
                            WHERE cartproductreadymade.customerid = '$customerId'";
    $readyMadeResult = mysqli_query($conn, $readyMadeTotalQuery);
    $readyMadeTotal = mysqli_fetch_assoc($readyMadeResult)['total'];

    // Calculate the total for customized products
    $customizedTotalQuery = "SELECT SUM((flavor.price + shape.price + size.price) * cartproductcustomized.quantity) AS total 
                             FROM cartproductcustomized 
                             JOIN flavor ON cartproductcustomized.flavorid = flavor.flavorid 
                             JOIN shape ON cartproductcustomized.shapeid = shape.shapeid 
                             JOIN size ON cartproductcustomized.sizeid = size.sizeid 
                             WHERE cartproductcustomized.customerid = '$customerId'";
    $customizedResult = mysqli_query($conn, $customizedTotalQuery);
    $customizedTotal = mysqli_fetch_assoc($customizedResult)['total'];

    // Grand total of the order
    $grandTotal = $readyMadeTotal + $customizedTotal;

    // Prepare the base insert query
    $insertOrderQuery = "INSERT INTO `order` (customerid, customer, contactnumber, additionalnote, deliveryoption, address, total";
    $valuesPlaceholder = "VALUES (?, ?, ?, ?, ?, ?, '$grandTotal'";
    $bindTypes = 'ssssss'; // Base types
    $bindValues = [&$customerId, &$_POST['customerName'], &$_POST['contactNumber'], &$_POST['additionalNotes'], &$_POST['deliveryOption'], &$_POST['deliveryAddress']];

    // Check if 'neededDateTime' is provided and not empty
    if (!empty($_POST['neededDateTime'])) {
        $insertOrderQuery .= ", datetimeneeded";
        $valuesPlaceholder .= ", ?";
        $bindTypes .= 's'; // Add a type for the additional parameter
        $bindValues[] = &$_POST['neededDateTime']; // Add the additional value
    }

    // Complete the insert query
    $insertOrderQuery .= ") " . $valuesPlaceholder . ")";

    $stmt = mysqli_prepare($conn, $insertOrderQuery);
    
    array_unshift($bindValues, $bindTypes); // Add $bindTypes at the beginning of $bindValues
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bindValues));
    
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($conn);

    // Copy Ready-Made Cart Items to Order Details
    $copyReadyMadeQuery = "INSERT INTO orderdetailreadymade (orderid, productreadymadeid, priceatorder, quantity) 
                            SELECT '$orderId', cartproductreadymade.productreadymadeid, productreadymade.price, cartproductreadymade.quantity 
                            FROM cartproductreadymade 
                            JOIN productreadymade ON cartproductreadymade.productreadymadeid = productreadymade.productreadymadeid 
                            WHERE cartproductreadymade.customerid = '$customerId'";
    mysqli_query($conn, $copyReadyMadeQuery);

    // Copy Customized Cart Items to Order Details
    $copyCustomizedQuery = "INSERT INTO orderdetailcustomized (orderid, flavorid, priceatorderflavor, shapeid, priceatordershape, sizeid, priceatordersize, quantity, additionalnote) 
                            SELECT '$orderId', 
                                cartproductcustomized.flavorid, flavor.price, 
                                cartproductcustomized.shapeid, shape.price, 
                                cartproductcustomized.sizeid, size.price, 
                                cartproductcustomized.quantity, 
                                cartproductcustomized.additionalnote
                            FROM cartproductcustomized 
                            JOIN flavor ON cartproductcustomized.flavorid = flavor.flavorid 
                            JOIN shape ON cartproductcustomized.shapeid = shape.shapeid 
                            JOIN size ON cartproductcustomized.sizeid = size.sizeid 
                            WHERE cartproductcustomized.customerid = '$customerId'";
    mysqli_query($conn, $copyCustomizedQuery);

    // Clear Cart After Copying
    $clearCartQuery = "DELETE FROM cartproductreadymade WHERE customerid = '$customerId';
                       DELETE FROM cartproductcustomized WHERE customerid = '$customerId';";
    if (mysqli_multi_query($conn, $clearCartQuery)) {
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));
    } else {
        throw new Exception("Error clearing cart: " . mysqli_error($conn));
    }

    // Commit transaction
    mysqli_commit($conn);

    //  send SMS notification about the details of the orde// Get customer name and order details
    $customerName = $_POST['customerName'];
    $deliveryOption = $_POST['deliveryOption'];
    $additionalNote = $_POST['additionalNotes'];
    $neededDateTime = $_POST['neededDateTime'] ? (new DateTime($_POST['neededDateTime']))->format('F j, Y, g:i A') : 'as soon as possible';

    // Compose the message
    $message = "Hi $customerName, thank you for your order with Golden Munch Bake Shop!\n\nOrder ID: $orderId.\nTotal Amount: Php" . number_format($grandTotal, 2) . ".\nDelivery Option: $deliveryOption.\nDate/Time Needed: $neededDateTime.\nNote: $additionalNote.\n\nWe will update you once your order is ready.";
    $contactNumber = $_POST['contactNumber'];
    sendSMS($contactNumber, $message);

    // Redirect to a confirmation page or display a success message
    header("Location: ../order-list.php?orderid=$orderId");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Order Failed: " . $e->getMessage();
}

require_once 'database-close.php';
?>
