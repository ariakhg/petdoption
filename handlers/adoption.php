<?php
session_start();
require_once '../config/connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log incoming request
error_log("Received request: " . file_get_contents('php://input'));

header('Content-Type: application/json');

// Check if request is POST and has JSON content
$jsonData = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$jsonData) {
    error_log("Invalid request method or JSON data");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$pet_id = $jsonData['pet_id'];
$user_id = $_SESSION['user_id'];
$action = $jsonData['action'];

error_log("Processing request for pet_id: $pet_id, user_id: $user_id, action: $action");

try {
    if ($action === 'reserve') {
        // Begin transaction
        $conn->beginTransaction();
        error_log("Started transaction for reserve action");

        // Check if pet is still available
        $checkStmt = $conn->prepare("SELECT AdoptionStatus FROM pets WHERE Pet_ID = ?");
        $checkStmt->execute([$pet_id]);
        $status = $checkStmt->fetchColumn();
        error_log("Current pet status: $status");

        if ($status !== 'Available') {
            $conn->rollBack();
            error_log("Pet not available");
            echo json_encode(['success' => false, 'message' => 'Pet is no longer available']);
            exit;
        }

        // Update pet status
        $updateStmt = $conn->prepare("UPDATE pets SET AdoptionStatus = 'Reserved' WHERE Pet_ID = ?");
        $updateStmt->execute([$pet_id]);
        error_log("Updated pet status to Reserved");

        // Create adoption request
        $requestStmt = $conn->prepare("INSERT INTO adoptionrequests (Pet_ID, User_ID, RequestDate, Status) VALUES (?, ?, NOW(), 'Pending')");
        $requestStmt->execute([$pet_id, $user_id]);
        error_log("Created adoption request");

        // Update user's ReservedPets
        $userStmt = $conn->prepare("SELECT ReservedPets FROM individualusers WHERE User_ID = ?");
        $userStmt->execute([$user_id]);
        $currentReserved = $userStmt->fetchColumn();
        
        $newReserved = $currentReserved ? $currentReserved . ',' . $pet_id : $pet_id;
        $updateUserStmt = $conn->prepare("UPDATE individualusers SET ReservedPets = ? WHERE User_ID = ?");
        $updateUserStmt->execute([$newReserved, $user_id]);
        error_log("Updated user's ReservedPets");

        // Commit transaction
        $conn->commit();
        error_log("Transaction committed successfully");

        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully'
        ]);

    } elseif ($action === 'unreserve') {
        $conn->beginTransaction();

        // Remove from ReservedPets
        $userStmt = $conn->prepare("SELECT ReservedPets FROM individualusers WHERE User_ID = ?");
        $userStmt->execute([$user_id]);
        $currentReserved = $userStmt->fetchColumn();

        if ($currentReserved) {
            $reservedArray = explode(',', $currentReserved);
            $reservedArray = array_filter($reservedArray, function($value) use ($pet_id) {
                return $value != $pet_id;
            });
            $newReserved = implode(',', $reservedArray);

            $updateUserStmt = $conn->prepare("UPDATE individualusers SET ReservedPets = ? WHERE User_ID = ?");
            $updateUserStmt->execute([$newReserved, $user_id]);
        }

        // Delete adoption request
        $deleteStmt = $conn->prepare("DELETE FROM adoptionrequests WHERE Pet_ID = ? AND User_ID = ?");
        $deleteStmt->execute([$pet_id, $user_id]);

        // Update pet status
        $updateStmt = $conn->prepare("UPDATE pets SET AdoptionStatus = 'Available' WHERE Pet_ID = ?");
        $updateStmt->execute([$pet_id]);

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Reservation cancelled successfully'
        ]);
    }

} catch (PDOException $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Database error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
