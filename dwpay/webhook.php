<?php
// Capture the incoming data (for application/x-www-form-urlencoded)
$incomingData = $_POST;

// Log the incoming data for debugging
$logFile = 'webhook_log.txt';
$logData = "Incoming Data: " . print_r($incomingData, true) . "\n";

// Check if the status is 'Credit'
if (isset($incomingData['status']) && $incomingData['status'] === 'Credit') {
    
    // Parse the 'purpose' field and assign to respective variables
    if (isset($incomingData['purpose'])) {
        $purposeParts = explode('-', $incomingData['purpose']);
        
        $user_id = isset($purposeParts[0]) ? $purposeParts[0] : null;
        $address_id = isset($purposeParts[1]) ? $purposeParts[1] : null;
        $product_id = isset($purposeParts[2]) ? $purposeParts[2] : null;
        $quantity = isset($purposeParts[3]) ? $purposeParts[3] : null;

        // Log the parsed data
        $logData .= "Parsed Purpose Data: \n";
        $logData .= "User ID: $user_id\n";
        $logData .= "Address ID: $address_id\n";
        $logData .= "Product ID: $product_id\n";
        $logData .= "Quantity: $quantity\n";

        // Call the external API with form data if status is Credit
        $apiUrl = 'https://testgmix.graymatterworks.com/api/place_order';

        $formData = [
            'user_id' => $user_id,
            'address_id' => $address_id,
            'product_id' => $product_id,
            'payment_mode' => 'Prepaid',
            'quantity' => $quantity
        ];

        // Initialize cURL
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));

        // Execute the request and get the response
        $apiResponse = curl_exec($ch);
        curl_close($ch);

        // Log API response
        $logData .= "API Response: " . $apiResponse . "\n";
    }

    // Write to log file
    file_put_contents($logFile, $logData, FILE_APPEND);

    // Respond with success message
    header('Content-Type: application/json');
    $response = [
        'status' => 'success',
        'message' => 'Webhook received, status is Credit, purpose parsed, order placed, and logged',
        'parsed_data' => [
            'user_id' => $user_id,
            'address_id' => $address_id,
            'product_id' => $product_id,
            'quantity' => $quantity
        ]
    ];
    echo json_encode($response);

} else {
    // If status is not 'Credit', log and respond with error
    $logData .= "Error: Invalid status or status not Credit\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    
    header('Content-Type: application/json');
    $response = [
        'status' => 'error',
        'message' => 'Invalid status or status not Credit'
    ];
    echo json_encode($response);
}
