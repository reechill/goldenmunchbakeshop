<?php
require_once 'sms-send.php';
require_once 'session-start.php';
require_once 'database-connect.php';
require_once 'xendit.php'; // Include Xendit configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = isset($_POST['orderid']) ? $_POST['orderid'] : '';
    $method = isset($_POST['method']) ? mysqli_real_escape_string($conn, trim($_POST['method'])) : '';
    $amount = isset($_POST['amount']) ? doubleval($_POST['amount']) : 0.0;
    $additionalnote = isset($_POST['additionalnote']) ? mysqli_real_escape_string($conn, trim($_POST['additionalnote'])) : '';

    // Basic validation
    if ($orderId == 0 || empty($method) || $amount <= 0) {
        echo "<script> alert('Unauthorized action.'); window.history.back(); </script>";
        exit;
    }

    // If the payment method is GCash, initiate Xendit payment
    if ($method == 'gcash') {
        $currency = 'PHP';
        $checkoutMethod = 'ONE_TIME_PAYMENT';
        $channelCode = 'PH_GCASH';
        $redirectSuccessUrl = 'https://goldenmunchbakeshop.com/order-view.php?id=' . $orderId;
        $redirectFailureUrl = 'https://goldenmunchbakeshop.com/order-view.php?id=' . $orderId;

        $data = [
            'reference_id' => $orderId,
            'currency' => $currency,
            'amount' => $amount,
            'checkout_method' => $checkoutMethod,
            'channel_code' => $channelCode,
            'channel_properties' => [
                'success_redirect_url' => $redirectSuccessUrl,
                'failure_redirect_url' => $redirectFailureUrl
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.xendit.co/ewallets/charges',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $xenditApiKey . ':',
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
            exit;
        } else {
            $result = json_decode($response, true);

            // Redirect to the appropriate URL based on the response
            if (isset($result['actions']['desktop_web_checkout_url'])) {
                header('Location: ' . $result['actions']['desktop_web_checkout_url']);
                exit;
            } elseif (isset($result['actions']['mobile_web_checkout_url'])) {
                header('Location: ' . $result['actions']['mobile_web_checkout_url']);
                exit;
            } else {
                echo 'Error initiating payment: No valid checkout URL found.';
                exit;
            }
        }
    } else {
        // For cash payments, directly insert into orderpayment and send SMS
        $insertQuery = "INSERT INTO orderpayment (orderid, method, amount, additionalnote) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, 'isds', $orderId, $method, $amount, $additionalnote);

        if (mysqli_stmt_execute($stmt)) {
            // Fetch customer contact number and send SMS
            $customerQuery = "SELECT contactnumber FROM `order` WHERE orderid = $orderId";
            $customerResult = mysqli_query($conn, $customerQuery);
            if ($row = mysqli_fetch_assoc($customerResult)) {
                $customerContact = $row['contactnumber'];
                $smsMessage = "Golden Munch Bakeshop Order Update:\n\nPayment received: PHP " . number_format($amount, 2) . " for Order ID: $orderId. Method: $method. " . ($additionalnote ? "Note: $additionalnote" : "");
                sendSMS($customerContact, $smsMessage);
            }

            header('Location: ../order-view.php?id=' . $orderId);
            exit();
        } else {
            echo "<script> alert('Error: ". mysqli_error($conn) ."'); window.history.back(); </script>";
        }
    }
} else {
    echo "<script> alert('Unauthorized action.'); window.history.back(); </script>";
    exit();
}

mysqli_close($conn);
?>
