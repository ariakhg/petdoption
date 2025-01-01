<?php
session_start();
require 'config/connection.php';

// Get center ID from URL
$center_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$center_id) {
    header('Location: findAPet.php');
    exit();
}

try {
    // Fetch center details
    $stmt = $conn->prepare("SELECT * FROM adoptioncenters WHERE Center_ID = ?");
    $stmt->execute([$center_id]);
    $center = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$center) {
        header('Location: findAPet.php');
        exit();
    }

    // Fetch pet listings for the center
    $stmt = $conn->prepare("SELECT * FROM pets WHERE Center_ID = ? AND AdoptionStatus IN ('Available', 'Reserved')");
    $stmt->execute([$center_id]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch reviews with user information
    $stmt = $conn->prepare("SELECT r.*, u.Name AS Full_Name, u.ProfilePic 
                        FROM reviewratings r 
                        JOIN individualusers u ON r.User_ID = u.User_ID 
                        WHERE r.Reviewed_Center_ID = ?");
    $stmt->execute([$center_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate average rating
    $averageRating = 0;
    if (count($reviews) > 0) {
        $averageRating = number_format(array_sum(array_column($reviews, 'Rating')) / count($reviews), 1);
    } else {
        $averageRating = 'N/A';
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($center['CenterName']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .back-link {
            color: #103559;
            font-size: 24px;
            display: inline-block;
            margin-bottom: 1rem;
            font-weight: 700;
            margin-left: 130px;
            margin-top: 30px;
            margin-bottom: 40px;
            text-decoration: none;
        }

        .profile-section {
            background-color: #F8F8F8;
            width: 1003px;
            height: 289px;
            padding: 73px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 40px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .profile-image {
            width: 143px;
            height: 143px;
            border-radius: 50%;
            background-color: var(--yellow);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image img , .review-section img {
            width: 143px;
            height: 143px;
            border-radius: 50%;
        }

        .profile-details h1 {
            font-size: 36px;
            font-weight: 700;
            color: #103559;
            margin-top: -15px;
            margin-bottom: 30px;
        }

        .profile-details h2 {
            font-size: 24px;
            font-weight: 700;
            color: #103559;
        }

        .profile-details p {
            font-size: 20px;
            font-weight: 400;
            color: #103559;
            margin-top: 10px;
        }

        .profile-right {
            margin-left: auto;
            align-items: right;
            text-align: right;
        }

        .contact-button {
            background-color: #FBD157;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            border: 1px solid #E7BD43;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            color: #1B141F;
            margin-bottom: 30px;
            text-decoration: none;
        }

        .section-title h1 {
            font-size: 36px;
            font-weight: 700;
            color: #103559;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .listings-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

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

        .pet-image {
            width: 143px;
            height: 143px;
            border-radius: 50%;
            margin: 1rem auto;
            object-fit: cover;
        }

        .status-available {
            color: #2F7A3B;
            background-color: #DCFFE4;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-reserved {
            color: #CC1F1F;
            background-color: #FFE0E0;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
        }

        .pet-info h3 {
            font-size: 24px;
            font-weight: 700;
            color: #103559;
            margin: 1rem 0;
        }

        .pet-info p {
            color: #103559;
            font-size: 16px;
            margin: 0.5rem 0;
        }

        .find-out-more {
            background-color: #FFD233;
            color: #103559;
            padding: 8px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 1rem;
            border: 1px solid #E7BD43;
        }

        .view-more {
            background-color: #FFF6E4;
            color: #103559;
            padding: 8px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: block;
            width: fit-content;
            margin: 2rem auto;
            border: none;
        }

        .review-section {
            background-color: rgb(255, 255, 255);
            width: 1003px;
            height: 250px;
            padding: 2rem;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 40px;
            border-bottom: 1px solid #B4ABABA8;
            display: flex;
            align-items: center;
            gap: 3rem;
        }

        .review-details h3 {
            font-size: 24px;
            font-weight: 700;
            color: #103559;
        }

        .review-details p {
            font-size: 20px;
            font-weight: 400;
            color: #9D9C9C;
        }

        .review-details strong {
            font-size: 20px;
            font-weight: 700;
            color: #9D9C9C;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <a href="findAPet.php" class="back-link">< Adoption Center</a>

    <div class="profile-section">
        <div class="profile-image">
            <img src="<?php echo htmlspecialchars($center['ProfilePic']); ?>" alt="<?php echo htmlspecialchars($center['CenterName']); ?>">
        </div>
        <div class="profile-details">
            <h1><?php echo htmlspecialchars($center['CenterName']); ?></h1>
            <h2>Adoption Center</h2>
            <p><?php echo htmlspecialchars($center['Location']); ?></p>
        </div>
        <div class="profile-right">
            <a href="contactLister.php?id=<?php echo $center_id; ?>" class="contact-button">Contact Lister</a>
            <div class="profile-details">
                <p>Total Pet Listings: <?php echo count($pets); ?></p>
                <p>Average Rating: <?php echo $averageRating; ?>/5</p>
            </div>
        </div>
    </div>

    <div class="section-title">
        <h1>All Pet Listings</h1>
    </div>

    <div class="listings-grid">
        <?php foreach ($pets as $pet): ?>
            <div class="pet-card">
                <?php if ($pet['AdoptionStatus'] == 'Available'): ?>
                    <span class="status-available">✓ Available!</span>
                <?php else: ?>
                    <span class="status-reserved">⊗ Reserved!</span>
                <?php endif; ?>
                
                <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                     alt="<?php echo htmlspecialchars($pet['Name']); ?>" 
                     class="pet-image">
                <div class="pet-info">
                    <h3>🐾 <?php echo htmlspecialchars($pet['Name']); ?></h3>
                    <p><?php echo htmlspecialchars($pet['Gender']); ?>, <?php echo htmlspecialchars($pet['Breed']); ?></p>
                    <p>📍 <?php echo htmlspecialchars($center['Location']); ?></p>
                    <p>🏠 <?php echo htmlspecialchars($center['CenterName']); ?></p>
                </div>
                <a href="petProfile.php?id=<?php echo $pet['Pet_ID']; ?>" 
                   class="find-out-more">Find out more →</a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($pets) > 3): ?>
        <button class="view-more-button">View more</button>
    <?php endif; ?>

    <div class="section-title">
        <h1>Reviews</h1>
    </div>

    <div class="reviews-section">
        <?php foreach ($reviews as $review): ?>
            <div class="review-section">
            <div class="review-image">
                <img src="<?php echo htmlspecialchars($review['ProfilePic']); ?>">
            </div>
                <div class="review-details">
                    <h3><?php echo htmlspecialchars($review['Full_Name']); ?></h3>
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['Rating']); ?>/5</p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($review['Date']); ?></p>
                    <p><strong>Review:</strong> "<?php echo htmlspecialchars($review['Review']); ?>"</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Footer -->
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