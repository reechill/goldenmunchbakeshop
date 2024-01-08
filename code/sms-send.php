<?php
    function sendSMS($number, $message, $senderName = 'GOLDENMUNCH') {
        $apiKey = 'eb802bc90a2564e46f5dc4e5ecb6507f'; // Replace with your Semaphore API key
        $apiUrl = 'https://api.semaphore.co/api/v4/messages';

        $parameters = [
            'apikey' => $apiKey,
            'number' => $number,
            'message' => $message,
            'sendername' => $senderName
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        return ['success' => true, 'response' => json_decode($response, true)];
    }
?>