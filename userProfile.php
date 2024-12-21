<php

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    /* body {
        font-family: Arial, sans-serif;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f7f7f7;
    } */

    .profile-card {
        margin-left: auto;
        margin-right: auto;
        margin-top: 30px;
        margin-bottom: 30px;
        display: flex;
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 50px;
        overflow: hidden;
        width: 736px;
    }

    .profile-left {
        background-color: #fff5e4;
        padding: 50px;
        text-align: center;
        flex: 1;
    }

    .profile-left img {
        border-radius: 50%;
        width: 200px;
        height: 200px;
        margin-bottom: 20px;
    }

    .profile-left h2 {
        font-size: 24px;
        color: #103559;
        margin-bottom: 10px;
    }

    .profile-left button {
        background-color: #ffcc00;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        font-size: 1rem;
        color: #333;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .profile-left button:hover {
        background-color: #e6b800;
    }

    .profile-right {
        background-color: #F8F8F8;
        padding: 50px;
        flex: 2;
    }

    .profile-right label {
        display: block;
        font-size: 20px;
        font-weight: 500;
        margin-bottom: 5px;
        color: #103559;
    }

    .profile-right input {
        height: 45px;
        width: 100%;
        padding: 10px;
        padding-left: 20px;
        border-radius: 50px;
        background-color:#FFFFFF;
        margin-bottom: 15px;
        font-size: 16px;
        color: #9D9C9C;
    }

    .profile-right input[disabled] {
        background-color: #FFFFFF;
    }

    .btn-primary {
        background-color: var(--yellowbtn);
        padding: 0.6rem 1.2rem;
        border: 1px solid #E7BD43;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        height: 45px;
        width: 131px;
        font-weight: bold;
        border-radius: 50px;
    }

</style>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php';?>

    <div class="profile-card">
        <div class="profile-left">
            <img src="images/woman.png" alt="Profile Picture">
            <h2 id="profile-title">Jozelle Chuah</h2>
            <button id="edit-button" class="btn-primary">Edit</button>
        </div>
        <div class="profile-right">
            <label for="first-name">First name:</label>
            <input type="text" id="first-name" value="Jozelle" disabled>

            <label for="surname">Surname:</label>
            <input type="text" id="surname" value="Chuah" disabled>

            <label for="email">Email:</label>
            <input type="email" id="email" value="shytchuah@gmail.com" disabled>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" value="60123456789" disabled>

            <label for="state">State:</label>
            <input type="text" id="state" value="Selangor" disabled>

            <label for="password">Password:</label>
            <input type="password" id="password" value="password" disabled>
        </div>
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
    <script>
        const editButton = document.getElementById('edit-button');
        const inputs = document.querySelectorAll('.profile-right input');
        const profileTitle = document.getElementById('profile-title');
        const profileDetails = document.getElementById('profile-details');

        // Simulate user type (center or individual)
        const isCenter = true; // Change to false for individual

        if (isCenter) {
            profileTitle.textContent = "Adoption Center Name";
            profileDetails.innerHTML = `
                <label for="center-name">Center Name:</label>
                <input type="text" id="center-name" value="Happy Paws Adoption" disabled>

                <label for="email">Email:</label>
                <input type="email" id="email" value="shytchuah@gmail.com" disabled>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" value="60123456789" disabled>

                <label for="state">State:</label>
                <input type="text" id="state" value="Selangor" disabled>

                <label for="password">Password:</label>
                <input type="password" id="password" value="password" disabled>
            `;
        }

        editButton.addEventListener('click', () => {
            const isEditing = editButton.textContent === 'Save';
            inputs.forEach(input => input.disabled = isEditing);
            editButton.textContent = isEditing ? 'Edit' : 'Save';
        });
    </script>
</body>
</html>
