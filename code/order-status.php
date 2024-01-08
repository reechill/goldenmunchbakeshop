<?php
require_once 'sms-send.php';
require_once 'session-start.php';
require_once 'database-connect.php';

// Check if user is logged in as admin, staff, or customer
$isAdmin = isset($_SESSION['adminid']);
$isStaff = isset($_SESSION['staffid']);
$isCustomer = isset($_SESSION['customerid']);
$isRider = isset($_SESSION['riderid']);

if (!($isAdmin || $isStaff || $isCustomer || $isRider)) {
    header("Location: index.php");
    exit;
}

// Extract POST data
$orderId = $_POST['orderid'] ?? null;
$currentStatus = $_POST['currentstatus'] ?? null;
$newStatus = $_POST['status'] ?? null;
$handledBy = $isRider ? $_SESSION['riderid'] : ($isAdmin ? $_SESSION['adminid'] : ($isStaff ? $_SESSION['staffid'] : $_SESSION['customerid']));
$role = $isRider ? 'rider' : ($isAdmin ? 'admin' : ($isStaff ? 'staff' : 'customer'));

// Additional fields for specific statuses
$dateTimeOnQueue = $_POST['datetimeonqueue'] ?? null;
$dateTimeDelivered = $_POST['datetimedelivered'] ?? null;
$deliveryFailureNote = $_POST['deliveryfailurenote'] ?? null;
$cancellationNote = $_POST['cancellationreason'] ?? null;

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update order status
    $updateOrderQuery = "UPDATE `order` SET status = '$newStatus' WHERE orderid = $orderId";
    mysqli_query($conn, $updateOrderQuery);

    // Special handling for specific statuses
    if ($newStatus === 'On Queue') {
        $updateDateTimeOnQueueQuery = "UPDATE `order` SET datetimeonqueue = '$dateTimeOnQueue' WHERE orderid = $orderId";
        mysqli_query($conn, $updateDateTimeOnQueueQuery);
    } elseif (in_array($newStatus, ['Picked-Up', 'Delivered', 'Failed Delivery'])) {
        $updateDateTimeDeliveredQuery = "UPDATE `order` SET datetimedeliveredpickedup = '$dateTimeDelivered' WHERE orderid = $orderId";
        mysqli_query($conn, $updateDateTimeDeliveredQuery);
    }

    // File upload handling for 'Delivered' status
    if ($newStatus === 'Delivered' && isset($_FILES['deliveryproof'])) {
        $file = $_FILES['deliveryproof'];

        // File upload error handling
        if ($file['error'] != 0) {
            throw new Exception("Error in file upload");
        }

        // Validate file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > 5000000) { // 5MB limit
            throw new Exception("Invalid file type or size");
        }

        // Generate unique file name
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = "delivery_" . $orderId . "_" . time() . "." . $extension;
        $destination = '../img/upload/delivery/' . $newFileName;

        // Move the file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to save file");
        }

        // Update order with image file name
        $updateImageQuery = "UPDATE `order` SET imagefilename = '$newFileName' WHERE orderid = $orderId";
        mysqli_query($conn, $updateImageQuery);
    }

    // Insert into orderhistory
    $historyNote = '';
    if ($newStatus === 'Failed Delivery') {
        $historyNote = 'Rider: ' . $_SESSION['name'] . '. Reason: ' . $deliveryFailureNote . ' Date: ' . (new DateTime($dateTimeDelivered))->format('F j, Y, g:i A');
    }
    else if ($newStatus === 'Cancelled') {
        $historyNote = 'Cancelled by: ' . $_SESSION['name'] . '. Reason: ' . $cancellationNote;
    }
    else if ($newStatus === 'Out For Delivery') {
        $historyNote = 'Rider: ' . $_SESSION['name'] . ' (' . $_SESSION['contactnumber'] . ')';
    }
    else if (in_array($newStatus, ['Picked-Up', 'Delivered'])) {
        $historyNote = (new DateTime($dateTimeDelivered))->format('F j, Y, g:i A');
    }

    $insertHistoryQuery = "INSERT INTO orderhistory (orderid, status, person, role, additionalnote) VALUES ($orderId, '$newStatus', $handledBy, '$role', '$historyNote')";
    mysqli_query($conn, $insertHistoryQuery);

    // Commit transaction
    mysqli_commit($conn);

    // Fetch customer contact number for the order
    $contactQuery = "SELECT contactnumber FROM `order` WHERE orderid = $orderId";
    $contactResult = mysqli_query($conn, $contactQuery);
    $contactRow = mysqli_fetch_assoc($contactResult);
    $customerContact = $contactRow['contactnumber'];

    //  send SMS
    $message = "Golden Munch Bakeshop Order Update:\n\nOrder #$orderId\nStatus: $newStatus\nNotes: $historyNote";
    $contactNumber = $customerContact;
    sendSMS($contactNumber, $message);

    // Redirect back to order view or another appropriate page
    header("Location: ../order-view.php?id=$orderId");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Order status update failed: " . $e->getMessage();
}

require_once 'database-close.php';
?>
