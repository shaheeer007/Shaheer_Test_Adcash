<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "123456789";
$dbname = "adcash_db";

try {
    // Create a PDO database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL statement to create a new table
    $sql = "CREATE TABLE IF NOT EXISTS clients_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(255) NOT NULL,
        volume INT,
        stock_id INT,
        purchase_price FLOAT,
        gain_loss FLOAT,
        is_gain INT,
        is_loss INT,
        purchase_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";

    // Execute the SQL statement
    $conn->exec($sql);

    echo "Table created successfully";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
