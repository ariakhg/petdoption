<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List A Pet Form</title>
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
        margin-top: 20px;
        border-radius: 35px;
    }
    .form-header h2 {
        width: 254px;
        height: 50px;
        background-color: #FCAD35;
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

    .petlist-container{
        margin-left: auto;
        margin-right: auto;
    }
    .petlist-form {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .petlist-form label {
        margin-left: 150px;
        font-size: 20px;
        font-weight: 500;
        color: #ffffff;
    }
    .petlist-form input {
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

    .petlist-form small{
        font-size: 10px;
        font-weight: 400;
        color: #FFFFFF;
        margin-top: -5px;
        margin-bottom: -25px;
        margin-left: 150px;
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
        margin-bottom: 30px;
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

    .radio {
        color: #ffffff;
        display: flex;
        justify-content: space-evenly;
        gap: 0.1rem;
    }

    .radio-label {
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 20px;
        margin-right: 150px;
    }
    .radio input {
        width: 15px;
        height: 15px;
        margin-top: 40px;
        margin-left: -100px;
        margin-right: -200px;
    }

    .drop-option{
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 20px;
        padding: 10px 20px 10px 20px;
        width: 488px;
        height: 45px;
        border-radius: 35px;
        font-size: 16px;
        font-weight: 500;
        color:rgb(0, 0, 0);
    }
    .drop-option option{
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

    .form-group {
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 1rem;
        color: #ffffff;
    }

    .form-group label {
        display: block;
        margin-left: 10px;
        margin-bottom: 1rem;
    }

    .form-group input {
        padding: 10px 30px 10px 30px;
        background: #ffffff;
        margin-top: -10px;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 20px;
        width: 488px;
        height: 45px;
        border-radius: 35px;
    }

    .measurements {
        display: flex;
        gap: 1rem;
        align-items: left;
        margin-left: auto;
        margin-right: auto;
    }

    .measurements input {
        width: 80px;
    }

    .metrics{
        width: 60px;
        height: 35px;
    }

    .metrics-label {
        font-size: 20px;
        font-weight: 500;
        color: #ffffff;
    }

    .history{
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 20px;
        width: 488px;
        height: 45px;
        border-radius: 35px;
        font-size: 16px;
        font-weight: 500;
        color:rgb(0, 0, 0);
        padding: 10px 30px 10px 30px;
        overflow:hidden;
    }

    .t label{
        margin-left: 5px;
    }

    .t input{
        margin-left: 5px;
    }


</style>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>

    <div class="form-container">
        <div class="form-header">
            <h2>List A Pet</h2>
        </div>
        <div class="petlist-container">
            <form class="petlist-form" id="myForm">
                <div class="radio">
                    <label>I am a...</label>
                    <input type="radio" id="user" name="userType" value="user" checked>
                    <label class="radio-label" for="user">User</label>
                    <input type="radio" id="adoptionCenter" name="userType" value="adoptionCenter">
                    <label class="radio-label" for="adoptionCenter">Adoption Center</label>
                </div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="seller-fullname">Seller's Full Name:</label>
                <input type="text" id="full-name" name="full-name" required>
                
                <label for="date-listing">Date Listing:</label>
                <input type="date" id="date-listing" name="date-listing" required>
                
                <label>Type of Animal:</label>
                <select name="animalType" id="animalType" class="drop-option">
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Bird">Bird</option>
                    <option value="Others">Others</option>
                </select>
                
                <label>Pet's Name:</label>
                <small>* If the pet does not have one, do create one.</small><br>
                <input type="text" name="petName" required>
                
                <div class="measurements">
                    <div class="t">
                    <label>Height:</label>
                    <input type="number" class="metrics" name="height" placeholder="CM" required>
                    </div>
                    <div class="t">
                    <label class="metrics-label">Weight:</label>
                    <input type="number" class="metrics" name="weight" placeholder="KG" required>
                    </div>
                    <div class="t">
                    <label>Colour(s):</label>
                    <input type="text" class="metrics" name="colors" required>
                    </div>

                </div>
                <div class="radio">
                    <label>Gender:</label>
                    <input type="radio" id="male" name="gender" value="male" checked>
                    <label class="radio-label" for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="female">
                    <label class="radio-label" for="female">Female</label>
                </div>
                
                <label>Vaccination History:</label>
                <textarea name="vaccination" class="history" rows="3" required></textarea>

                <div class="form-group">
                <label>Attach image of pet:</label>
                <input type="file" name="petImage" accept="image/*" class="file-upload" required>
                </div>
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
            <h2 class="modal-thanks">Pet Listed Successfully!</h2>
            <p class="modal-thankstext">View Pet Listed to see your pet update.</p>
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
