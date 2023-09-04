<?php
// api.php

require_once 'db.php'; // Include the common database connection function
require_once 'stocks.php'; // Include the stocks controller
require_once 'clients.php'; // Include the clients controller
require_once 'clients_transactions.php'; // Include the clients_transactions controller
require_once 'clients_wallet.php'; // Include the clients_wallet controller

// Existing CORS and preflight request handling
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// Get the database connection
$conn = connectToDatabase();

if (!$conn) {
    // Handle database connection error
    echo json_encode(["error" => "Failed to connect to the database"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests for specific endpoints
    if (isset($_GET['endpoint'])) {
        $endpoint = $_GET['endpoint'];
        
        if ($endpoint === 'getAllStocks') {
            // Handle requests for getting all stocks
            getStocks($conn);
        } elseif ($endpoint === 'getAllClients') {
            // Handle requests for getting all clients
            getClients($conn);
        }
        elseif ($endpoint === 'getAllTransactions') {
            // Handle requests for getting all clients transactions
            getTransactions($conn);
        } 
        elseif ($endpoint === 'getWalletInfo') {
            // Handle requests for getting all wallet of client info
            getWallet($conn);
        }
    } else {
        // Handle other GET requests
        echo json_encode(["error" => "Invalid endpoint"]);
    }
}

// Close the database connection
$conn = null;
?>
