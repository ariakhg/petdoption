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
    // Fetch pet details with lister information
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
            END AS lister_pic
            FROM pets p
            LEFT JOIN individualusers i ON p.User_ID = i.User_ID
            LEFT JOIN adoptioncenters ac ON p.Center_ID = ac.Center_ID
            WHERE p.Pet_ID = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([$pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        header('Location: findAPet.php');
        exit();
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
        margin-bottom: 5rem;
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
    .like-button{
        height: 30px;
        width: 30px;
        font-size: 24px;
        line-height: 33px;
        border-radius: 50px;
        background-color: #FCEED5;
        border: 1px solid #E7BD43;
        margin-top: 35px;
        align-items: right;
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

    /* .about-header{
        font-size: 36px;
        font-weight: 700px;
    } */

    .about-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    .about-content {
        padding: 0 1rem;
        margin-left: 100px;
    }

    .about-title {
        color: #103559;
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .about-text {
        color: #424242;
        font-size: 24px;
        font-weight: 400;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .medical-info, .date-info {
        margin-bottom: 1rem;
        font-size: 24px;
        font-weight: 400;
    }

    .medical-label, .date-label {
        color: #1a237e;
        font-weight: 700;
        font-size: 24px;
        display: inline;
    }

    .lister-card {
        margin-right: 100px;
        background-color: #F8F8F8;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .lister-avatar {
        width: 100px;
        height: 100px;
        margin: 0 auto 1rem;
    }

    .lister-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .lister-name {
        color: #103559;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .lister-location {
        color: #103559;
        font-size: 20px;
        font-weight: 400px;
        margin-bottom: 1.5rem;
    }

    .contact-btn {
        background-color: #ffd700;
        color: #1a237e;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        border: none;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
    }
</style>

<body>
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
                <input type="button" class="like-button" value="♡">
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
            <?php endif; ?>
        </div>
    </div>

    <div class="about-section">
        <div class="about-content">
            <h2 class="about-title">About Me</h2>
            <p class="about-text"><?php echo nl2br(htmlspecialchars($pet['Description'])); ?></p>
            <div class="medical-info">
                <span class="medical-label">Medical History : </span>
                <?php echo htmlspecialchars($pet['MedicalHistory']); ?>
            </div>
            <div class="date-info">
                <span class="date-label">Date Listed : </span>
                <?php echo date('d/m/Y', strtotime($pet['DateListed'])); ?>
            </div>
        </div>
        
        <div class="lister-card">
            <h2 class="about-title">Lister</h2>
            <div class="lister-avatar">
                <img src="<?php echo htmlspecialchars($pet['lister_pic']); ?>" alt="Lister">
            </div>
            <div class="lister-name"><?php echo htmlspecialchars($pet['lister_name']); ?></div>
            <div class="lister-location"><?php echo htmlspecialchars($pet['lister_location']); ?></div>
            <button class="contact-btn">Contact Lister</button>
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

    <script>
        let isAdopted = false;
        const adoptButton = document.getElementById('adoptButton');
        const statusBadge = document.getElementById('statusBadge');

        function toggleAdoption() {
            if (!isAdopted) {
                showConfirmModal();
            } else {
                // Reset the button and status
                isAdopted = false;
                adoptButton.textContent = 'Adopt Me';
                adoptButton.classList.remove('unreserve');
                statusBadge.textContent = 'Available';
                statusBadge.classList.remove('reserved');
            }
        }
        function showConfirmModal() {
            document.getElementById('confirmMoal').style.display = 'block';
        }

        function hideConfirmModal() {
            document.getElementById('confirmAdoption').style.display = 'none';
        }

        function confirmAdoption() {
            isAdopted = true;
            document.getElementById('confirmModal').style.display = 'none';
            document.getElementById('successModal').style.display = 'block';
            
            // Update button and status
            adoptButton.textContent = 'Unreserve Me';
            adoptButton.classList.add('unreserve');
            statusBadge.textContent = 'Reserved';
            statusBadge.classList.add('reserved');

            // Hide success modal after 3 seconds
            setTimeout(() => {
                document.getElementById('successModal').style.display = 'none';
            }, 3000);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
    </script>
</body>
</html>