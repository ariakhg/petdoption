<!-- 5 -->

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

try {
    // Individual user data
    $individualUsers = [
        ['1', 'Rebecca Lee', 'rebecca@gmail.com', 'profile/defaultprofile.png', '1234567890', 'Johor', 'password123', 'User', ''],
        ['2', 'Jane Soo', 'janesoo@gmail.com', 'janesoo.jpg', '9876543210', 'Selangor', 'securepassword', 'User', '']
    ];

    // Insert hashed passwords into the database for individual users
    foreach ($individualUsers as $user) {
        $hashedPassword = password_hash($user[6], PASSWORD_DEFAULT);  // Hash the password
        $stmt = $conn->prepare("INSERT INTO individualusers (User_ID, Name, Email, ProfilePic, PhoneNo, Location, Password, Role, SavedPets) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user[0], $user[1], $user[2], $user[3], $user[4], $user[5], $hashedPassword, $user[7], $user[8]]);
    }

    echo "Sample data inserted into individualusers successfully with hashed passwords<br>";

} catch (PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
