<?php
require 'connection.php';

try {
    // Create the individualusers table
    $sql = "CREATE TABLE IF NOT EXISTS `individualusers` (
        `User_ID` int(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `Name` varchar(255) NOT NULL,
        `Email` varchar(255) NOT NULL,
        `ProfilePic` varchar(255) NOT NULL DEFAULT 'assets/defaultProfile.jpg',
        `PhoneNo` varchar(100) NOT NULL,
        `Location` varchar(255) NOT NULL,
        `Password` varchar(255) NOT NULL,
        `Role` enum('Center','User','Admin') NOT NULL,
        `SavedPets` text,
        `ReservedPets` text
    )";

    // Execute the SQL statement
    $conn->exec($sql);

    echo "Table individualusers created successfully<br>";

} catch (PDOException $e) {
    echo "Error creating individualusers table: " . $e->getMessage();
}

try {
    // Individual user data
    $individualUsers = [
        ['1', 'Rebecca Lee', 'rebecca@gmail.com', '1234567890', 'Johor', '123', 'User', '', ''],
        ['2', 'Jane Soo', 'janesoo@gmail.com', '9876543210', 'Selangor', '123', 'User', '', '4']
    ];

    // Insert hashed passwords into the database for individual users
    foreach ($individualUsers as $user) {
        $hashedPassword = password_hash($user[5], PASSWORD_DEFAULT);  // Hash the password
        $stmt = $conn->prepare("INSERT INTO individualusers (User_ID, Name, Email, PhoneNo, Location, Password, Role, SavedPets) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user[0], $user[1], $user[2], $user[3], $user[4], $hashedPassword, $user[6], $user[7]]);
    }

    echo "Sample data inserted into individualusers successfully with hashed passwords<br>";

} catch (PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
