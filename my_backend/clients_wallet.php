<?php
 
require_once 'db.php'; // Include the common database connection function

$conn = connectToDatabase(); // Get the database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the emp_id parameter is provided in the query string
    if (isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        try {
            // Prepare and execute the SQL query with a WHERE clause to filter by user_id
            $stmt = $conn->prepare("SELECT cw.amount
                                    FROM clients_wallet AS cw
                                    WHERE cw.user_id = :user_id");
              $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
              $stmt->execute();
              $wallet_amount = $stmt->fetchAll(PDO::FETCH_ASSOC);

              $all_users = $conn->prepare("SELECT ct.*
                        FROM clients_transactions AS ct
                        WHERE ct.user_id = :user_id");
                        $all_users->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $all_users->execute();
                        $results_allUsers = $all_users->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(["current_amount" => $wallet_amount, "gain_loss" => $results_allUsers]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error retrieving clients transactions records: " . $e->getMessage()]);
        }
    } else {
        // Handle the case when user_id parameter is not provided
        echo json_encode(["error" => "Missing user_id parameter"]);
    }
}

 
 







