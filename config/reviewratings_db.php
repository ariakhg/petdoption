<!-- 10 -->

<?php
require 'connection.php';

try {
  // Create the reviewratings table
  $sql1 = "CREATE TABLE IF NOT EXISTS `reviewratings` (
      `Rating_ID` int(5) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
      `User_ID` int(5) UNSIGNED NOT NULL,
      `Reviewed_Center_ID` int(5) UNSIGNED NOT NULL,
      `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `Rating` decimal(10,0) NOT NULL,
      `Review` text NOT NULL
  )";
  $conn->exec($sql1);

  // Add foreign key constraints
  $sql2 = "ALTER TABLE `reviewratings`
            ADD KEY `users_rate` (`User_ID`),
            ADD KEY `center_review` (`Reviewed_Center_ID`)";
  $conn->exec($sql2);

  $sql3 = "ALTER TABLE `reviewratings`
            ADD CONSTRAINT `center_review` FOREIGN KEY (`Reviewed_Center_ID`) 
            REFERENCES `adoptioncenters` (`Center_ID`) 
            ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `users_rate` FOREIGN KEY (`User_ID`) 
            REFERENCES `individualusers` (`User_ID`) 
            ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table reviewratings created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating reviewratings table: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
