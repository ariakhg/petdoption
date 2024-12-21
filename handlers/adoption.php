<?php
session_start();
require_once '../config/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$pet_id = $data['pet_id'];
$user_id = $_SESSION['user_id']; // Use session user_id for security
$action = $data['action']; // 'reserve' or 'unreserve'

try {
    // Start transaction
    $conn->beginTransaction();

    if ($action === 'reserve') {
        // Check if pet is available
        $stmt = $conn->prepare("SELECT AdoptionStatus FROM pets WHERE Pet_ID = ?");
        $stmt->execute([$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pet['AdoptionStatus'] !== 'Available') {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Pet is no longer available']);
            exit;
        }

        // Update pet status to Reserved
        $stmt = $conn->prepare("UPDATE pets SET AdoptionStatus = 'Reserved' WHERE Pet_ID = ?");
        $stmt->execute([$pet_id]);

        // Create adoption request
        $stmt = $conn->prepare("INSERT INTO adoptionrequest 
                              (Pet_ID, Finder_ID, Date

    } else if ($action === 'unreserve') {
        // Update pet status to Available
        $stmt = $conn->prepare("UPDATE pets SET AdoptionStatus = 'Available' WHERE Pet_ID = ?");
        $stmt->execute([$pet_id]);

        // Commit transaction
        $conn->commit();
        echo json_encode(['success' => true]);
    } else {
        // Invalid action
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    // Roll back transaction on error
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
