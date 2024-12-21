<!-- 3 -->

<?php
require 'connection.php';

try {
    // Create the admin table
    $sql = "CREATE TABLE IF NOT EXISTS `admin` (
        `Admin_ID` varchar(10) PRIMARY KEY,
        `Name` varchar(255) NOT NULL,
        `Email` varchar(255) NOT NULL,
        `PhoneNo` varchar(100) NOT NULL,
        `Password` varchar(255) NOT NULL,
        `Role` enum('Center','User','Admin','') NOT NULL
    )";
    $conn->exec($sql);
    echo "Table admin created successfully<br>";

    // Admin data
    $admins = [
        ['admin1', 'Zubaria', 'ariakhg@gmail.com', '+60 14-6722 996', 'AdminPass2024!', 'Admin'],
        ['admin2', 'Jozelle', 'jozelly@gmail.com', '+60 12-973 7604', 'AdminSecret2024!', 'Admin'],
        ['admin3', 'Shyanne', 'shy@gmail.com', '+60 18-265 2399', 'AdminWord2024!', 'Admin']
    ];

    // Insert hashed passwords into the database
    foreach ($admins as $admin) {
        $hashedPassword = password_hash($admin[4], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin (Admin_ID, Name, Email, PhoneNo, Password, Role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$admin[0], $admin[1], $admin[2], $admin[3], $hashedPassword, $admin[5]]);
    }

    echo "Sample data inserted successfully with hashed passwords<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;

?>