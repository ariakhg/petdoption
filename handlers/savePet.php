<?php
session_start();
require_once '../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in first']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $pet_id = $data['pet_id'] ?? null;
    $action = $data['action'] ?? null;

    if (!$pet_id || !$action) {
        throw new Exception('Invalid request');
    }

    // Get current saved pets
    $stmt = $conn->prepare("SELECT SavedPets FROM individualusers WHERE User_ID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $savedPets = !empty($user['SavedPets']) ? explode(',', $user['SavedPets']) : [];
    
    if ($action === 'save') {
        if (!in_array($pet_id, $savedPets)) {
            $savedPets[] = $pet_id;
        }
    } else if ($action === 'unsave') {
        $savedPets = array_diff($savedPets, [$pet_id]);
    }
    
    // Update saved pets
    $newSavedPets = implode(',', array_filter($savedPets));
    $updateStmt = $conn->prepare("UPDATE individualusers SET SavedPets = ? WHERE User_ID = ?");
    $updateStmt->execute([$newSavedPets, $_SESSION['user_id']]);

    echo json_encode([
        'success' => true,
        'message' => $action === 'save' ? 'Pet saved successfully' : 'Pet removed from saved',
        'isSaved' => $action === 'save'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>