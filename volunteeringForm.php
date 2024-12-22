<?php
session_start();
require_once 'config/connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            throw new Exception("User not logged in");
        }

        $stmt = $conn->prepare("INSERT INTO volunteering (User_ID, Full_Name, Date_of_Birth, Email, Phone_Number, Location, EmergencyContact, Status) 
        VALUES (:userId,:fullName,:dob,:email,:phone,:location,:emergency,'Pending')");
        
        $stmt->execute([
            ':userId' => $userId,
            ':fullName' => $_POST['full-name'],
            ':dob' => $_POST['dob'],
            ':email' => $_POST['email'],
            ':phone' => $_POST['phone'],
            ':location' => $_POST['location'],
            ':emergency' => $_POST['emergency-contact']
        ]);

        // Send JSON response for AJAX
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteering Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
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
        /* position: relative; */
    }
    .form-container {
        margin-left: auto;
        margin-right: auto;
        margin-top: 30px;
        margin-bottom: 30px;
        width: 833px;
        background-color: #103559;
        color: #fff;
        padding: 20px 30px;
        border-radius: 35px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    .form-header {
        text-align: center;
        margin-bottom: 20px;
        border-radius: 35px;
    }

    .form-header h2 {
        background-color: #ffa500;
        color: #FFFFFF;
        padding: 10px 50px 10px 50px;
        border-radius: 35px;
        display: inline-block;
    }

    .desc p{
        color: #FFFFFF;
        margin-top: 30px;
        margin-left: auto;
        margin-right: auto;
        width: 80%;
        font-size: 16px;
    }

    .volunteer-container{
        margin-left: auto;
        margin-right: auto;
    }
    .volunteer-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        color: #ffffff;
        font-size: 16px;
        font-weight: 500;
    }

    .form-group input {
        padding: 12px 16px;
        border: 2px solid #ffffff;
        border-radius: 35px;
        font-size: 14px;
        background-color: #ffffff;
        color: #000000;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #ffa500;
        box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
    }

    .button-group {
        grid-column: 1 / -1;
        display: flex;
        justify-content: center;
        margin-top: 20px;
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

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #103559;
        padding: 40px;
        border-radius: 35px;
        text-align: center;
        position: relative;
        width: 90%;
        max-width: 500px;
    }

    .modal-thanks {
        color: #ffffff;
        font-size: 40px;
        font-weight: 700;
        margin: 20px 0;
    }

    .modal-thankstext {
        color: #ffffff;
        font-size: 24px;
        font-weight: 400;
        margin-bottom: 20px;
    }

    /* Style for showing the modal */
    .modal.show {
        display: flex;
    }

</style>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php';?>

    <div class="form-container">
        <div class="form-header">
            <h2>Volunteering Form</h2>
            <div class="desc">
                <p>Ensure that you are 16 or older in order to work. <br> Petdoption will send your application to adoption centers that are in need of helpers.</p>
            </div>
        </div>
        <div class="volunteer-container">
            <form class="volunteer-form" id="myForm">
                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full-name" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="emergency-contact">Emergency Contact</label>
                    <input type="tel" id="emergency-contact" name="emergency-contact" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Address</label>
                    <input type="text" id="location" name="location" placeholder="To match with nearby adoption centers" required>
                </div>
                
                <div class="button-group">
                    <button class="btn-form" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="thankYouModal" class="modal">
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
        const form = document.getElementById('myForm');
        const modal = document.getElementById('thankYouModal');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(form);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show the modal
                    modal.classList.add('show');
                    
                    // Reset form (only non-readonly fields)
                    document.getElementById('phone').value = '';
                    document.getElementById('location').value = '';
                    document.getElementById('emergency-contact').value = '';

                    // Hide modal and redirect after 3 seconds
                    setTimeout(() => {
                        modal.classList.remove('show');
                        window.location.href = 'findAPet.php'; // Adjust redirect as needed
                    }, 3000);
                } else {
                    alert('Error: ' + (result.error || 'Something went wrong'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });

        // Remove the onclick attribute from the submit button and update it to:
        document.querySelector('button[type="submit"]').removeAttribute('onclick');
    </script>
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
