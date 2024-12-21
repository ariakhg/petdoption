<?php
session_start();
require 'config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
$idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $idField = ?");
    $stmt->execute([$userId]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-card {
            margin: 30px auto;
            display: flex;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            width: 800px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-left {
            background-color: #FFF5E4;
            padding: 100px 40px 40px 40px;
            text-align: center;
            align-items: center;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .profile-right {
            background-color: #F8F8F8;
            padding: 40px;
            flex: 2;
        }

        .profile-pic-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .profile-pic-container img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #666;
            object-fit: cover;
        }

        .profile-right label {
            display: block;
            color: #333;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 16px;
        }

        .profile-right input {
            width: 100%;
            padding: 12px 16px 12px 16px;
            border: 1px solid #ddd;
            border-radius: 50px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .profile-right input:disabled {
            background-color: #f5f5f5;
            color: #666;
        }
        .name-input-container {
            display: flex;
            flex-direction: column; /* Stack the label above the input */
            flex: 1; /* Make both fields equal width */
        }

        .name-input-container label {
            margin-bottom: 5px; /* Add spacing between the label and input */
            font-weight: 500;
            color: #333;
        }

        .name-input-container input {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 14px;
        }

        .button-group {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 1rem;
        }

        #edit-button {
            justify-content: center;
            margin-top: 20px;
        }

        .form-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .form-success {
            color: #28a745;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .upload-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #FFD93D;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .upload-icon img {
            width: 35px;
            height: 35px;
        }

        #picture {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-card">
        <div class="profile-left">
            <div class="profile-pic-container">
                <img id="profile-image" src="<?php echo $userData['ProfilePic'] ?: 'images/woman.png'; ?>" alt="Profile Picture">
                <label for="picture" class="upload-icon">
                    <img src="assets/upload-icon.png" alt="Upload">
                </label>
                <input type="file" id="picture" name="picture" accept="image/*">
            </div>
            <h2 id="profile-title"><?php echo $userRole === 'Center' ? $userData['CenterName'] : $userData['Name']; ?></h2>
            <button id="edit-button" class="btn-primary">Edit</button>
            <div class="button-group" style="display: none;">
                <button type="button" id="cancel-button" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </div>
        <div class="profile-right">
            <form id="profileForm">
                <div class="name-group">
                    <div class="name-input-container">
                        <label for="first-name">First name:</label>
                        <input type="text" id="first-name" name="first-name" value="<?php echo explode(' ', $userData['Name'])[0] ?? ''; ?>" disabled required>
                    </div>
                    <div class="name-input-container">
                        <label for="last-name">Last name:</label>
                        <input type="text" id="last-name" name="last-name" value="<?php echo explode(' ', $userData['Name'])[1] ?? ''; ?>" disabled required>
                    </div>
                </div>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $userData['Email']; ?>" disabled required>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $userData['PhoneNo']; ?>" disabled required>

                <label for="state">State:</label>
                <input type="text" id="state" name="state" value="<?php echo $userData['Location']; ?>" disabled required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="********" disabled>

                <div id="confirm-password-field" style="display: none;">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password">
                    <p class="form-error" id="password-error"></p>
                </div>

            </form>
            <p id="form-error-message" class="form-error"></p>
            <p id="form-success-message" class="form-success"></p>
        </div>
    </div>

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
            const editButton = document.getElementById('edit-button');
            const inputs = document.querySelectorAll('.profile-right input:not([type="password"])');
            const passwordInput = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm-password-field');
            const buttonGroup = document.querySelector('.button-group');
            const profileForm = document.getElementById('profileForm');
            const cancelButton = document.getElementById('cancel-button');
            const pictureInput = document.getElementById('picture');

            // Store original values
            let originalValues = {};
            inputs.forEach(input => {
                originalValues[input.id] = input.value;
            });

            // Handle Edit button click
            editButton.addEventListener('click', () => {
                inputs.forEach(input => input.disabled = false);
                passwordInput.disabled = false;
                passwordInput.value = ''; // Clear password field
                confirmPasswordField.style.display = 'block';
                buttonGroup.style.display = 'flex';
                editButton.style.display = 'none';
            });

            // Handle Cancel button click
            cancelButton.addEventListener('click', () => {
                inputs.forEach(input => {
                    input.value = originalValues[input.id];
                    input.disabled = true;
                });
                passwordInput.value = '********';
                passwordInput.disabled = true;
                confirmPasswordField.style.display = 'none';
                buttonGroup.style.display = 'none';
                editButton.style.display = 'block';
                document.getElementById('password-error').textContent = '';
                document.getElementById('form-error-message').textContent = '';
                document.getElementById('form-success-message').textContent = '';
            });

            // Handle profile picture upload
            pictureInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please upload a JPG, PNG, or GIF file.');
                    return;
                }

                const formData = new FormData();
                formData.append('picture', file);

                // Show loading state
                const profileImage = document.getElementById('profile-image');
                profileImage.style.opacity = '0.5';

                fetch('handlers/updateProfilePic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update profile image
                        profileImage.src = data.picture;
                        document.getElementById('form-success-message').textContent = 'Profile picture updated successfully';
                        document.getElementById('form-error-message').textContent = '';
                    } else {
                        document.getElementById('form-error-message').textContent = data.message;
                        document.getElementById('form-success-message').textContent = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('form-error-message').textContent = 'Failed to upload profile picture';
                })
                .finally(() => {
                    profileImage.style.opacity = '1';
                    pictureInput.value = ''; // Reset file input
                });
            });

            // Handle form submission
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate passwords if changed
                const password = passwordInput.value;
                const confirmPassword = document.getElementById('confirm-password').value;
                
                if (password && password !== confirmPassword) {
                    document.getElementById('password-error').textContent = 'Passwords do not match';
                    return;
                }

                const formData = new FormData(this);
                
                // Add loading state to save button
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.textContent;
                submitButton.textContent = 'Saving...';
                submitButton.disabled = true;

                fetch('handlers/updateProfile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update profile title with new name
                        if (data.newName) {
                            document.getElementById('profile-title').textContent = data.newName;
                        }
                        
                        // Update original values
                        inputs.forEach(input => {
                            if (input.type !== 'password') {
                                originalValues[input.id] = input.value;
                            }
                        });
                        
                        // Reset form state
                        inputs.forEach(input => input.disabled = true);
                        passwordInput.value = '********';
                        passwordInput.disabled = true;
                        confirmPasswordField.style.display = 'none';
                        buttonGroup.style.display = 'none';
                        editButton.style.display = 'block';
                        
                        document.getElementById('form-success-message').textContent = data.message;
                        document.getElementById('form-error-message').textContent = '';
                    } else {
                        document.getElementById('form-error-message').textContent = data.message;
                        document.getElementById('form-success-message').textContent = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('form-error-message').textContent = 'Update failed';
                })
                .finally(() => {
                    // Reset button state
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
