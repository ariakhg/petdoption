<?php
session_start();
require 'config/connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

error_log("Session data: " . print_r($_SESSION, true));
error_log("User ID: " . ($_SESSION['user_id'] ?? 'not set'));
error_log("User Role: " . ($_SESSION['role'] ?? 'not set'));

$error_message = "";
$success_message = "";
$default_image = 'images/defaultprofile.png';

// Retrieve user data function
function getUserData($conn, $userId, $userRole) {
    $table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
    $idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';
    
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $idField = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Update user data function
function updateUser($conn, $userId, $userRole, $data) {
    $table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
    $idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';
    
    try {
        $conn->beginTransaction();
        
        $updates = [];
        $params = [];

        // Handle fields based on role
        if ($userRole === 'Center') {
            if (!empty($data['centerName'])) {
                $updates[] = "CenterName = ?";
                $params[] = $data['centerName'];
            }
        } else {
            if (!empty($data['userFirstName']) && !empty($data['userLastName'])) {
                $updates[] = "Name = ?";
                $params[] = trim($data['userFirstName'] . ' ' . $data['userLastName']);
            }
        }

        // Handle common fields
        $emailField = $userRole === 'Center' ? 'centerEmail' : 'userEmail';
        if (!empty($data[$emailField])) {
            $updates[] = "Email = ?";
            $params[] = $data[$emailField];
        }

        $phoneField = $userRole === 'Center' ? 'centerPhone' : 'userPhone';
        if (!empty($data[$phoneField])) {
            $updates[] = "PhoneNo = ?";
            $params[] = $data[$phoneField];
        }

        $stateField = $userRole === 'Center' ? 'centerState' : 'userState';
        if (!empty($data[$stateField])) {
            $updates[] = "Location = ?";
            $params[] = $data[$stateField];
        }

        // Handle password update
        if (!empty($data['password'])) {
            $updates[] = "Password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!empty($updates)) {
            $params[] = $userId;
            $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $idField = ?";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($params);
            $conn->commit();
            return $result;
        }
        
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
}

// Check if user is logged in and retrieve user data
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

// Add form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    error_log("Form submitted: " . print_r($_POST, true));
    $response = ['success' => false, 'message' => ''];
    
    try {
        error_log("Attempting to update user...");
        if (updateUser($conn, $_SESSION['user_id'], $_SESSION['role'], $_POST)) {
            error_log("Update successful");
            $response['success'] = true;
            $response['message'] = "Profile updated successfully";
            
            // Update session data if name was changed
            if ($_SESSION['role'] === 'Center' && !empty($_POST['centerName'])) {
                $_SESSION['name'] = $_POST['centerName'];
                $response['newName'] = $_POST['centerName'];
            } elseif (!empty($_POST['userFirstName']) && !empty($_POST['userLastName'])) {
                $newName = trim($_POST['userFirstName'] . ' ' . $_POST['userLastName']);
                $_SESSION['name'] = $newName;
                $response['newName'] = $newName;
            }
        } else {
            $response['message'] = "No changes were made";
        }
    } catch (Exception $e) {
        error_log("Error updating user: " . $e->getMessage());
        $response['message'] = $e->getMessage();
    }
    
    error_log("Sending response: " . print_r($response, true));
    echo json_encode($response);
    exit();
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
            <button type="button" id="edit-button" class="btn-primary">Edit</button>
        </div>
        <div class="profile-right">
            <form id="profileForm" method="POST">
                <input type="hidden" name="update" value="true">
                
                <?php if ($userRole === 'Center'): ?>
                    <div class="name-input-container">
                        <label for="center-name">Center Name:</label>
                        <input type="text" id="center-name" name="centerName" value="<?php echo htmlspecialchars($userData['CenterName']); ?>" disabled required>
                    </div>
                <?php else: ?>
                    <div class="name-group">
                        <div class="name-input-container">
                            <label for="first-name">First name:</label>
                            <input type="text" id="first-name" name="userFirstName" value="<?php echo htmlspecialchars(explode(' ', $userData['Name'])[0] ?? ''); ?>" disabled required>
                        </div>
                        <div class="name-input-container">
                            <label for="surname">Last name:</label>
                            <input type="text" id="surname" name="userLastName" value="<?php echo htmlspecialchars(explode(' ', $userData['Name'])[1] ?? ''); ?>" disabled required>
                        </div>
                    </div>
                <?php endif; ?>

                <label for="email">Email:</label>
                <input type="email" id="email" name="<?php echo $userRole === 'Center' ? 'centerEmail' : 'userEmail'; ?>" 
                       value="<?php echo htmlspecialchars($userData['Email']); ?>" disabled required>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="<?php echo $userRole === 'Center' ? 'centerPhone' : 'userPhone'; ?>" 
                       value="<?php echo htmlspecialchars($userData['PhoneNo']); ?>" disabled required>

                <label for="state">State:</label>
                <input type="text" id="state" name="<?php echo $userRole === 'Center' ? 'centerState' : 'userState'; ?>" 
                       value="<?php echo htmlspecialchars($userData['Location']); ?>" disabled required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="********" disabled>

                <div id="confirm-password-field" style="display: none;">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm-password">
                    <p class="form-error" id="password-error"></p>
                </div>

                <div class="button-group" style="display: none;">
                    <button type="button" id="cancel-button" class="btn-secondary">Cancel</button>
                    <button type="submit" name="update" class="btn-primary">Save</button>
                </div>

                <p id="form-error-message" class="form-error"></p>
                <p id="form-success-message" class="form-success"></p>
            </form>
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
            const profileImage = document.getElementById('profile-image');
            const navProfileImage = document.querySelector('.nav-profile');
            const errorMessage = document.getElementById('form-error-message');
            const successMessage = document.getElementById('form-success-message');

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
                    document.getElementById('form-error-message').textContent = 'Please upload a JPG, PNG, or GIF file.';
                    return;
                }

                const formData = new FormData();
                formData.append('picture', file);

                // Show loading state
                profileImage.style.opacity = '0.5';
                if (navProfileImage) navProfileImage.style.opacity = '0.5';

                fetch('handlers/updateProfilePic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update both profile images
                        profileImage.src = data.picture;
                        if (navProfileImage) navProfileImage.src = data.picture;
                        
                        document.getElementById('form-success-message').textContent = data.message;
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
                    // Reset opacity
                    profileImage.style.opacity = '1';
                    if (navProfileImage) navProfileImage.style.opacity = '1';
                    pictureInput.value = ''; // Reset file input
                });
            });

            // Handle form submission
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
                
                const formData = new FormData(this);
                formData.append('update', 'true');
                
                // Log form data
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // Add loading state
                const submitButton = buttonGroup.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.textContent;
                submitButton.textContent = 'Saving...';
                submitButton.disabled = true;

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Update profile title if name was changed
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
                        passwordInput.value = '**********';
                        passwordInput.disabled = true;
                        confirmPasswordField.style.display = 'none';
                        buttonGroup.style.display = 'none';
                        editButton.style.display = 'block';
                        
                        successMessage.textContent = data.message;
                        errorMessage.textContent = '';
                    } else {
                        errorMessage.textContent = data.message;
                        successMessage.textContent = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.textContent = 'Update failed';
                })
                .finally(() => {
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                });
            });
        });
    </script>
</body>
</html>
