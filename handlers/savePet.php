<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$pet_id = $data['pet_id'];
$user_id = $_SESSION['user_id'];

try {
    // Get current saved pets
    $stmt = $conn->prepare("SELECT SavedPets FROM individualusers WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $savedPets = $result['SavedPets'] ? explode(',', $result['SavedPets']) : [];
    
    if (in_array($pet_id, $savedPets)) {
        // Pet is already saved, so unsave it
        $savedPets = array_diff($savedPets, [$pet_id]);
        $action = 'unsaved';
    } else {
        // Save the pet
        $savedPets[] = $pet_id;
        $action = 'saved';
    }
    
    // Update the saved pets in the database
    $savedPetsString = implode(',', array_filter($savedPets));
    $stmt = $conn->prepare("UPDATE individualusers SET SavedPets = ? WHERE User_ID = ?");
    $stmt->execute([$savedPetsString, $user_id]);
    
    echo json_encode(['success' => true, 'action' => $action]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 