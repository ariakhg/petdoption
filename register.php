<?php
session_start();
require_once 'config/connection.php';

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountType = $_POST['accountType'];
    
    if ($accountType === 'user') {
        $email = filter_var($_POST['userEmail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['userPassword'];
        $confirmPassword = $_POST['userConfirmPassword'];
        $phone = $_POST['userPhone'];
        $location = $_POST['userLocation'];
    } else {
        $email = filter_var($_POST['centerEmail'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['centerPassword'];
        $confirmPassword = $_POST['centerConfirmPassword'];
        $phone = $_POST['centerPhone'];
        $location = $_POST['centerLocation'];
    }

    try {
        // Check if passwords match
        if ($password !== $confirmPassword) {
            $_SESSION['error'] = "Passwords do not match!";
        } else {
            // Check if email already exists in both tables
            $emailExists = false;
            
            // Check individualusers table
            $stmt = $conn->prepare("SELECT Email FROM individualusers WHERE Email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $emailExists = true;
            }
            
            // Check adoptioncenters table
            $stmt = $conn->prepare("SELECT Email FROM adoptioncenters WHERE Email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $emailExists = true;
            }
            
            if ($emailExists) {
                $_SESSION['error'] = "Email already exists! Please use a different email.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                if ($accountType === 'user') {
                    $firstName = $_POST['firstName'];
                    $lastName = $_POST['lastName'];
                    $fullName = $firstName . ' ' . $lastName;
                    
                    // Insert user
                    $stmt = $conn->prepare("INSERT INTO individualusers (Name, Email, PhoneNo, Location, Password, Role) 
                                          VALUES (?, ?, ?, ?, ?, 'User')");
                    if ($stmt->execute([$fullName, $email, $phone, $location, $hashedPassword])) {
                        $_SESSION['success'] = "Registration successful! Please login.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Failed to create user account.";
                    }
                    
                } else if ($accountType === 'adoption-center') {
                    $centerName = $_POST['centerName'];
                    
                    // Insert adoption center
                    $stmt = $conn->prepare("INSERT INTO adoptioncenters (CenterName, Email, PhoneNo, Location, Password, Role, AvgRating) 
                                          VALUES (?, ?, ?, ?, ?, 'Center', 0.0)");
                    if ($stmt->execute([$centerName, $email, $phone, $location, $hashedPassword])) {
                        $_SESSION['success'] = "Registration successful! Please login.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Failed to create adoption center account.";
                    }
                }
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    }
}
?>

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
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <form class="register-form" method="POST">
                    <div class="account-type">
                        <p>I am a...</p>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="accountType" value="user">
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
                            <input type="text" name="firstName" placeholder="First Name" required>
                            <input type="text" name="lastName" placeholder="Last Name" required>
                        </div>
                        <input type="email" name="userEmail" placeholder="Email" required>
                        <input type="tel" name="userPhone" placeholder="Phone Number" required>
                        <input type="text" name="userLocation" placeholder="Location" required>
                        <input type="password" name="userPassword" placeholder="Password" required>
                        <input type="password" name="userConfirmPassword" placeholder="Confirm Password" required>
                    </div>

                    <!-- Adoption Center form fields -->
                    <div id="adoption-center-fields" class="form-fields" style="display: none;">
                        <input type="text" name="centerName" placeholder="Adoption Center Name">
                        <input type="email" name="centerEmail" placeholder="Email">
                        <input type="tel" name="centerPhone" placeholder="Phone Number">
                        <input type="text" name="centerLocation" placeholder="Location">
                        <input type="password" name="centerPassword" placeholder="Password">
                        <input type="password" name="centerConfirmPassword" placeholder="Confirm Password">
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
            <p>&copy; 2024 Petdoption. All rights reserved.</p>
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
            
            function toggleRequired(fields, required) {
                fields.querySelectorAll('input').forEach(input => {
                    input.required = required;
                });
            }
        
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'user') {
                        userFields.style.display = 'block';
                        adoptionCenterFields.style.display = 'none';
                        toggleRequired(userFields, true);
                        toggleRequired(adoptionCenterFields, false);
                    } else {
                        userFields.style.display = 'none';
                        adoptionCenterFields.style.display = 'block';
                        toggleRequired(userFields, false);
                        toggleRequired(adoptionCenterFields, true);
                    }
                });
            });
        });
    </script>
</body>
</html>