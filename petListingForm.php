<?php
session_start();
require_once 'config/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$isEdit = isset($_GET['edit']);
$pet = null;
$errorMessage = '';

// Fetch pet data if editing
if ($isEdit) {
    try {
        $stmt = $conn->prepare("
            SELECT p.*, 
                   CASE 
                       WHEN p.Center_ID IS NOT NULL THEN ac.CenterName
                       WHEN p.User_ID IS NOT NULL THEN i.Name
                   END AS lister_name
            FROM pets p
            LEFT JOIN individualusers i ON p.User_ID = i.User_ID
            LEFT JOIN adoptioncenters ac ON p.Center_ID = ac.Center_ID
            WHERE p.Pet_ID = ?
        ");
        $stmt->execute([$_GET['edit']]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pet) {
            $_SESSION['error'] = "Pet not found.";
            header('Location: petListing.php');
            exit();
        }

        // Verify ownership
        if ($_SESSION['role'] === 'Center' && $pet['Center_ID'] != $_SESSION['user_id'] ||
            $_SESSION['role'] !== 'Center' && $pet['User_ID'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Unauthorized access.";
            header('Location: petListing.php');
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error fetching pet details: " . $e->getMessage();
        header('Location: petListing.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get user/center ID based on role
        $user_id = null;
        $center_id = null;
        if ($_SESSION['role'] === 'Center') {
            $center_id = $_SESSION['user_id'];
        } else {
            $user_id = $_SESSION['user_id'];
        }

        // Handle file upload if new photo is provided
        $photo = $isEdit ? $pet['Photo'] : ''; // Keep existing photo for edit
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $target_dir = "uploads/pets/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            }
            
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo = $target_file;
                
                // Delete old photo if exists and different
                if ($isEdit && !empty($pet['Photo']) && file_exists($pet['Photo'])) {
                    unlink($pet['Photo']);
                }
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        }

        if ($isEdit) {
            // Update existing pet
            $sql = "UPDATE pets SET 
                    Name = ?, 
                    AnimalType = ?, 
                    Breed = ?, 
                    Gender = ?, 
                    Weight = ?, 
                    Height = ?, 
                    DOB = ?, 
                    Color = ?, 
                    MedicalHistory = ?, 
                    Description = ?
                    " . ($photo !== $pet['Photo'] ? ", Photo = ?" : "") . "
                    WHERE Pet_ID = ?";
            
            $params = [
                $_POST['name'],
                $_POST['animalType'],
                $_POST['breed'],
                $_POST['gender'],
                $_POST['weight'],
                $_POST['height'],
                $_POST['dob'],
                $_POST['color'],
                $_POST['medicalHistory'],
                $_POST['description']
            ];

            if ($photo !== $pet['Photo']) {
                $params[] = $photo;
            }
            $params[] = $_GET['edit'];

        } else {
            // Insert new pet
            $sql = "INSERT INTO pets (User_ID, Center_ID, Name, AnimalType, Breed, Gender, 
                    Weight, Height, Photo, DOB, AdoptionStatus, Color, MedicalHistory, Description) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $user_id,
                $center_id,
                $_POST['name'],
                $_POST['animalType'],
                $_POST['breed'],
                $_POST['gender'],
                $_POST['weight'],
                $_POST['height'],
                $photo,
                $_POST['dob'],
                'Available',
                $_POST['color'],
                $_POST['medicalHistory'],
                $_POST['description']
            ];
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = $isEdit ? "Pet updated successfully!" : "Pet listed successfully!";
        header('Location: petListing.php');
        exit();

    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List a Pet - Petdoption</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            margin-left: auto;
            margin-right: auto;
            margin-top: 30px;
            margin-bottom: 30px;
            width: 833px;
            background-color: #103559;
            color: #fff;
            padding: 20px 30px;
            border-radius: 35px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
            border-radius: 35px;
        }

        .form-header h2 {
            background-color: #ffa500;
            color: #FFFFFF;
            padding: 10px 50px 10px 50px;
            border-radius: 35px;
            display: inline-block;
        }

        .desc p {
            color: #FFFFFF;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
            font-size: 16px;
        }

        .pet-form-container {
            margin-left: auto;
            margin-right: auto;
        }

        .pet-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-row {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .form-group label {
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 2px solid #ffffff;
            border-radius: 35px;
            font-size: 14px;
            background-color: #ffffff;
            color: #000000;
            transition: all 0.3s ease;
        }

        .form-group textarea {
            border-radius: 20px;
            resize: vertical;
            min-height: 120px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ffa500;
            box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
        }

        .button-group {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-form {
            background-color: #ffa500;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 35px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-form:hover {
            background-color: #ff8c00;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="form-container">
        <div class="form-header">
            <h2><?php echo $isEdit ? 'Edit Pet' : 'List a Pet'; ?></h2>
            <div class="desc">
                <p><?php echo $isEdit ? 'Update the details of your pet listing.' : 'Fill in the details about the pet you want to put up for adoption.'; ?></p>
            </div>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <div class="pet-form-container">
            <form class="pet-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Pet Name</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo $pet ? htmlspecialchars($pet['Name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="animalType">Animal Type</label>
                    <select id="animalType" name="animalType" required>
                        <option value="">Select animal type</option>
                        <?php
                        $types = ['Dog', 'Cat', 'Bird', 'Other'];
                        foreach ($types as $type) {
                            $selected = ($pet && $pet['AnimalType'] == $type) ? 'selected' : '';
                            echo "<option value=\"$type\" $selected>$type</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" name="breed" 
                           value="<?php echo $pet ? htmlspecialchars($pet['Breed']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="color">Color</label>
                    <input type="text" id="color" name="color" 
                           value="<?php echo $pet ? htmlspecialchars($pet['Color']) : ''; ?>" required>
                </div>

                <!-- Combined row for gender, weight, and height -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="Male" <?php echo ($pet && $pet['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($pet && $pet['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" step="0.1" 
                               value="<?php echo $pet ? htmlspecialchars($pet['Weight']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="height">Height (cm)</label>
                        <input type="number" id="height" name="height" 
                               value="<?php echo $pet ? htmlspecialchars($pet['Height']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" 
                           value="<?php echo $pet ? htmlspecialchars($pet['DOB']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="photo">Photo</label>
                    <?php if ($pet && $pet['Photo']): ?>
                        <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                             alt="Current pet photo" style="max-width: 200px; margin-bottom: 1rem;">
                        <br>
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <small>Leave empty to keep current photo</small>
                    <?php else: ?>
                        <input type="file" id="photo" name="photo" accept="image/*" required>
                    <?php endif; ?>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="medicalHistory">Medical History</label>
                    <textarea id="medicalHistory" name="medicalHistory" required><?php echo $pet ? htmlspecialchars($pet['MedicalHistory']) : ''; ?></textarea>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo $pet ? htmlspecialchars($pet['Description']) : ''; ?></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-form">
                        <?php echo $isEdit ? 'Save Changes' : 'List Pet'; ?>
                    </button>
                </div>
            </form>
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
</body>
</html>
