<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pet Listings</title>
    <link rel="stylesheet" href="css/petListing.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-links">
                <img src="assets/logo.png" alt="Petdoption Logo">
                <a href="">Find a Pet</a>
                <a href="">List a Pet</a>
                <a href="">Volunteer</a>
            </div>
            <div class="nav-links">
                <a class="nav-bar-icon" href="">
                    <img src="assets/saved-pets-icon.png">
                </a>
                <a class="nav-bar-icon" href="">
                    <img src="assets/chat-icon.png">
                </a>
                <img class="nav-profile" src="">
                <a href="login.php">Log Out</a>
            </div>
        </div>
    </nav>
    <div class="parentBox">
        <div class="content-header">
            <h1 class="title">My Pet Listings</h1>
            <div class="buttons">
                <button class="btn adoption-btn">
                    Adoption Requests
                    <img src="assets/eye-icon.png">
                </button>
                <button class="btn list-pet-btn">
                    List A New Pet
                    <img src="assets/plus-icon.png">
                </button>
            </div>
        </div>
    </div>
    <div class="parentBox2">
    <div class="pet-card">
    <div class="pet-info">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
            <div class="pet-details">
                <h2>Mochi</h2>
                <p>Dog, Pitbull</p>
            </div>
        <div class="status-badge available">Available</div>
    </div>
    <div class="action-buttons">
        <button class="btn edit-btn">Edit</button>
            <div class="dropdown">
                <button class="btn update-btn">Update</button>
                <div class="dropdown-content">
                    <a href="#">Available</a>
                    <a href="#">Reserved</a>
                    <a href="#">Adopted</a>
                </div>
            </div>
        <button class="btn delete-btn">Delete</button>
        </div>
    </div>
    </div>
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