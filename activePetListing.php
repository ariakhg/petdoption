<?php
require 'config/connection.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM pets WHERE Pet_ID = ?");
        $stmt->execute([$_GET['id']]);
        header('Location: activePetListing.php');
        exit();
    } catch(PDOException $e) {
        echo "Error deleting pet: " . $e->getMessage();
    }
}

try {
    // Fetch all active pet listings
    $stmt = $conn->prepare("
        SELECT p.*, 
               COALESCE(u.PhoneNo, ac.PhoneNo) as ListerPhone,
               COALESCE(u.Email, ac.Email) as ListerEmail,
               COALESCE(u.Role, 'Center') as ListerRole
        FROM pets p
        LEFT JOIN individualusers u ON p.User_ID = u.User_ID
        LEFT JOIN adoptioncenters ac ON p.Center_ID = ac.Center_ID
        WHERE p.AdoptionStatus = 'Available'
        ORDER BY p.DateListed DESC
    ");
    $stmt->execute();
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $pets = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Pet Listings</title>
    <link rel="stylesheet" href="userManagement.css">
</head>
<body>
    <div class="container">
        <h1>Active Pet Listings</h1>
        <table>
            <tr>
                <th>Pet ID</th>
                <th>Pet Photo</th>
                <th>Pet Name</th>
                <th>Lister Phone</th>
                <th>Lister Email</th>
                <th>Role</th>
                <th>Date Listed</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($pets)): ?>
                <?php foreach ($pets as $pet): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pet['Pet_ID']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($pet['Photo']); ?>" 
                             style="width: 50px; height: 50px; object-fit: cover;" 
                             alt="Pet Photo">
                    </td>
                    <td><?php echo htmlspecialchars($pet['Name']); ?></td>
                    <td><?php echo htmlspecialchars($pet['ListerPhone']); ?></td>
                    <td><?php echo htmlspecialchars($pet['ListerEmail']); ?></td>
                    <td><?php echo htmlspecialchars($pet['ListerRole']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($pet['DateListed'])); ?></td>
                    <td>
                        <a href="petProfile.php?id=<?php echo $pet['Pet_ID']; ?>">
                            View
                        </a>
                        <a href="?action=delete&id=<?php echo $pet['Pet_ID']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this listing?');">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No active pet listings found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>