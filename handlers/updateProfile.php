<?php
session_start();
require '../config/connection.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'newName' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Not logged in';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
$idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';

try {
    $conn->beginTransaction();
    
    $updates = [];
    $params = [];

    // Handle name fields based on role
    if ($userRole === 'Center') {
        if (!empty($_POST['centerName'])) {
            $updates[] = "CenterName = ?";
            $params[] = $_POST['centerName'];
            $response['newName'] = $_POST['centerName'];
        }
    } else {
        if (!empty($_POST['userFirstName']) && !empty($_POST['userLastName'])) {
            $fullName = trim($_POST['userFirstName'] . ' ' . $_POST['userLastName']);
            $updates[] = "Name = ?";
            $params[] = $fullName;
            $response['newName'] = $fullName;
        }
    }

    // Handle email update with uniqueness check
    $emailField = $userRole === 'Center' ? 'centerEmail' : 'userEmail';
    if (!empty($_POST[$emailField])) {
        $stmt = $conn->prepare("SELECT $idField FROM $table WHERE Email = ? AND $idField != ?");
        $stmt->execute([$_POST[$emailField], $userId]);
        if ($stmt->fetch()) {
            throw new Exception('Email already exists');
        }
        $updates[] = "Email = ?";
        $params[] = $_POST[$emailField];
    }

    // Handle other fields
    $phoneField = $userRole === 'Center' ? 'centerPhone' : 'userPhone';
    if (!empty($_POST[$phoneField])) {
        $updates[] = "PhoneNo = ?";
        $params[] = $_POST[$phoneField];
    }

    $stateField = $userRole === 'Center' ? 'centerState' : 'userState';
    if (!empty($_POST[$stateField])) {
        $updates[] = "Location = ?";
        $params[] = $_POST[$stateField];
    }

    // Handle password update
    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['confirm-password']) {
            throw new Exception('Passwords do not match');
        }
        $updates[] = "Password = ?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if (!empty($updates)) {
        // Add WHERE clause parameter
        $params[] = $userId;
        
        // Construct and execute update query
        $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $idField = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute($params)) {
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Profile updated successfully';
            
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
    $response['message'] = $e->getMessage();
} catch (PDOException $e) {
    $conn->rollBack();
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
exit();
?>
