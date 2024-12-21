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
    $stmt = $conn->prepare("SELECT SavedPets FROM individualusers WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $savedPets = $result['SavedPets'] ? explode(',', $result['SavedPets']) : [];
    $isSaved = in_array($pet_id, $savedPets);
    
    echo json_encode(['success' => true, 'saved' => $isSaved]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 