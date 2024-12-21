<!-- 1 -->

<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Leave blank for no password
$port = 3306; // Adjust if you're using a different port

try {
    // Connect to MySQL server without specifying a database
    $conn = new PDO("mysql:host=$host;port=$port", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create the database
    $sql = "CREATE DATABASE IF NOT EXISTS db_adoption";

    // Execute the query
    $conn->exec($sql);

    echo "Database created successfully or already exists.";
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage();
} finally {
    // Close the connection
    $conn = null;
}
?>