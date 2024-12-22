<?php
require_once 'config/connection.php';

// Start session at the very beginning
session_start();

// Handle status updates
if (isset($_POST['update_status'])) {
    $pet_id = $_POST['pet_id'];
    $new_status = $_POST['new_status'];
    
    $sql = "UPDATE pets SET AdoptionStatus = ? WHERE Pet_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$new_status, $pet_id]);
}

// Handle deletion
if (isset($_POST['delete_pet'])) {
    $pet_id = $_POST['pet_id'];
    
    $sql = "DELETE FROM pets WHERE Pet_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id]);
}

// Check session variables that match login.php
$user_id = $_SESSION['user_id'] ?? null;  // This is set in login.php
$role = $_SESSION['role'] ?? null;        // This is set in login.php

// Determine which query to use based on role
if ($role === 'Center') {
    $sql = "SELECT * FROM pets WHERE Center_ID = ?";
} else {
    $sql = "SELECT * FROM pets WHERE User_ID = ?";
}

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Error fetching pets: " . $e->getMessage());
    echo "Error: " . $e->getMessage(); // Debug
    $pets = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pet Listings</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .parentBox {
            padding: 2rem 8rem 2rem 8rem;
            overflow-y: auto; 
            max-height: calc(100vh - 160px);
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .title {
            color: #1B1B1B;
            font-size: 30px;
            font-weight: bold;
        }
        
        .pet-details h2 {
            font-size: 22px;
        }

        .pet-details p {
            font-size: 16px;
        }

        .btn {
            padding: 0.5rem 1rem;
            margin-right: 0.5rem;
            border-radius: 50px;
            border: none;
            cursor: pointer; 
            align-items: center;
            gap: 1rem;
            font-weight: 500;
        }

        .btn:hover {
            background-color: #E5E7EB;
        }
        
        .petlisting-btn {
            display: flex;
            gap: 1rem;
        }

        .btn-primary {
            padding: 0.5rem 1rem;
            border: none;
            background: #FFD233;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-primary img {
            width: 30px;
            height: 30px;
        }
        
        .pet-card {
            background: #F0F9FF;
            padding: 1rem 1rem 1rem 1rem;
            width: 100%;
            height: 120px;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .pet-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .pet-image {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin: 1.5rem 1rem 1.5rem 1.5rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 16px;
            margin-left: 2rem;
        }
        
        .available { background: #D1FAE5; }
        .adopted { background: #FFE4E6; }
        .reserved { background: #E5E7EB; }
        
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 120px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1;
        }
        
        .dropdown-content a {
            padding: 0.5rem 1rem;
            display: block;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }

        .dropdown-content a:hover {
            background-color: #E5E7EB;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
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

    <div class="parentBox">
        <div class="content-header">
            <h1 class="title">My Pet Listings</h1>
            <div class="petlisting-btn">
                <button class="btn-primary" onclick="window.location.href='adoptionRequest.php'">
                    Adoption Requests
                    <img src="assets/eye-icon.png" alt="View">
                </button>
                <button class="btn-primary" onclick="window.location.href='petListingForm.php'">
                    List A New Pet
                    <img src="assets/plus-icon.png" alt="Add">
                </button>
            </div>
        </div>

        <?php foreach ($pets as $pet): ?>
            <!-- Debug info -->
            <div style="display:none">
                <?php
                echo "Pet ID: " . $pet['Pet_ID'] . "<br>";
                echo "Name: " . $pet['Name'] . "<br>";
                echo "User ID: " . $pet['User_ID'] . "<br>";
                echo "Center ID: " . $pet['Center_ID'] . "<br>";
                ?>
            </div>
            <div class="pet-card">
                <div class="pet-info">
                    <?php if (!empty($pet['Photo'])): ?>
                        <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                             alt="<?php echo htmlspecialchars($pet['Name']); ?>" 
                             class="pet-image"
                             onerror="this.src='assets/default-pet.jpg'">
                    <?php else: ?>
                        <img src="assets/default-pet.jpg" 
                             alt="Default pet image" 
                             class="pet-image">
                    <?php endif; ?>
                    <div class="pet-details">
                        <h2><?php echo htmlspecialchars($pet['Name']); ?></h2>
                        <p><?php echo htmlspecialchars($pet['AnimalType']); ?>, <?php echo htmlspecialchars($pet['Breed']); ?></p>
                    </div>
                    <div class="status-badge <?php echo strtolower($pet['AdoptionStatus']); ?>">
                        <?php echo htmlspecialchars($pet['AdoptionStatus']); ?>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn edit-btn" onclick="window.location.href='list_pet.php?edit=<?php echo $pet['Pet_ID']; ?>'">Edit</button>
                    <div class="dropdown">
                        <button class="btn update-btn">Update</button>
                        <div class="dropdown-content">
                            <form method="POST">
                                <input type="hidden" name="pet_id" value="<?php echo $pet['Pet_ID']; ?>">
                                <a href="#" onclick="updateStatus(<?php echo $pet['Pet_ID']; ?>, 'Available')">Available</a>
                                <a href="#" onclick="updateStatus(<?php echo $pet['Pet_ID']; ?>, 'Reserved')">Reserved</a>
                                <a href="#" onclick="updateStatus(<?php echo $pet['Pet_ID']; ?>, 'Adopted')">Adopted</a>
                            </form>
                        </div>
                    </div>
                    <button class="btn delete-btn" onclick="deletePet(<?php echo $pet['Pet_ID']; ?>)">Delete</button>
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
    
    <script>
        function updateStatus(petId, status) {
            if (confirm('Are you sure you want to update the status?')) {
                const form = new FormData();
                form.append('pet_id', petId);
                form.append('new_status', status);
                form.append('update_status', true);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: form
                }).then(() => window.location.reload());
            }
        }

        function deletePet(petId) {
            if (confirm('Are you sure you want to delete this pet?')) {
                const form = new FormData();
                form.append('pet_id', petId);
                form.append('delete_pet', true);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: form
                }).then(() => window.location.reload());
            }
        }
    </script>
</body>
</html>