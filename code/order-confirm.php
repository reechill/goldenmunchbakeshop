<?php
require_once 'database-connect.php';
require_once 'sms-send.php';
require_once 'xendit.php'; // Include Xendit configuration

// Function to get data from the webhook payload
function getWebhookData() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

// Function to insert GCash payment details into the database
function insertGCashPayment($conn, $orderId, $amount) {
    $insertPaymentQuery = "INSERT INTO orderpayment (orderid, method, amount) VALUES (?, 'gcash', ?)";
    $stmt = mysqli_prepare($conn, $insertPaymentQuery);
    mysqli_stmt_bind_param($stmt, 'id', $orderId, $amount);
    return mysqli_stmt_execute($stmt);
}

// Function to get customer contact number
function getCustomerContactFromOrderId($conn, $orderId) {
    $query = "SELECT contactnumber FROM `order` WHERE orderid = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $orderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['contactnumber'] ?? '';
}

// Extracting data from the webhook payload
$webhookData = getWebhookData();
$chargeStatus = $webhookData['data']['status'] ?? '';
$chargeId = $webhookData['data']['id'] ?? '';
$referenceId = $webhookData['data']['reference_id'] ?? '';
$chargeAmount = $webhookData['data']['charge_amount'] ?? 0;

if ($chargeId && $referenceId) {
    $orderId = $referenceId;

    if ($chargeStatus === 'SUCCEEDED') {
        // Insert GCash payment into the database
        if (insertGCashPayment($conn, $orderId, $chargeAmount)) {
            // Send SMS notification
            $customerContact = getCustomerContactFromOrderId($conn, $orderId);
            $smsMessage = "Golden Munch Bakeshop Order Update:\n\nPayment received: PHP " . number_format($chargeAmount, 2) . " for Order ID: $orderId. Method: GCash.";
            sendSMS($customerContact, $smsMessage);
        } else {
            echo "Error processing payment: " . mysqli_error($conn);
        }
    } else {
    }
} else {
    echo 'No charge ID or reference ID provided.';
}

//header("Location: ../order-view.php?id=$orderId");

// Close the database connection
mysqli_close($conn);
?>