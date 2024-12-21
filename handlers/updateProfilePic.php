<?php
session_start();
require '../config/connection.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'picture' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Not logged in';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$table = ($userRole === 'Center') ? 'adoptioncenters' : 'individualusers';
$idField = ($userRole === 'Center') ? 'Center_ID' : 'User_ID';

if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['picture']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $response['message'] = 'Invalid file type. Please upload JPG, PNG, or GIF';
        echo json_encode($response);
        exit();
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../profile';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $new_filename = uniqid() . '.' . $ext;
    $upload_path = $upload_dir . '/' . $new_filename;
    $db_path = 'profile/' . $new_filename;
    
    try {
        if (move_uploaded_file($_FILES['picture']['tmp_name'], $upload_path)) {
            // Get current profile picture
            $stmt = $conn->prepare("SELECT ProfilePic FROM $table WHERE $idField = ?");
            $stmt->execute([$userId]);
            $old_pic = $stmt->fetchColumn();

            // Update database with new picture
            $stmt = $conn->prepare("UPDATE $table SET ProfilePic = ? WHERE $idField = ?");
            if ($stmt->execute([$db_path, $userId])) {
                // Delete old picture if it exists and isn't the default
                if ($old_pic && $old_pic != 'images/woman.png' && file_exists('../' . $old_pic)) {
                    unlink('../' . $old_pic);
                }

                $response = [
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'picture' => $db_path
                ];
            } else {
                unlink($upload_path); // Delete uploaded file if database update fails
                $response['message'] = 'Failed to update database';
            }
        } else {
            $response['message'] = 'Failed to upload file';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'No file uploaded or upload error occurred';
}

echo json_encode($response);
