<!-- 2 -->

<?php

$host = 'localhost';
$username = 'root';
$password = ''; // Leave blank for no password
$database = 'db_adoption';
$port = 3306; // Adjust if you're using a different port

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
  } 
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>