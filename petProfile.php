<?php
session_start();
require_once 'config/connection.php';

// Get pet ID from URL
$pet_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$pet_id) {
    header('Location: findAPet.php');
    exit();
}

try {
    // Fetch pet details with lister information and check if current user reserved it
    $sql = "SELECT p.*, 
            CASE 
                WHEN p.Center_ID IS NOT NULL THEN ac.CenterName
                WHEN p.User_ID IS NOT NULL THEN i.Name
            END AS lister_name,
            CASE 
                WHEN p.Center_ID IS NOT NULL THEN ac.Location
                WHEN p.User_ID IS NOT NULL THEN i.Location
            END AS lister_location,
            CASE 
                WHEN p.Center_ID IS NOT NULL THEN ac.ProfilePic
                WHEN p.User_ID IS NOT NULL THEN i.ProfilePic
            END AS lister_pic,
            CASE 
                WHEN ar.User_ID = ? THEN true
                ELSE false
            END AS is_reserved_by_user
            FROM pets p
            LEFT JOIN individualusers i ON p.User_ID = i.User_ID
            LEFT JOIN adoptioncenters ac ON p.Center_ID = ac.Center_ID
            LEFT JOIN adoptionrequests ar ON p.Pet_ID = ar.Pet_ID AND ar.Status = 'Pending'
            WHERE p.Pet_ID = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['user_id'] ?? null, $pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        header('Location: findAPet.php');
        exit();
    }

    // Check if pet is saved by current user
    $checkSavedSql = "SELECT SavedPets FROM individualusers WHERE User_ID = ?";
    $stmt = $conn->prepare($checkSavedSql);
    $stmt->execute([$_SESSION['user_id'] ?? null]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $savedPets = !empty($userData['SavedPets']) ? explode(',', $userData['SavedPets']) : [];
    $isPetSaved = in_array($pet_id, $savedPets);
    
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
    <title>Pet Profile - <?php echo htmlspecialchars($pet['Name']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>

<style>
    .nav-bar-icon img {
        width: 1.5rem;
        height: 1.5rem;
    }
    a {
        text-decoration: none;
        color: #103559;
    }
    .back-link {
        color: #103559;
        font-size: 18px;
        display: inline-block;
        margin-bottom: 1rem;
        font-weight: 700;
        margin-left: 130px;
        margin-top: 30px;
        margin-bottom: 40px;
        }
    .card-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 3rem;
    }
    .card {
        background-color: #e7f3ff;
        border-radius: 50px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 1045px;
        height: 371px;
        padding: 20px;
        display: flex;
        flex-direction: row;
    }
    .pet-image {
        width: 257px;
        height: 257px;
        border-radius: 50%;
        margin-top: auto;
        margin-bottom: auto;
        margin-left: 50px;
    }
    .pet-name{
        margin-top: 30px;
        margin-left: 80px;
        font-size: 30px;
        height: 54px;
    }
    .status-available {
        margin-top: 32px;
        background-color: #AFD8BB;
        align-items: center;
        text-align: center;
        width: 167px;
        height: 42px;
        border-radius: 25px; 
        line-height: 40px;
        margin-left: 30px;
        font-size: 16px;
        font-weight: bold;
        color: #1B141F;
    }

    .status-reserved {
        margin-top: 30px;
        background-color: #f44336;
    }
    .pet-details {
        margin-left: -555px;
        margin-top: 100px;
        height: 200px;
        width: 500px;
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Three columns */
        gap: 20px; /* Spacing between grid items */
        background-color:#E4F4FF;
        padding: 20px;
        border-radius: 10px;
        width: 60%; /* Adjust the width to fit the content */
    }

    .detail-item {
    text-align: left;
    }

    .detail-item h3 {
    margin: 0;
    font-size: 18px;
    color: #103559;
    font-weight: bold;
    }

    .detail-item p {
    margin: 5px 0 0;
    font-size: 20px;
    font-weight: 400;
    color: #103559;
    }

    /* Centering the last two items under the first three */
    .detail-item:nth-child(4),
    .detail-item:nth-child(5) {
    grid-column: span 1; /* Span a single column */
    }

    .like-container{
        margin-left: 200px;
    }

    .like-button {
        background-color: #FFF5E9;
        border: none;
        cursor: pointer;
        padding: 12px;
        border-radius: 50%;
        transition: all 0.3s ease;
        width: 48px;
        height: 48px;
        margin-top: 1.5rem;
    }

    .like-button:hover {
        background-color: #FFE8CC;
    }

    .like-button svg {
        width: 24px;
        height: 24px;
        transition: all 0.3s ease;
        stroke: #103559;
    }

    .like-button.saved {
        background-color: #FFF5E9;
    }

    .like-button.saved svg {
        fill:rgb(240, 95, 95);
        stroke: #103559;
    }

    .btn-primary {
        margin-top: 250px;
        margin-left: -160px;
        background-color: var(--yellowbtn);
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        border: 1px solid #E7BD43;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        height: 45px;
        width: 147px;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: var(--activeyellow);
    }

    #adoptButton {
        font-size: 14px
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: #103559;
        border-radius: 10px;
        padding: 2rem;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .modal-question{
        font-size: 24px;
        font-weight: 700;
        color: #FFFFFF;
    }

    .modal-thanks{
        font-size: 40px;
        font-weight: 700;
        color: #FFFFFF;
    }
    .modal-thankstext{
        font-size: 24px;
        font-weight: 400;
        color: #FFFFFF;
    }

    .modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1rem;
    }

    .modal-buttons button {
        padding: 0.5rem 2rem;
        border-radius: 20px;
        border: 2px solid #E7BD43;
        cursor: pointer;
    }

    .confirm-btn {
        background-color: #FBD157;
        font-size: 16px;
        font-weight: 700;
        color: #1B141F;
    }

    .cancel-btn {
        background-color: #FFFFFF;
        font-size: 16px;
        font-weight: 700;
        color: #1B141F;
    }

    .success-icon {
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background-color: #103559;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        border: 13px solid #FCAD35;
    }

    .success-icon svg {
        width: 150px;
        height: 150px;
        fill: #FCAD35;
    }

    .status-available.reserved {
        background-color: #F1E2B9;
        color: #1B141F;
        font-size: 16px;
    }

    .about-section {
        display: grid;
        grid-template-columns: 2fr 1fr; /* 2/3 for about, 1/3 for lister */
        gap: 2rem;
        margin: 2rem 8rem;
        align-items: start; /* Align items to top */
    }

    .about-content {
        background-color: #FFFFFF;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .about-title {
        color: #103559;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .about-text {
        color: #424242;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .medical-info, .date-info {
        margin-bottom: 1rem;
        font-size: 18px;
        font-weight: 400;
    }

    .medical-label, .date-label {
        color: #103559;
        font-weight: 700;
        font-size: 18px;
        display: inline;
    }

    .lister-card {
        background-color: #F8F8F8;
        border-radius: 20px;
        padding: 2rem;
        position: sticky;
        top: 2rem;
        display: flex;
        flex-direction: column;
    }

    .lister-profile {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .lister-pic {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1rem;
    }

    .lister-info h3 {
        color: #103559;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .lister-info p {
        color: #666666;
        font-size: 16px;
        margin: 0.5rem 0 0;
    }

    .lister-location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666666;
        font-size: 16px;
        margin-top: 0.5rem;
    }

    .lister-location img {
        width: 16px;
        height: 16px;
    }

    /* Make the layout responsive */
    @media (max-width: 1200px) {
        .about-section {
            margin: 2rem 4rem;
        }
    }

    @media (max-width: 768px) {
        .about-section {
            grid-template-columns: 1fr;
            margin: 2rem 2rem;
        }

        .lister-card {
            position: static;
        }
    }

    .view-lister-btn {
        background-color: var(--yellowbtn);
        color: #103559;
        padding: 0.75rem 1rem;
        border-radius: 25px;
        border: 1px solid #E7BD43;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 1.5rem;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .view-lister-btn:hover {
        background-color: var(--activeyellow);
    }

    .view-lister-btn:disabled {
        background-color: #cccccc;
        border-color: #bbbbbb;
        cursor: not-allowed;
        color: #666666;
    }

    .card img {
        width: 200px;
        height: 200px;
        margin-right: 3rem;
    }

