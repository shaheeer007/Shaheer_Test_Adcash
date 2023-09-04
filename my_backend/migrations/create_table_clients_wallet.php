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
    $sql = "CREATE TABLE IF NOT EXISTS clients_wallet (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id VARCHAR(255) NOT NULL,
        amount FLOAT NOT NULL DEFAULT 0.0
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
