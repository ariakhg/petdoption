<?php
session_start();
require 'config/connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $center_id = $_POST['center_id'];
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Insert review
        $stmt = $conn->prepare("
            INSERT INTO reviewratings (User_ID, Reviewed_Center_ID, Rating, Review) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $center_id, $rating, $review]);

        // Update center's average rating
        $stmt = $conn->prepare("
            UPDATE adoptioncenters 
            SET AvgRating = (
                SELECT ROUND(AVG(Rating), 1)
                FROM reviewratings 
                WHERE Reviewed_Center_ID = ?
            )
            WHERE Center_ID = ?
        ");
        $stmt->execute([$center_id, $center_id]);

        $conn->commit();
        
        $_SESSION['success_message'] = "Review submitted successfully!";
        header('Location: savedPets.php');
        exit();

    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Error submitting review. Please try again.";
        header('Location: savedPets.php');
        exit();
    }
}

try {
    // Fetch user's saved and reserved pets
    $stmt = $conn->prepare("SELECT SavedPets, ReservedPets FROM individualusers WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Convert comma-separated strings to arrays
    $savedPetIds = !empty($user['SavedPets']) ? explode(',', $user['SavedPets']) : [];
    $reservedPetIds = !empty($user['ReservedPets']) ? explode(',', $user['ReservedPets']) : [];

    // Fetch saved pets details
    $savedPets = [];
    if (!empty($savedPetIds)) {
        $placeholders = str_repeat('?,', count($savedPetIds) - 1) . '?';
        $stmt = $conn->prepare("SELECT * FROM pets WHERE Pet_ID IN ($placeholders)");
        $stmt->execute($savedPetIds);
        $savedPets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch reserved pets details
    $reservedPets = [];
    if (!empty($reservedPetIds)) {
        $placeholders = str_repeat('?,', count($reservedPetIds) - 1) . '?';
        $stmt = $conn->prepare("
            SELECT p.*, p.Center_ID, ar.Status as RequestStatus 
            FROM pets p 
            JOIN adoptionrequests ar ON p.Pet_ID = ar.Pet_ID 
            WHERE p.Pet_ID IN ($placeholders)
        ");
        $stmt->execute($reservedPetIds);
        $reservedPets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle remove from saved
if (isset($_POST['remove_saved'])) {
    $pet_id = $_POST['pet_id'];
    try {
        // Remove pet ID from SavedPets
        $savedPetIds = array_diff($savedPetIds, [$pet_id]);
        $newSavedPets = implode(',', $savedPetIds);
        
        $stmt = $conn->prepare("UPDATE individualusers SET SavedPets = ? WHERE User_ID = ?");
        $stmt->execute([$newSavedPets, $user_id]);
        
        header('Location: savedPets.php');
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Saved Pets</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        h1 {
            color: #1B1B1B;
            font-size: 30px;
            font-weight: bold;
            margin-left: 100px;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 16px;
        }

        .tabs {
            display: flex;
            gap: 0;
            background-color: #fdf6e9;
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 24px;
        }

        .tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
        }

        .tab.active {
            background-color: var(--yellowbtn);
        }

        .pet-card {
            display: flex;
            align-items: center;
            padding: 16px;
            border-bottom: 3px solid #eee;
            gap: 16px;
            background-color: var(--lightblue);
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .pet-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pet-info {
            flex: 1;
        }

        .pet-name {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .pet-breed {
            color: #666;
            margin: 0;
        }

        .button {
            padding: 8px 24px;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            white-space: nowrap;
        }

        .remove-button, .contact-button {
            background-color: var(--yellowbtn);
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }

        .pet-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 200px;
        }

        .form-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .form-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: var(--darkblue);
            padding: 2.5rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
        }

        .form-container h2, .form-container h3 {
            color: white;
            margin-bottom: 1.5rem;
        }

        .stars {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .star {
            color: #E4E4E4;
            font-size: 2.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star.active {
            color: var(--yellowbtn);
        }

        textarea {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            border: none;
            resize: none;
            margin-bottom: 2rem;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .cancel-btn, .submit-btn {
            padding: 0.75rem 2.5rem;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .cancel-btn {
            background-color: #E4E4E4;
        }

        .submit-btn {
            background-color: var(--yellowbtn);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .no-pets {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
        }

    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <h1>My Saved / Applied Pets</h1>

    <div class="container">
        <div class="tabs">
            <div class="tab active" onclick="switchTab('saved')">Saved</div>
            <div class="tab" onclick="switchTab('applied')">Applied</div>
        </div>

        <!-- Saved Pets Tab -->
        <div id="saved-content" class="tab-content active">
            <?php if (empty($savedPets)): ?>
                <p class="no-pets">No saved pets yet.</p>
            <?php else: ?>
                <?php foreach ($savedPets as $pet): ?>
                    <div class="pet-card">
                        <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                             alt="<?php echo htmlspecialchars($pet['Name']); ?>" 
                             class="pet-image">
                        <div class="pet-info">
                            <h3 class="pet-name"><?php echo htmlspecialchars($pet['Name']); ?></h3>
                            <p class="pet-breed"><?php echo htmlspecialchars($pet['AnimalType']); ?>, 
                                               <?php echo htmlspecialchars($pet['Breed']); ?></p>
                        </div>
                        <div class="pet-actions">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="pet_id" value="<?php echo $pet['Pet_ID']; ?>">
                                <button type="submit" name="remove_saved" class="button remove-button">Remove</button>
                            </form>
                            <a href="petProfile.php?id=<?php echo $pet['Pet_ID']; ?>" class="button contact-button">View Profile</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Applied/Reserved Pets Tab -->
        <div id="applied-content" class="tab-content">
            <?php if (empty($reservedPets)): ?>
                <p class="no-pets">No applied pets yet.</p>
            <?php else: ?>
                <?php foreach ($reservedPets as $pet): ?>
                    <div class="pet-card">
                        <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                             alt="<?php echo htmlspecialchars($pet['Name']); ?>" 
                             class="pet-image">
                        <div class="pet-info">
                            <h3 class="pet-name"><?php echo htmlspecialchars($pet['Name']); ?></h3>
                            <p class="pet-breed"><?php echo htmlspecialchars($pet['AnimalType']); ?>, 
                                               <?php echo htmlspecialchars($pet['Breed']); ?></p>
                        </div>
                        <div class="pet-actions">
                            <span class="status-badge <?php echo strtolower($pet['RequestStatus']); ?>">
                                <?php echo htmlspecialchars($pet['RequestStatus']); ?>
                            </span>
                            <?php if ($pet['RequestStatus'] === 'Approved'): ?>
                                <button class="button review-button" onclick="openForm(<?php echo $pet['Center_ID']; ?>)">
                                    Give a rating / review
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Form -->
    <div id="reviewForm" class="form-popup">
        <div class="form-container">
            <h2>Submit a review</h2>
            <form method="POST" id="reviewFormContent">
                <input type="hidden" name="center_id" id="center_id_input">
                <input type="hidden" name="rating" id="rating_input">
                <div class="rating-section">
                    <h3>1. Rate your experience with the lister/center</h3>
                    <div class="stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                <div class="review-section">
                    <h3>2. Review</h3>
                    <textarea name="review" placeholder="Type in your review" rows="5"></textarea>
                </div>
                <div class="button-container">
                    <button type="button" class="cancel-btn" onclick="closeForm()">Cancel</button>
                    <button type="submit" name="submit_review" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
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

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            if (tab === 'saved') {
                document.querySelector('.tab:first-child').classList.add('active');
                document.getElementById('saved-content').classList.add('active');
            } else {
                document.querySelector('.tab:last-child').classList.add('active');
                document.getElementById('applied-content').classList.add('active');
            }
        }

        function openForm(centerId) {
            document.getElementById("reviewForm").style.display = "block";
            document.getElementById("center_id_input").value = centerId;
        }

        function closeForm() {
            document.getElementById("reviewForm").style.display = "none";
            rating = 0;
            document.getElementById("rating_input").value = "";
            document.querySelector('textarea').value = '';
            updateStars();
        }

        const stars = document.querySelectorAll('.star');
        let rating = 0;

        stars.forEach(star => {
            star.addEventListener('click', () => {
                rating = parseInt(star.getAttribute('data-rating'));
                document.getElementById("rating_input").value = rating;
                updateStars();
            });

            star.addEventListener('mouseover', () => {
                const hoverRating = parseInt(star.getAttribute('data-rating'));
                highlightStars(hoverRating);
            });

            star.addEventListener('mouseout', () => {
                highlightStars(rating);
            });
        });

        function highlightStars(count) {
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                star.classList.toggle('active', starRating <= count);
            });
        }

        function updateStars() {
            highlightStars(rating);
        }

        // Add form validation
        document.getElementById('reviewFormContent').onsubmit = function(e) {
            const rating = document.getElementById('rating_input').value;
            const review = document.querySelector('textarea[name="review"]').value;
            
            if (!rating) {
                e.preventDefault();
                alert('Please select a rating');
                return false;
            }
            if (!review.trim()) {
                e.preventDefault();
                alert('Please write a review');
                return false;
            }
            return true;
        };
    </script>
</body>
</html>