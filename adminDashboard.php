<?php
require 'config/connection.php';

try {
    // Query to count active users
    $stmt = $conn->prepare("SELECT COUNT(*) as userCount FROM individualusers WHERE Role = 'User'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $userCount = $result['userCount'];

    // Query to count total active pet listings
    $stmt = $conn->prepare("SELECT COUNT(*) as listingCount FROM pets WHERE AdoptionStatus = 'Available'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $listingCount = $result['listingCount'];

    // Query to count adopted pets
    $stmt = $conn->prepare("SELECT COUNT(*) as adoptCount FROM pets WHERE AdoptionStatus = 'Adopted'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $adoptCount = $result['adoptCount'];

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $userCount = 0;
    $listingCount = 0;
    $adoptCount = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminDashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h1>
        Welcome Back, Admin!
    </h1>
    <div class="mainParent">
        <div class="subParent1">
            <div class="userBox">
                <div class="top1">
                    <div class="userNum">
                        <h2><?php echo htmlspecialchars($userCount); ?></h2>
                    </div>
                    <div class="userIcon">
                        <img src="assets/admin-user-icon.png" id="userIcon">
                    </div>
                </div>
                <p id="Text">Active Users</p>
                <a href="userManagement.php">
                    <button id="viewBtn">View More</button>
                </a>
            </div>
            <div class="listingBox">
                <div class="top2">
                    <div class="listingNum">
                        <h2><?php echo htmlspecialchars($listingCount); ?></h2>
                    </div>
                    <div class="listingIcon">
                        <img src="assets/pet-listing-icon.png" id="listingIcon">
                    </div>  
                </div>
                <p id="Text">Total Active Pets Listing</p>
                <a href="activePetListing.php">
                    <button id="viewBtn">View More</button>
                </a>    
            </div>
        </div>
    </div>
    <footer>
        <div class="footer">
            <p>&copy; 2024 Petdoption. All rights reserved.</p>
            <img src="assets/logo.png" alt="Petdoption Logo" class="footer-logo">
            <div>
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html> 