<!-- 6 -->

<?php
require 'connection.php';

try {
  // Create the pets table
  $sql1 = "CREATE TABLE IF NOT EXISTS `pets` (
    `Pet_ID` int(5) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `User_ID` int(5) UNSIGNED,
    `Center_ID` int(5) UNSIGNED,
    `Name` varchar(100) NOT NULL,
    `AnimalType` varchar(100) NOT NULL,
    `Breed` varchar(100) NOT NULL,
    `Gender` enum('Female','Male') NOT NULL,
    `Weight` decimal(10,0) NOT NULL,
    `Height` decimal(10,0) NOT NULL,
    `Photo` varchar(255) NOT NULL DEFAULT 'assets/defaultPet.png',
    `DOB` date NOT NULL,
    `AdoptionStatus` enum('Available','Adopted','Reserved') NOT NULL,
    `Color` varchar(100) NOT NULL,
    `MedicalHistory` text NOT NULL,
    `Description` text NOT NULL,
    `DateListed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  )";
  $conn->exec($sql1);

  // Add foreign key constraints
  $sql2 = "ALTER TABLE `pets`
            ADD KEY `center_pet` (`Center_ID`),
            ADD KEY `user_pet` (`User_ID`)";
  $conn->exec($sql2);

  $sql3 = "ALTER TABLE `pets`
            ADD CONSTRAINT `center_pet` FOREIGN KEY (`Center_ID`) REFERENCES `adoptioncenters` (`Center_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `user_pet` FOREIGN KEY (`User_ID`) REFERENCES `individualusers` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE";
  $conn->exec($sql3);

  echo "Table pets created successfully with constraints";

} catch (PDOException $e) {
    echo "Error creating pets table: " . $e->getMessage();
}

try {
  // Insert data into pets
  $sql_pets = "INSERT INTO `pets` (`Pet_ID`, `User_ID`, `Center_ID`, `Name`, `AnimalType`, `Breed`, `Gender`, `Weight`, `Height`, `DOB`, `AdoptionStatus`, `Color`, `MedicalHistory`, `Description`, `DateListed`) VALUES
              ('1', NULL, '1', 'Max', 'Dog', 'Golden Retriever', 'Male', 30, 60, '2021-05-01', 'Available', 'Golden', 'Vaccinated, Neutered', 'Max is an energetic and friendly Golden Retriever who loves to play fetch and run in open spaces. He is great with kids and other pets, making him the perfect family dog. Max enjoys long walks and cuddling up on the couch after a fun day outdoors. He is fully vaccinated and neutered, and his ideal home is one with a yard or active lifestyle.', CURRENT_TIMESTAMP),
              ('2', NULL, '2', 'Bella', 'Cat', 'Siamese', 'Female', 5, 25, '2020-08-15', 'Adopted', 'Brown', 'Vaccinated', 'Bella is a calm and affectionate Siamese cat with a gentle nature. She loves curling up in cozy spots and being pampered with attention. Bella is ideal for a quiet home where she can be the center of attention. She is litter box trained, fully vaccinated, and enjoys sitting by the window, watching the world go by.', CURRENT_TIMESTAMP),
              ('3', NULL, '2', 'Rocky', 'Dog', 'Bulldog', 'Male', 25, 50, '2022-02-10', 'Available', 'White', 'No health issues', 'Rocky is a loyal and energetic Bulldog with a lovable personality. He enjoys outdoor adventures and playing tug-of-war with his toys. Rocky is very protective of his family and will always make sure you feel safe. He’s looking for an active home where he can get plenty of exercise and attention. Despite his tough exterior, he’s extremely affectionate and loves being around people.', CURRENT_TIMESTAMP),
              ('4', '1', NULL, 'Charlie', 'Dog', 'Beagle', 'Male', 12, 45, '2022-07-01', 'Available', 'Tri-Color', 'Vaccinated', 'Charlie is a cheerful Beagle with a nose for adventure. He loves hiking, exploring new places, and playing in the backyard. He’s highly intelligent and enjoys sniffing around during walks. Charlie would be a perfect fit for an active family or individual who enjoys outdoor activities. He is fully vaccinated and ready for his new home.', CURRENT_TIMESTAMP),
              ('5', NULL, '1', 'Milo', 'Cat', 'Persian', 'Male', 7, 30, '2021-12-25', 'Available', 'White', 'Neutered', 'Milo is a calm and regal Persian cat, with a thick, soft coat that requires regular grooming. He is an affectionate lap cat who loves attention and cuddles. Milo is a low-maintenance companion and would do well in a home where he can enjoy a quiet, relaxed environment. He’s neutered and enjoys being pampered with extra treats and affection.', CURRENT_TIMESTAMP),
              ('6', '2', NULL, 'Luna', 'Dog', 'Poodle', 'Female', 10, 40, '2021-03-15', 'Available', 'Cream', 'Vaccinated, Spayed', 'Luna is a friendly and affectionate Poodle, known for her intelligence and eagerness to please. She’s a great family dog and is especially good with kids. Luna loves interactive play, including fetch and obedience training, and is also content to cuddle up after a good play session. She is fully vaccinated and spayed, ready to join her forever family.', CURRENT_TIMESTAMP)";
  
  $conn->exec($sql_pets);

  echo "Sample data inserted successfully";
} catch (PDOException $e) {
  echo "Error inserting sample data: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
