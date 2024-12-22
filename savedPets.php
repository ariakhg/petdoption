<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Saved Pets</title>
    <link rel="stylesheet" href="css/savedPets.css">
    <script src="javascript/savedPets.js" defer></script>
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
    <h1>
        Saved / Applied
    </h1>
    <div class="container">
    <div class="tabs">
      <div class="tab active" onclick="switchTab('saved')">Saved</div>
      <div class="tab" onclick="switchTab('applied')">Applied</div>
    </div>
    <div id="saved-content" class="tab-content active">
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <button class="button remove-button">Remove</button>
      </div>
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <button class="button remove-button">Remove</button>
      </div>
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <button class="button remove-button">Remove</button>
      </div>
    </div>
    <div id="applied-content" class="tab-content">
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <div class="pet-actions">
          <button class="button under-review">Under Review</button>
        </div>
      </div>
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <div class="pet-actions">
          <button class="button approved">Approved</button>
          <button class="review-button" onclick="openForm()">Give a rating / review</button>
          <div id="reviewForm" class="form-popup">
            <div class="form-container">
                <h2>Submit a review</h2>
                
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
                    <textarea placeholder="Type in your review" rows="5"></textarea>
                </div>

                <div class="button-container">
                    <button class="cancel-btn" onclick="closeForm()">Cancel</button>
                    <button class="submit-btn" onclick="submitReview()">Submit</button>
                </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pet-card">
        <img src="assets/mochi.jpg" alt="Mochi" class="pet-image">
        <div class="pet-info">
          <h3 class="pet-name">Mochi</h3>
          <p class="pet-breed">Dog, Pitbull</p>
        </div>
        <div class="pet-actions">
          <button class="button declined">Declined</button>
        </div>
      </div>
    </div>
  </div><footer>
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