<?php
// Require the connection file
require 'connection.php';

try {
    // Create the SQL query to create the table
    $sql1 = "CREATE TABLE IF NOT EXISTS `adoptionrequests` (
        `Request_ID` int(5) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
        `Pet_ID` int(5) UNSIGNED NOT NULL,
        `User_ID` int(5) UNSIGNED NOT NULL,
        `RequestDate` timestamp NOT NULL DEFAULT current_timestamp(),
        `Status` enum('Pending','Approved','Rejected') NOT NULL
    )";
    $conn->exec($sql1);

    // Add foreign key constraints
    $sql2 = "ALTER TABLE `adoptionrequests`
            ADD KEY `pet_request` (`Pet_ID`),
            ADD KEY `user_request` (`User_ID`)";
    $conn->exec($sql2);

    $sql3 = "ALTER TABLE `adoptionrequests`
            ADD CONSTRAINT `pet_request` FOREIGN KEY (`Pet_ID`) REFERENCES `pets` (`Pet_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `user_request` FOREIGN KEY (`User_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE";
    $conn->exec($sql3);

    echo "Table adoptionrequests created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating adoptionrequests table: " . $e->getMessage();
}

// Close the connection
$conn = null;

?>