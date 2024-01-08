<?php
require_once 'database-connect.php';
require_once 'xendit.php'; // Include Xendit configuration

// Assume you get the charge ID and order ID from the query parameters
$chargeId = $_GET['id'] ?? '';
$orderId = $_GET['order_id'] ?? 0;

if ($chargeId && $orderId) {
    // Function to make a GET request to Xendit API to check the payment status
    function getXenditPaymentFailure($chargeId, $apiKey) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.xendit.co/ewallets/charges/" . $chargeId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic " . base64_encode($apiKey . ":")
            ]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    // Get payment status
    $paymentStatus = getXenditPaymentFailure($chargeId, $xenditApiKey);

    if ($paymentStatus && $paymentStatus['status'] === 'FAILED') {
        // Log the failure or notify the admin if necessary
        // For now, just displaying a message
        echo "<script>alert('Payment failed. Please try again.'); window.location.href='../order-view.php?orderid=" . $orderId . "';</script>";
    } else {
        echo "<script>alert('Unexpected error occurred. Please contact support.'); window.location.href='../order-view.php?orderid=" . $orderId . "';</script>";
    }
} else {
    echo "<script>alert('No charge ID or order ID provided.'); window.location.href='../order-view.php';</script>";
}

// Close the database connection
mysqli_close($conn);
?>
