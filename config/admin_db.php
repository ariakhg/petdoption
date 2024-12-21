<!-- 3 -->

<?php
require 'connection.php';

try {
    // Create the admin table
    $sql = "CREATE TABLE IF NOT EXISTS `admin` (
        `Admin_ID` varchar(10) PRIMARY KEY,
        `Name` varchar(255) NOT NULL,
        `Email` varchar(255) NOT NULL,
        `PhoneNo` varchar(100) NOT NULL,
        `Password` varchar(255) NOT NULL,
        `Role` enum('Center','User','Admin','') NOT NULL
    )";
    $conn->exec($sql);
    echo "Table admin created successfully<br>";

    // Sample data insertion
    $sql = "INSERT INTO admin (Admin_ID, Name, Email, PhoneNo, Password, Role) VALUES
    ('admin1', 'Zubaria', 'ariakhg@gmail.com', '+60 14-6722 996', 'AdminPass2024!', 'Admin'),
    ('admin2', 'Jozelle', 'jozelly@gmail.com', '+60 12-973 7604', 'AdminSecret2024!', 'Admin'),
    ('admin3', 'Shyanne', 'shy@gmail.com', '+60 18-265 2399', 'AdminWord2024!', 'Admin')";

    $conn->exec($sql);
    echo "Sample data inserted successfully<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;

?>