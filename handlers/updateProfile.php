<?php
session_start();
require '../config/connection.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Not logged in';
    echo json_encode($response);
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
$idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    try {
        $conn->beginTransaction();

        // Get form data
        $new_email = $_POST['email'];
        $new_phone = $_POST['phone'];
        $new_state = $_POST['state'];
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

        // Initialize update arrays
        $updates = [];
        $params = [];

        // Handle name based on user role
        if ($userRole === 'Center') {
            if (!empty($_POST['center-name'])) {
                $updates[] = "CenterName = ?";
                $params[] = $_POST['center-name'];
                $response['newName'] = $_POST['center-name'];
            }
        } else {
            if (!empty($_POST['first-name']) && !empty($_POST['surname'])) {
                $fullName = trim($_POST['first-name'] . ' ' . $_POST['surname']);
                $updates[] = "Name = ?";
                $params[] = $fullName;
                $response['newName'] = $fullName;
            }
        }

        // Add other fields to update
        if (!empty($new_email)) {
            // Check if email exists
            $stmt = $conn->prepare("SELECT $idField FROM $table WHERE Email = ? AND $idField != ?");
            $stmt->execute([$new_email, $userId]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }
            $updates[] = "Email = ?";
            $params[] = $new_email;
        }

        if (!empty($new_phone)) {
            $updates[] = "PhoneNo = ?";
            $params[] = $new_phone;
        }

        if (!empty($new_state)) {
            $updates[] = "Location = ?";
            $params[] = $new_state;
        }

        // Handle password update if provided
        if (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                throw new Exception('Passwords do not match');
            }
            $updates[] = "Password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        // If there are updates to make
        if (!empty($updates)) {
            $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $idField = ?";
            $params[] = $userId;

            $stmt = $conn->prepare($sql);
            if ($stmt->execute($params)) {
                $conn->commit();
                $response['success'] = true;
                $response['message'] = !empty($new_password) ? 
                    'Profile updated successfully with new password' : 
                    'Profile updated successfully';

                // Update session data
                if (isset($response['newName'])) {
                    $_SESSION['name'] = $response['newName'];
                }
            } else {
                throw new Exception('Failed to update profile');
            }
        } else {
            $response['message'] = 'No changes to update';
        }

    } catch (Exception $e) {
        $conn->rollBack();
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['success'] = false;
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit();
?>
