<!-- 4 -->

<?php
require 'connection.php';

try {
    // Create the individualusers table
    $sql = "CREATE TABLE IF NOT EXISTS `individualusers` (
        `User_ID` int(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `Name` varchar(255) NOT NULL,
        `Email` varchar(255) NOT NULL,
        `ProfilePic` varchar(255) NOT NULL DEFAULT 'profile/defaultprofile.png',
        `PhoneNo` varchar(100) NOT NULL,
        `Location` varchar(255) NOT NULL,
        `Password` varchar(255) NOT NULL,
        `Role` enum('Center','User','Admin') NOT NULL,
        `SavedPets` text
    )";

    // Execute the SQL statement
    $conn->exec($sql);

    echo "Table individualusers created successfully<br>";

} catch (PDOException $e) {
    echo "Error creating individualusers table: " . $e->getMessage();
}

// try {
//     // Insert sample data into individualusers
//     $sql = "INSERT INTO `individualusers` (`User_ID`, `Name`, `Email`, `ProfilePic`, `PhoneNo`, `Location`, `Password`, `Role`, `SavedPets`) VALUES
//         ('U001', 'Rebecca Lee', 'rebecca@gmail.com', 'rebeccalee.jpg', '1234567890', 'Johor', 'password123', 'User', 'P001,P002'),
//         ('U002', 'Jane Soo', 'janesoo@gmail.com', 'janesoo.jpg', '9876543210', 'Selangor', 'securepassword', 'User', 'P003')";
//     // Execute the SQL statement
//     $conn->exec($sql);

//     echo "Sample data inserted into individualusers successfully";

// } catch (PDOException $e) {
//     echo "Error inserting sample data: " . $e->getMessage();
// }

// Close the connection
$conn = null;
?>