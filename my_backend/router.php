<?php
// router.php

// Allow requests from any origin (not recommended for production)
header("Access-Control-Allow-Origin: *");

// Allow specific HTTP methods (e.g., GET, POST, PUT, DELETE)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

// Allow specific headers (e.g., Content-Type, Authorization)
header("Access-Control-Allow-Headers: *");

// Set the Content-Type header to JSON
header("Content-Type: application/json");

// Enable credentials (cookies, HTTP authentication)
header("Access-Control-Allow-Credentials: true");

// Set the maximum age for preflight requests (in seconds)
header("Access-Control-Max-Age: 3600");

if (isset($_GET['endpoint'])) {
    $endpoint = $_GET['endpoint'];

    // Define your API endpoints and their corresponding controllers
    $endpoints = [
        'getAllClients' => 'clients.php',
        'getAllStocks' => 'stocks.php',
        'getAllTransactions' => 'clients_transactions.php',
        'getWalletInfo' => 'clients_wallet.php'

    ];

    if (array_key_exists($endpoint, $endpoints)) {
        // Include the appropriate controller based on the endpoint
        include $endpoints[$endpoint];
        exit();
    }
}

// Handle invalid endpoints or other requests
header("HTTP/1.0 404 Not Found");
echo "404 Not Found";