</style>

<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>

    <a href="findAPet.php" class="back-link">< Pet Profile</a>

    <div class="card-container">
        <div class="card">
            <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" alt="<?php echo htmlspecialchars($pet['Name']); ?>" class="pet-image">
            <h2 class="pet-name"><?php echo htmlspecialchars($pet['Name']); ?></h2>
            <div id="statusBadge" class="status-available <?php echo strtolower($pet['AdoptionStatus']); ?>">
                <?php echo htmlspecialchars($pet['AdoptionStatus']); ?>
            </div>
            <div class="like-container">
                <button class="like-button <?php echo $isPetSaved ? 'saved' : ''; ?>" onclick="toggleSave(<?php echo $pet_id; ?>)">
                    <svg viewBox="0 0 24 24" fill="<?php echo $isPetSaved ? '#103559' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </button>
            </div>
            <div class="pet-details">
                <div class="detail-item">
                    <h3>Breed</h3>
                    <p><?php echo htmlspecialchars($pet['AnimalType'] . ', ' . $pet['Breed']); ?></p>
                </div>
                <div class="detail-item">
                    <h3>Weight</h3>
                    <p><?php echo htmlspecialchars($pet['Weight']); ?>kg</p>
                </div>
                <div class="detail-item">
                    <h3>Height</h3>
                    <p><?php echo htmlspecialchars($pet['Height']); ?>cm</p>
                </div>
                <div class="detail-item">
                    <h3>Gender</h3>
                    <p><?php echo htmlspecialchars($pet['Gender']); ?></p>
                </div>
                <div class="detail-item">
                    <h3>Color</h3>
                    <p><?php echo htmlspecialchars($pet['Color']); ?></p>
                </div>
            </div>
            <?php if ($pet['AdoptionStatus'] === 'Available'): ?>
                <button id="adoptButton" class="btn-primary" onclick="toggleAdoption()">Adopt Me</button>
            <?php elseif ($pet['AdoptionStatus'] === 'Reserved' && $pet['is_reserved_by_user']): ?>
                <button id="cancelButton" class="btn-primary" onclick="showCancelModal()">Unreserve</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="about-section">
        <div class="about-content">
            <h2 class="about-title">About Me</h2>
            <p class="about-text"><?php echo htmlspecialchars($pet['Description']); ?></p>
            
            <div class="medical-info">
                <span class="medical-label">Medical History:</span>
                <?php echo htmlspecialchars($pet['MedicalHistory']); ?>
            </div>
            
            <div class="date-info">
                <span class="date-label">Listed on:</span>
                <?php echo date('F j, Y', strtotime($pet['DateListed'])); ?>
            </div>
        </div>

        <div class="lister-card">
            <h2 class="about-title">Lister</h2>
            <div class="lister-profile">
                <img src="<?php echo htmlspecialchars($pet['lister_pic']); ?>" alt="Lister" class="lister-pic">
                <div class="lister-info">
                    <h3><?php echo htmlspecialchars($pet['lister_name']); ?></h3>
                    <p><?php echo htmlspecialchars($pet['lister_location']); ?></p>
                </div>
            </div>
            
            <?php if (!empty($pet['Center_ID'])): ?>
                <a href="centerProfile.php?id=<?php echo $pet['Center_ID']; ?>" class="view-lister-btn">
                    View Center
                </a>
            <?php else: ?>
                <button class="view-lister-btn" disabled>Contact Lister</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-question">Send adoption application?</h2>
            <div class="modal-buttons">
                <button class="cancel-btn" onclick="hideConfirmModal()">Cancel</button>
                <button class="confirm-btn" onclick="confirmAdoption()">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            <h2 class="modal-thanks">Thank you!</h2>
            <p class="modal-thankstext">Your application has been submitted.</p>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-question">Cancel your adoption application?</h2>
            <div class="modal-buttons">
                <button class="cancel-btn" onclick="hideCancelModal()">No, keep it</button>
                <button class="confirm-btn" onclick="confirmCancellation()">Yes, cancel it</button>
            </div>
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
        let isAdopted = <?php echo ($pet['AdoptionStatus'] === 'Reserved') ? 'true' : 'false' ?>;
        const adoptButton = document.getElementById('adoptButton');
        const statusBadge = document.getElementById('statusBadge');

        function toggleAdoption() {
            if (!isAdopted) {
                showConfirmModal();
            } else {
                cancelReservation();
            }
        }

        function showConfirmModal() {
            document.getElementById('confirmModal').style.display = 'block';
        }

        function hideConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function confirmAdoption() {
            console.log('Sending adoption request...');
            fetch('handlers/adoption.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pet_id: <?php echo $pet_id ?>,
                    action: 'reserve'
                })
            })
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    // Hide confirm modal and show success modal
                    document.getElementById('confirmModal').style.display = 'none';
                    document.getElementById('successModal').style.display = 'block';
                    
                    // Update status badge
                    statusBadge.textContent = 'Reserved';
                    statusBadge.className = 'status-available reserved';
                    
                    // Update adopt button
                    adoptButton.textContent = 'Unreserve';
                    isAdopted = true;

                    // Hide success modal after 3 seconds
                    setTimeout(() => {
                        document.getElementById('successModal').style.display = 'none';
                    }, 3000);
                } else {
                    console.error('Error:', data.message);
                    alert(data.message || 'Failed to submit adoption request');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function cancelReservation() {
            if (confirm('Are you sure you want to cancel your reservation?')) {
                fetch('handlers/adoption.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pet_id: <?php echo $pet_id ?>,
                        action: 'unreserve'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge
                        statusBadge.textContent = 'Available';
                        statusBadge.className = 'status-available';
                        
                        // Reset button
                        adoptButton.textContent = 'Adopt Me';
                        isAdopted = false;
                        
                        alert('Reservation cancelled successfully');
                    } else {
                        alert(data.message || 'Failed to cancel reservation');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        function showCancelModal() {
            document.getElementById('cancelModal').style.display = 'block';
        }

        function hideCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
        }

        function confirmCancellation() {
            fetch('handlers/adoption.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pet_id: <?php echo $pet_id ?>,
                    action: 'unreserve'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide cancel modal
                    document.getElementById('cancelModal').style.display = 'none';
                    
                    // Update status badge
                    statusBadge.textContent = 'Available';
                    statusBadge.className = 'status-available';
                    
                    // Update button
                    location.reload();
                } else {
                    alert(data.message || 'Failed to cancel reservation');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        function toggleSave(petId) {
            if (!document.querySelector('.like-button')) return;
            
            const button = document.querySelector('.like-button');
            const isSaved = button.classList.contains('saved');
            const action = isSaved ? 'unsave' : 'save';

            fetch('handlers/savePet.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pet_id: petId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('saved');
                    const svg = button.querySelector('svg');
                    if (data.isSaved) {
                        svg.setAttribute('fill', '#FF6B6B');
                    } else {
                        svg.setAttribute('fill', 'none');
                    }
                } else {
                    if (data.message === 'Please log in first') {
                        window.location.href = 'login.php';
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>