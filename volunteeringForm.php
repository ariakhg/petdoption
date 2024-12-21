<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteering Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    /* body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    } */
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
        padding: 10px;
        border-radius: 35px;
        display: inline-block;
    }

    .list-container{
        margin-top: 30px;
        margin-left: auto;
        margin-right: auto;
        width: 492px;
    }

    .form-header ul li{
        list-style: disc;
        font-size: 14px;
        color: #FFFFFF;
        text-align: left;
    }

    .volunteer-container{
        margin-left: auto;
        margin-right: auto;
    }
    .volunteer-form {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .volunteer-form label {
        margin-left: 150px;
        font-size: 20px;
        font-weight: 500;
        color: #ffffff;
    }
    .volunteer-form input {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 20px;
        width: 488px;
        height: 45px;
        border-radius: 35px;
        font-size: 16px;
        font-weight: 500;
        color:rgb(0, 0, 0);
    }

    label {
        font-size: 14px;
        font-weight: bold;
    }

    input {
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        width: 100%;
    }

    input:focus {
        outline: 2px solid #ffa500;
    }

    button {
        margin-left: auto;
        margin-right: auto;
        background-color: #ffa500;
        color: #FFFFFF;
        padding: 10px;
        border: none;
        border-radius: 35px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        text-align: center;
        width: 170px;
        height: 45px;
    }

    button:hover {
        background-color: #e59500;
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
            <div class="list-container">
                <ul>
                    <li>Ensure that you are 16 or older in order to work.</li>
                    <li>PetAdoption will send your application to adoption centers that are in need of helpers.</li>
                    <li>Check your email address or SMS in the next 3 days from submitting your form to know application outcome.</li>
                </ul>
            </div>
        </div>
        <div class="volunteer-container">
            <form class="volunteer-form" id="myForm">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="full-name">Full Name:</label>
                <input type="text" id="full-name" name="full-name" required>
                
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
                
                <label for="location">Location Address:</label>
                <input type="text" id="location" name="location" placeholder="To match with nearby adoption centers" required>
                
                <label for="skills">Skills/Experience:</label>
                <input type="text" id="skills" name="skills">
                
                <button type="submit" onclick="showSuccessModal()">Submit</button>
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

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the form from actually submitting
            
            // Show the modal
            modal.classList.add('show');
            
            // Optional: Close modal when clicking outside
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.remove('show');
                }
            });

            // Optional: Reset form
            form.reset();

            // Optional: Hide modal after 3 seconds
            setTimeout(() => {
                modal.classList.remove('show');
            }, 3000);
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
