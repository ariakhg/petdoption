<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petdoption - Login</title>
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
    <main>
        <div class="landing-title">
            <h1>Every Pet Deserves a Loving Home.</h1>
            <h2>Adopt a Pet at Petdoption Now!</h2>
        </div>
        <div class="landing">
            <!-- Left side - Image -->
            <div class="landing-img">
                <img src="assets/landing.png" alt="Happy pets">
            </div>

            <!-- Right side - Login Form -->
            <div class="login-form">
                <h2>Welcome Back</h2>
                <form id="loginForm" method="POST">
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
                    <div class="form-fields">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn-form">Log In</button>
                    <p class="change-prompt">
                        Don't have an account? <a href="register.php">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="aboutus">
        <div class="aboutus-container">
            <div class="aboutus-card">
                <h3>Find</h3>
                <p>Find a pet that catches your eye!</p>
            </div>
            <div class="aboutus-card">
                <h3>Connect</h3>
                <p>Connect with the lister to arrange a meet up.</p>
            </div>
            <div class="aboutus-card">
                <h3>Adopt</h3>
                <p>Bring your chosen pet home!</p>
            </div>
        </div>
    </section>
    </main>

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
</body>
</html>