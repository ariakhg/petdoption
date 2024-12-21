<!-- 4 -->

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

} catch (PDOException $e) {
    echo "Error creating adoptioncenters table: " . $e->getMessage() . "<br>";
}

try {
    // Adoption centers data
    $adoptionCenters = [
        ['1', 'PAWS', '017-284 7500', 'paws.png', 'Petaling Jaya', 'paws@gmail.com', 'pAw3!hehe', 'Center', 3.9],
        ['2', 'SPCA Selangor', '03-4256 5312', 'spca.png', 'Petaling Jaya', 'spca@gmail.com', 'Whatisagoodp@ssword', 'Center', 4.4]
    ];

    // Insert hashed passwords into the database for adoption centers
    foreach ($adoptionCenters as $center) {
        $hashedPassword = password_hash($center[6], PASSWORD_DEFAULT);  // Hash the password
        $stmt = $conn->prepare("INSERT INTO adoptioncenters (Center_ID, CenterName, PhoneNo, ProfilePic, Location, Email, Password, Role, AvgRating) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$center[0], $center[1], $center[2], $center[3], $center[4], $center[5], $hashedPassword, $center[7], $center[8]]);
    }

    echo "Sample data inserted into adoptioncenters successfully with hashed passwords<br>";

} catch (PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage() . "<br>";
}

// Close the connection
$conn = null;
?>
