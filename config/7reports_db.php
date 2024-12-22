<?php
require 'connection.php';

try {
  // Create the reports table
  $sql1 = "CREATE TABLE IF NOT EXISTS `reports` (
    `Report_ID` int(5) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `User_ID` int(5) UNSIGNED,
    `Center_ID` int(5) UNSIGNED,
    `Reported_User_ID` int(5) UNSIGNED,
    `Reported_Center_ID` int(5) UNSIGNED,
    `Email` varchar(255) NOT NULL,
    `PhoneNo` varchar(100) NOT NULL,
    `Date` date NOT NULL,
    `Description` text NOT NULL,
    `Attachments` varchar(255) NOT NULL,
    `Status` enum('Pending','Approved','Rejected','Resolved') NOT NULL
  )";
  $conn->exec($sql1);

  // Add foreign key constraints
  $sql2 = "ALTER TABLE `reports`
            ADD KEY `center_report` (`Center_ID`),
            ADD KEY `users_report` (`User_ID`),
            ADD KEY `center_reported` (`Reported_Center_ID`),
            ADD KEY `users_reported` (`Reported_User_ID`)";
  $conn->exec($sql2);

  $sql3 = "ALTER TABLE `reports`
            ADD CONSTRAINT `center_report` FOREIGN KEY (`Center_ID`) REFERENCES `adoptioncenters` (`Center_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `center_reported` FOREIGN KEY (`Reported_Center_ID`) REFERENCES `adoptioncenters` (`Center_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `users_report` FOREIGN KEY (`User_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `users_reported` FOREIGN KEY (`Reported_User_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table reports created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating reports table: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>