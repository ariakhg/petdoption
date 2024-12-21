<!-- 3 -->

<?php
require 'connection.php';

try {
    // Create the adoptioncenters table
    $sql = "CREATE TABLE IF NOT EXISTS `adoptioncenters` (
        `Center_ID` int(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `CenterName` varchar(255) NOT NULL,
        `PhoneNo` varchar(100) NOT NULL,
        `ProfilePic` varchar(255) NOT NULL DEFAULT 'profile/defaultprofile.png',
        `Location` varchar(255) NOT NULL,
        `Email` varchar(255) NOT NULL,
        `Password` varchar(255) NOT NULL,
        `Role` enum('Center','User','Admin') NOT NULL,
        `AvgRating` decimal(10,1) NOT NULL
    )";
    $conn->exec($sql);
    echo "Table adoptioncenters created successfully<br>";

    // // Insert sample data
    // $sql = "INSERT INTO `adoptioncenters` 
    //         (Center_ID, CenterName, PhoneNo, ProfilePic, Location, Email, Password, Role, AvgRating) VALUES
    //         ('C001', 'PAWS', '017-284 7500', 'paws.png', 'Petaling Jaya', 'paws@gmail.com', 'pAw3!hehe', 'Center', 3.9),
    //         ('C002', 'SPCA Selangor', '03-4256 5312', 'spca.png', 'Petaling Jaya', 'spca@gmail.com', 'Whatisagoodp@ssword', 'Center', 4.4)";
    // $conn->exec($sql);

    // echo "Sample data inserted successfully<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
