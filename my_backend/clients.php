<?php
// clients.php

require_once 'db.php'; // Include the common database connection function

$conn = connectToDatabase(); // Get the database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET requests to retrieve a list of client records
    try {
        $stmt = $conn->query("SELECT * FROM clients");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error retrieving client records: " . $e->getMessage()]);
    }
} else if($_SERVER['REQUEST_METHOD'] === 'POST'){
     // Get the JSON payload from the request body
     $jsonPayload = file_get_contents("php://input");

     // Check if the JSON payload is not empty
     if (!empty($jsonPayload)) {
         // Decode the JSON payload
         $data = json_decode($jsonPayload);
 
         // Check if the JSON payload contains a user_id
         if ($data && isset($data->user_id)) {
             $user_id = $data->user_id;
 
             try {
                 // Update the is_active column in the clients table
                 // Set is_active = 1 for the matching user_id and is_active = 0 for others
                 $stmt = $conn->prepare("UPDATE clients SET is_active = CASE WHEN id = :user_id THEN 1 ELSE 0 END");
                 $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                 $stmt->execute();
 
                 echo json_encode(["message" => "Updated is_active status successfully"]);
             } catch (PDOException $e) {
                 echo json_encode(["error" => "Error updating is_active status: " . $e->getMessage()]);
             }
         } else {
             // Handle the case when user_id is not provided in the JSON payload
             echo json_encode(["error" => "Missing user_id in the JSON payload"]);
         }
     } else {
         // Handle the case when the JSON payload is empty
         echo json_encode(["error" => "Empty JSON payload"]);
     }
}
 
