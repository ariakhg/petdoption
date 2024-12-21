<!-- 9 -->

<?php
require 'connection.php';

try {
  // Create the volunteering table
  $sql1 = "CREATE TABLE IF NOT EXISTS `volunteering` (
  `Response_ID` varchar(10) NOT NULL,
  `User_ID` varchar(10) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Location` text NOT NULL,
  `EmergencyContact` varchar(100) NOT NULL,
  `Status` enum('Pending','Approved','Rejected','') NOT NULL,
  PRIMARY KEY (`Response_ID`)
  )";

  $conn->exec($sql1);

  // Add an index for `User_ID` and a foreign key constraint
  $sql2 = "ALTER TABLE `volunteering` 
            ADD KEY `users_volunteer` (`User_ID`)";
  $conn->exec($sql2);

  $sql3 = "ALTER TABLE `volunteering` 
            ADD CONSTRAINT `users_volunteer` FOREIGN KEY (`User_ID`) 
            REFERENCES `individualusers` (`User_ID`) 
            ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table volunteering created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating volunteering table: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
