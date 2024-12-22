<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Profile</title>
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
        font-size: 24px;
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
        margin-bottom: 1rem;
    }
    .card {
        background-color: #e7f3ff;
        border-radius: 50px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 1045px;
        height: 132px;
        padding: 20px;
        display: flex;
        flex-direction: row;
        /* position: relative; */
    }
    .pet-image {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        margin-top: auto;
        margin-bottom: auto;
        margin-left: 50px;
    }
    .pet-name{
        margin-top: 20px;
        margin-left: 20px;
        font-size: 40px;
        height: 54px;
    }
    .pet-details {
        margin-left: 20px;
        margin-right: 40px;
        height: 100px;
        display: grid;
        background-color: #E4F4FF;
        padding: 20px;
        border-radius: 10px;
        width: 40%; /* Adjust the width to fit the content */
    }

    .detail-item {
    text-align: left;
    }

    .detail-item h3 {
    margin: 0;
    font-size: 24px;
    color: #103559;
    font-weight: bold;
    }

    .detail-item p {
    margin: 5px 0 0;
    font-size: 14px;
    font-weight: 500;
    color: #103559;
    }

    .btn-primary {
        margin-top: 25px;
        margin-left: -80px;
        margin-right: 10px;
        background-color: var(--yellowbtn);
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        border: 1px solid #E7BD43;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        height: 45px;
        width: 153px;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: var(--activeyellow);
    }

    .btn-crud{
        margin-top: 24px;
        margin-left: 10px;
        border-radius: 50%;
        background-color: #E4F4FF;
        width: 48px; 
        height: 48px;
        border: none;
        cursor: pointer;
    }
    .crud{
        width: 48px; 
        height: 48px;
        border-radius: 50%;
    }
</style>
<body>
    <!-- Navigation Bar -->
    <?php include 'navbar.php';?>

    <a href="#" class="back-link">< Adoption Request</a>
    <div class="card-container">
        <div class="card">
            <img src="images/dog1.jpg" alt="Mochi" class="pet-image">
            <h2 class="pet-name">Mochi</h2>
            <div class="pet-details">
                <div class="detail-item">
                    <h3>Requested by</h3>
                    <p>jozelle@gmail.com</p>
                </div>
            </div>
            <button id="chatButton" class="btn-primary">Chat with User</button>
            <button class="btn-crud"><img src="ui/eye.png" alt="view" class="crud"></button>
            <button class="btn-crud"><img src="ui/reject.png" alt="reject" class="crud"></button>
            <button class="btn-crud"><img src="ui/accept.png" alt="accept" class="crud"></button>
        </div>
    </div>
    <br><br><br>

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