<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petdoption - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-links">
                <img src="assets/logo.png" alt="Petdoption Logo">
                <a href="login.php">About us</a>
            </div>
            <div class="nav-links">
                <a href="login.php" class="btn-secondary">Log In</a>
                <a href="register.php" class="btn-primary">Register</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="register-container">
        <div class="register-card">
            <div class="left-section">
                <h1>Join the<br>Petdoption Family</h1>
                <p>Sign up to find pets, list animals for adoption, or volunteer to make a difference!</p>
                <img src="assets/register.png" alt="Pets House Illustration" class="register-img">
            </div>
            
            <div class="right-section">
                <h2>Create Account</h2>
                <form class="register-form">
                    <div class="account-type">
                        <p>I am a...</p>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="accountType" value="user" checked>
                                User
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="accountType" value="adoption-center">
                                Adoption Center
                            </label>
                        </div>
                    </div>

                    <!-- User form fields -->
                    <div id="user-fields" class="form-fields">
                        <div class="name-group">
                            <input type="text" placeholder="First Name" required>
                            <input type="text" placeholder="Last Name" required>
                        </div>
                        <input type="email" placeholder="Email" required>
                        <input type="tel" placeholder="Phone Number" required>
                        <input type="text" placeholder="State" required>
                        <input type="password" placeholder="Password" required>
                        <input type="password" placeholder="Confirm Password" required>
                    </div>

                    <!-- Adoption Center form fields -->
                    <div id="adoption-center-fields" class="form-fields" style="display: none;">
                        <input type="text" placeholder="Adoption Center Name" required>
                        <input type="email" placeholder="Email" required>
                        <input type="tel" placeholder="Phone Number" required>
                        <input type="text" placeholder="State" required>
                        <input type="password" placeholder="Password" required>
                        <input type="password" placeholder="Confirm Password" required>
                    </div>

                    <button type="submit" class="btn-form">Register</button>

                    <p class="change-prompt">Already have an account? <a href="login.php">Log In</a></p>
                </form>
            </div>
        </div>
    </main>

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
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="accountType"]');
            const userFields = document.getElementById('user-fields');
            const adoptionCenterFields = document.getElementById('adoption-center-fields');
        
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'user') {
                        userFields.style.display = 'block';
                        adoptionCenterFields.style.display = 'none';
                    } else {
                        userFields.style.display = 'none';
                        adoptionCenterFields.style.display = 'block';
                    }
                });
            });
        });
    </script>
</body>
</html>