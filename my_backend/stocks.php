<?php
// stocks.php

require_once 'db.php'; // Include the common database connection function

$conn = connectToDatabase(); // Get the database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests to retrieve a list of stock items
    try {
        $stmt = $conn->query("SELECT * FROM stocks ORDER BY created_at DESC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error retrieving stock items: " . $e->getMessage()]);
    }
}
