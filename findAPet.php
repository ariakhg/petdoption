<?php
session_start();
require 'config/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Petdoption - Find A Pet</title>
</head>
<style>
    .header1{
        height:182px;
        display: flex;
    }

    .header1 img {
        width: 100%;
    }

    h1{
        font-weight: 700;
        font-size: 32px;
        padding-top: 2rem;
        padding-bottom: 1rem;
        text-align: center;
    }

    .search-bar {
        align-items: center;
        background-color:#F8F8F8;
        padding: 10px 20px;
        border-radius: 30px;
        margin-left: auto;
        margin-right: auto;
        width: 900px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-input {
        position: relative;
        margin-right: 20px;
    }

    .search-input input {
        border: none;
        outline: none;
        font-size: 16px;
        padding: 10px 10px 10px 10px;
        border-radius: 30px;
        background-color: #ffffff;
        width: 400px;
        padding-left: 55px;
        color: #9D9C9C;
    }

    .search-input .search-icon {
        position: absolute;
        top: 60%;
        transform: translateY(-50%);
        left: 18px;
        color: #888;
    }

    .search-icon img {
        width: 1.5rem;
    }

    .dropdown {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-right: 20px;
    }

    .dropdown label {
        font-size: 16px;
        color: #9D9C9C;
        margin-bottom: 5px;
    }

    .dropdown select {
        border: none;
        background-color: #F8F8F8;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        border: #F8F8F8;
    }

    /* Pet Grid Layout */
    .pet-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Pet Card Styles */
    .pet-card {
        background-color: var(--lightblue);
        border-radius: 50px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        min-height: 433px;
        width: 326px;
    }

    .status-available,
    .status-reserved {
        position: absolute;
        top: 2rem;
        left: 2rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-available {
        background-color: #E8F8F0;
        color: #1D976C;
    }

    .status-reserved {
        background-color: #FFE8E8;
        color: #FF4D4D;
    }

    .pet-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 2.5rem 0 1.5rem 0;
    }

    .pet-info {
        text-align: center;
        width: 100%;
    }

    .pet-info h2 {
        font-family: 'Nunito Sans', sans-serif;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--darkblue);
    }

    .pet-details {
        color: #666;
        margin-bottom: 1rem;
        font-size: 14px;
    }

    .location,
    .lister {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: #666;
        font-size: 14px;
    }

    .location img,
    .lister img {
        width: 16px;
        height: 16px;
    }

    .find-out-more {
        background-color: var(--yellowbtn);
        color: var(--darkblue);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        margin-top: 1rem;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: fit-content;
        margin: 1.5rem auto 0;
        transition: background-color 0.3s ease;
    }

    .find-out-more:hover {
        background-color: var(--activeyellow);
    }

    .find-out-more img {
        width: 16px;
        height: 16px;
    }

    .find-pet-btn {
        background-color: var(--yellowbtn);
        color: var(--darkblue);
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .find-pet-btn:hover {
        background-color: var(--activeyellow);
    }

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: var(--darkblue);
    }

    .no-results p {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

</style>

<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php';?>

    <!-- Find A Pet -->
    <h1>Meet The Pets & Give Them A Loving Forever Home!</h1>
    <div class="header1">
        <img src="assets/header1.jpg" alt="Header">
    </div> 
    <br>

    <!-- Search & Filter Form -->
    <form method="GET" action="" class="search-bar">
        <div class="search-input">
            <i class="search-icon"><img src="assets/search.png" alt="Search"></i>
            <input type="text" name="search" placeholder="Search pet, center or lister name" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        
        <div class="dropdown">
            <label>Animal</label>
            <select name="AnimalType">
                <option value="all" <?php echo (!isset($_GET['AnimalType']) || $_GET['AnimalType'] == 'all') ? 'selected' : ''; ?>>All</option>
                <option value="Dog" <?php echo (isset($_GET['AnimalType']) && $_GET['AnimalType'] == 'Dog') ? 'selected' : ''; ?>>Dog</option>
                <option value="Cat" <?php echo (isset($_GET['AnimalType']) && $_GET['AnimalType'] == 'Cat') ? 'selected' : ''; ?>>Cat</option>
                <option value="Bird" <?php echo (isset($_GET['AnimalType']) && $_GET['AnimalType'] == 'Bird') ? 'selected' : ''; ?>>Bird</option>
            </select>
        </div>

        <div class="dropdown">
            <label>State</label>
            <select name="Location">
                <option value="all" <?php echo (!isset($_GET['Location']) || $_GET['Location'] == 'all') ? 'selected' : ''; ?>>All</option>
                <option value="Selangor" <?php echo (isset($_GET['Location']) && $_GET['Location'] == 'Selangor') ? 'selected' : ''; ?>>Selangor</option>
                <option value="Johor" <?php echo (isset($_GET['Location']) && $_GET['Location'] == 'Johor') ? 'selected' : ''; ?>>Johor</option>
                <option value="Penang" <?php echo (isset($_GET['Location']) && $_GET['Location'] == 'Penang') ? 'selected' : ''; ?>>Penang</option>
            </select>
        </div>

        <div class="dropdown">
            <label>Gender</label>
            <select name="Gender">
                <option value="all" <?php echo (!isset($_GET['Gender']) || $_GET['Gender'] == 'all') ? 'selected' : ''; ?>>All</option>
                <option value="Male" <?php echo (isset($_GET['Gender']) && $_GET['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_GET['Gender']) && $_GET['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <button type="submit" class="find-pet-btn">Find Pet</button>
    </form>

    <!-- Pet Listings -->
    <div class="pet-grid">
        <?php
        try {
            // Fetch pets with user or center location and user information
            $sql = "SELECT 
                    p.*, 
                    CASE 
                        WHEN p.Center_ID IS NOT NULL THEN ac.CenterName
                        WHEN p.User_ID IS NOT NULL THEN i.Name
                    END AS lister_name,
                    CASE 
                        WHEN p.Center_ID IS NOT NULL THEN ac.Location
                        WHEN p.User_ID IS NOT NULL THEN i.Location
                    END AS location
                FROM pets p
                LEFT JOIN individualusers i ON p.User_ID = i.User_ID
                LEFT JOIN adoptioncenters ac ON p.Center_ID = ac.Center_ID
                WHERE p.AdoptionStatus IN ('Available', 'Reserved')";

            $params = array();

            // Add search condition
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $searchTerm = '%' . $_GET['search'] . '%';
                $sql .= " AND (p.Name LIKE ? OR ac.CenterName LIKE ? OR i.Name LIKE ?)";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Add animal type filter
            if (isset($_GET['AnimalType']) && $_GET['AnimalType'] != 'all') {
                $sql .= " AND p.AnimalType = ?";
                $params[] = $_GET['AnimalType'];
            }

            // Add state filter
            if (isset($_GET['Location']) && $_GET['Location'] != 'all') {
                $sql .= " AND (ac.Location = ? OR i.Location = ?)";
                $params[] = $_GET['Location'];
                $params[] = $_GET['Location'];
            }

            // Add gender filter
            if (isset($_GET['Gender']) && $_GET['Gender'] != 'all') {
                $sql .= " AND p.Gender = ?";
                $params[] = $_GET['Gender'];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($pets) > 0) {
                foreach ($pets as $pet) {
                    $statusClass = $pet['AdoptionStatus'] === 'Available' ? 'status-available' : 'status-reserved';
                    $statusIcon = $pet['AdoptionStatus'] === 'Available' ? '✓' : '✕';
                    ?>
                    <div class="pet-card">
                        <div class="<?php echo $statusClass; ?>">
                            <?php echo $statusIcon; ?> <?php echo $pet['AdoptionStatus']; ?>!
                        </div>
                        <img src="<?php echo $pet['Photo']; ?>" alt="<?php echo $pet['AnimalType']; ?>" class="pet-image">
                        <div class="pet-info">
                            <h2><?php echo $pet['Name']; ?></h2>
                            <p class="pet-details">
                                <?php echo "{$pet['AnimalType']}, {$pet['Breed']}, {$pet['Gender']}."; ?>
                            </p>
                            <p class="location">
                                <img src="assets/location-icon.png" alt="Location">
                                <?php echo $pet['location']; ?>
                            </p>
                            <p class="lister">
                                <img src="assets/lister-icon.png" alt="Lister">
                                <?php echo $pet['lister_name']; ?>
                            </p>
                            <button class="find-out-more" onclick="window.location.href='petProfile.php?id=<?php echo $pet['Pet_ID']; ?>'">
                                Find out more
                                <img src="assets/arrow-right.png" alt="Arrow">
                            </button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="no-results">
                    <p>No pets found matching your criteria.</p>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            echo "Error fetching pets: " . $e->getMessage();
        }
        ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer">
            <p>&copy;Copyright 2024 Pedoption. All rights reserved.</p>
            <img src="assets/logo.png" alt="Petdoption Logo" class="footer-logo">
            <div>
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>