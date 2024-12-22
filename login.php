<?php
session_start();
require_once 'config/connection.php';

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $accountType = $_POST['accountType'];
    $remember = isset($_POST['remember']) ? true : false;

    try {
        switch ($accountType) {
            case 'user':
                $stmt = $conn->prepare("SELECT * FROM individualusers WHERE Email = ?");
                break;
            case 'adoption-center':
                $stmt = $conn->prepare("SELECT * FROM adoptioncenters WHERE Email = ?");
                break;
            case 'admin':
                $stmt = $conn->prepare("SELECT * FROM admin WHERE Email = ?");
                break;
        }

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Email address not found. Please check your email or register for a new account.";
        } elseif (!password_verify($password, $user['Password'])) {
            $_SESSION['error'] = "Incorrect password. Please try again.";
        } else {
            // Login successful
            $_SESSION['user_id'] = $user[($accountType === 'user' ? 'User_ID' : 
                                        ($accountType === 'adoption-center' ? 'Center_ID' : 'Admin_ID'))];
            $_SESSION['role'] = $user['Role'];
            $_SESSION['email'] = $user['Email'];

            // Handle remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                
                $stmt = $conn->prepare("UPDATE " . ($accountType === 'user' ? 'individualusers' : 
                                                 ($accountType === 'adoption-center' ? 'adoptioncenters' : 'admin')) . 
                                     " SET remember_token = ? WHERE Email = ?");
                $stmt->execute([$token, $email]);

                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/');
                setcookie('user_role', $user['Role'], time() + (30 * 24 * 60 * 60), '/');
            }

            // Redirect based on role
            switch ($user['Role']) {
                case 'User':
                    header("Location: findAPet.php");
                    break;
                case 'Center':
                    header("Location: petListing.php");
                    break;
                case 'Admin':
                    header("Location: adminDashboard.php");
                    break;
            }
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred. Please try again later.";
    }
}
?>

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
                <a href="login.php#aboutus">About us</a>
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
                                Center
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="accountType" value="admin">
                                Admin
                            </label>
                        </div>
                    </div>
                    <div class="form-fields">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn-form">Log In</button>
                    <p class="change-prompt">
                        Don't have an account? <a href="register.php">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section class="aboutus" id="aboutus">
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