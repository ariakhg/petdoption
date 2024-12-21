<!-- 6 -->

<?php
require 'connection.php';

try {
  // Create the reviewratings table
  $sql1 = "CREATE TABLE IF NOT EXISTS `chatbox` (
    `Chat_ID` varchar(10) NOT NULL,
    `Sender_ID` varchar(10) NOT NULL,
    `Receiver_ID` varchar(10) NOT NULL,
    `Timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `MessageText` text NOT NULL,
    `Photo` varchar(255) NOT NULL,
    PRIMARY KEY (`Chat_ID`)
  )";
  $conn->exec($sql1);

  // // Add foreign key constraints
  // $sql2 = "ALTER TABLE `chatbox`
  //           ADD KEY `users_sender` (`Sender_ID`),
  //           ADD KEY `users_receiver` (`Receiver_ID`)";
  // $conn->exec($sql2);

  $sql3 = "ALTER TABLE `chatbox`
            ADD CONSTRAINT `center_receiver` FOREIGN KEY (`Receiver_ID`) REFERENCES `adoptioncenters` (`Center_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `users_receiver` FOREIGN KEY (`Receiver_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `users_sender` FOREIGN KEY (`Sender_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table chatbox created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating chatbox table: " . $e->getMessage();
}

// Close the connection
$conn = null;

?>