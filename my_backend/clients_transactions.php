<?php
// clients.php

require_once 'db.php'; // Include the common database connection function

$conn = connectToDatabase(); // Get the database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the emp_id parameter is provided in the query string
    if ($_GET['user_id'] > 0) {
        $user_id = $_GET['user_id'];

        try {
            // Prepare and execute the SQL query with a WHERE clause to filter by user_id
            $stmt = $conn->prepare("SELECT ct.*, s.stock_name AS stock_name, c.name AS client_name, s.current_price AS stock_current_price
                                    FROM clients_transactions AS ct
                                    LEFT JOIN stocks AS s ON ct.stock_id = s.id
                                    LEFT JOIN clients AS c ON ct.user_id = c.id
                                    WHERE ct.user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Iterate through the results to calculate gain_loss, is_gain, and is_loss
            foreach ($results as &$row) {
                $currentPrice = $row['stock_current_price'];
                $purchasePrice = $row['purchase_price'];
                $volume = $row['volume'];

                $gainLoss = ($currentPrice * $volume) - ($purchasePrice * $volume);

                if ($gainLoss < 0) {
                    $row['gain_loss'] = $gainLoss;
                    $row['is_gain'] = 0;
                    $row['is_loss'] = 1;
                } elseif ($gainLoss > 0) {
                    $row['gain_loss'] = $gainLoss;
                    $row['is_gain'] = 1;
                    $row['is_loss'] = 0;
                } else {
                    $row['gain_loss'] = 0;
                    $row['is_gain'] = 0;
                    $row['is_loss'] = 0;
                }

                // Update the clients_transactions table with the calculated values
                $updateStmt = $conn->prepare("UPDATE clients_transactions 
                                              SET gain_loss = :gain_loss, is_gain = :is_gain, is_loss = :is_loss 
                                              WHERE id = :transaction_id");
                $updateStmt->bindParam(':gain_loss', $row['gain_loss'], PDO::PARAM_INT);
                $updateStmt->bindParam(':is_gain', $row['is_gain'], PDO::PARAM_INT);
                $updateStmt->bindParam(':is_loss', $row['is_loss'], PDO::PARAM_INT);
                $updateStmt->bindParam(':transaction_id', $row['id'], PDO::PARAM_INT);
                $updateStmt->execute();
            }

            // Encode and echo the updated results
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error retrieving clients transactions records: " . $e->getMessage()]);
        }
    }  else if ($_GET['user_id'] == 0) {
         
         
        try {
            // Prepare and execute the SQL query with a WHERE clause to filter by user_id
            $stmt = $conn->prepare("SELECT ct.*, s.stock_name AS stock_name, c.name AS client_name, c.id AS client_id, s.current_price AS stock_current_price
                                    FROM clients_transactions AS ct
                                    LEFT JOIN stocks AS s ON ct.stock_id = s.id
                                    LEFT JOIN clients AS c ON ct.user_id = c.id");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Iterate through the results to calculate gain_loss, is_gain, and is_loss
            foreach ($results as &$row) {
                $currentPrice = $row['stock_current_price'];
                $purchasePrice = $row['purchase_price'];
                $volume = $row['volume'];

                $gainLoss = ($currentPrice * $volume) - ($purchasePrice * $volume);

                if ($gainLoss < 0) {
                    $row['gain_loss'] = $gainLoss;
                    $row['is_gain'] = 0;
                    $row['is_loss'] = 1;
                } elseif ($gainLoss > 0) {
                    $row['gain_loss'] = $gainLoss;
                    $row['is_gain'] = 1;
                    $row['is_loss'] = 0;
                } else {
                    $row['gain_loss'] = 0;
                    $row['is_gain'] = 0;
                    $row['is_loss'] = 0;
                }

                // Update the clients_transactions table with the calculated values
                $updateStmt = $conn->prepare("UPDATE clients_transactions 
                                              SET gain_loss = :gain_loss, is_gain = :is_gain, is_loss = :is_loss 
                                              WHERE id = :transaction_id");
                $updateStmt->bindParam(':gain_loss', $row['gain_loss'], PDO::PARAM_INT);
                $updateStmt->bindParam(':is_gain', $row['is_gain'], PDO::PARAM_INT);
                $updateStmt->bindParam(':is_loss', $row['is_loss'], PDO::PARAM_INT);
                $updateStmt->bindParam(':transaction_id', $row['id'], PDO::PARAM_INT);
                $updateStmt->execute();
            }

            // Encode and echo the updated results
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error retrieving clients transactions records: " . $e->getMessage()]);
        }
    }else {
        // Handle the case when user_id parameter is not provided
        echo json_encode(["error" => "Missing user_id parameter"]);
    }
}

 
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonPayload = file_get_contents("php://input");

    if (!empty($jsonPayload)) {
        $data = json_decode($jsonPayload);

        if ($data && isset($data->volume) && isset($data->stockId) && isset($data->user_id)) {
            $user_id = $data->user_id;
            $stockId = $data->stockId;
            $volume = $data->volume;

            // Retrieve the current_price for the specified stockId from the stocks table
            $stmt = $conn->prepare("SELECT current_price FROM stocks WHERE id = :stockId");
            $stmt->bindParam(':stockId', $stockId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Retrieve the user amount from wallet
            $total_amount_query = $conn->prepare("SELECT amount FROM clients_wallet WHERE user_id = :user_id");
            $total_amount_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $total_amount_query->execute();
            $result_total_amount = $total_amount_query->fetch(PDO::FETCH_ASSOC);

            $TOTAL_AMOUNT_IN_WALLET = $result_total_amount['amount'];

            if($TOTAL_AMOUNT_IN_WALLET > 0) {
                if ($result) {
                    $currentPrice = $result['current_price'];
                    $total_price_of_stock = $volume * $currentPrice;
                    if($TOTAL_AMOUNT_IN_WALLET > $total_price_of_stock){
                        try {
                            // Insert a new record into the clients_transactions table
                            $stmt = $conn->prepare("INSERT INTO clients_transactions (user_id, stock_id, volume, purchase_price) VALUES (:user_id, :stockId, :volume, :purchase_price)");
                            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                            $stmt->bindParam(':stockId', $stockId, PDO::PARAM_INT);
                            $stmt->bindParam(':volume', $volume, PDO::PARAM_INT);
                            $stmt->bindParam(':purchase_price', $currentPrice, PDO::PARAM_INT);
                            $stmt->execute();
        
                            // Retrieve the newly inserted record
                            $newRecordId = $conn->lastInsertId();
        
                            // Optionally, you can return the newly inserted record as JSON
                            $stmt = $conn->prepare("SELECT * FROM clients_transactions WHERE id = :newRecordId");
                            $stmt->bindParam(':newRecordId', $newRecordId, PDO::PARAM_INT);
                            $stmt->execute();
                            $newRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
                              // Upadte amount in clients_wallet table
                              $new_amount_after_buy_stock = $TOTAL_AMOUNT_IN_WALLET - $total_price_of_stock;
                              $stmt_updated_amount = $conn->prepare("UPDATE clients_wallet SET amount = :new_amount_after_buy_stock WHERE user_id = :user_id");
                              $stmt_updated_amount->bindParam(':new_amount_after_buy_stock', $new_amount_after_buy_stock, PDO::PARAM_INT); // Assuming amount is an integer
                              $stmt_updated_amount->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Assuming user_id is an integer
                              $stmt_updated_amount->execute();

                            echo json_encode(["message" => "Record inserted successfully", "new_record" => $newRecord]);
                        } catch (PDOException $e) {
                            echo json_encode(["error" => "Error inserting record: " . $e->getMessage()]);
                        }
                    } else {
                        echo json_encode(["error" => "Your wallet amount is not enough to buy this stock. You should have atleast: ".$total_price_of_stock]);
                    }
                    
                } else {
                    // Handle the case when the specified stockId is not found in the stocks table
                    echo json_encode(["error" => "Invalid stockId"]);
                }
            } else {
                echo json_encode(["error" => "Your wallet is empty"]);
            }
        } else {
            // Handle the case when required data is missing in the JSON payload
            echo json_encode(["error" => "Missing or invalid data in the JSON payload"]);
        }
    } else {
        // Handle the case when the JSON payload is empty
        echo json_encode(["error" => "Empty JSON payload"]);
    }
}







