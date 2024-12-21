<!-- 6 -->

<?php
// Require the connection file
require 'connection.php';

try {
    // Create the SQL query to create the table
    $sql1 = "CREATE TABLE IF NOT EXISTS `adoptionrequest` (
        `Request_ID` varchar(10) NOT NULL,
        `Finder_ID` varchar(10) NOT NULL,
        `Lister_ID` varchar(10) NOT NULL,
        `Center_ID` varchar(10) NOT NULL,
        `Pet_ID` varchar(10) NOT NULL,
        `DateRequested` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `Status` enum('Pending','Approved','Rejected','') NOT NULL,
        PRIMARY KEY (`Request_ID`)
  )";
  $conn->exec($sql1);

  // Add foreign key constraints
  $sql2 = "ALTER TABLE `adoptionrequest`
            ADD KEY `user_finder` (`Finder_ID`),
            ADD KEY `user_lister` (`Lister_ID`),
            ADD KEY `pet_listed` (`Pet_ID`)";
  $conn->exec($sql2);

  $sql3 = "ALTER TABLE `adoptionrequest`
            ADD CONSTRAINT `center_lister` FOREIGN KEY (`Lister_ID`) REFERENCES `adoptioncenters` (`Center_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `pet_listed` FOREIGN KEY (`Pet_ID`) REFERENCES `pets` (`Pet_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `user_finder` FOREIGN KEY (`Finder_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `user_lister` FOREIGN KEY (`Lister_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table adoptionrequest created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating adoptionrequest table: " . $e->getMessage();
}

// Close the connection
$conn = null;

?>