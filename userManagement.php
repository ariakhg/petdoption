<?php
require 'config/connection.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM individualusers WHERE User_ID = ? AND Role = 'User'");
        $stmt->execute([$_GET['id']]);
        header('Location: userManagement.php');
        exit();
    } catch(PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
}

try {
    // Fetch all active users
    $stmt = $conn->prepare("
        SELECT User_ID, Name, Location, PhoneNo, Email, Role, ProfilePic 
        FROM individualusers 
        WHERE Role = 'User' 
        ORDER BY Name
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="userManagement.css">
</head>
<body>
    <div class="container">
        <h1>User Management</h1>
        <table>
            <tr>
                <th>Profile Picture</th>
                <th>Full Name</th>
                <th>Location</th>
                <th>Phone_Num</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($user['ProfilePic'] ?: 'assets/profile-icon.png'); ?>" 
                             id="profileIcon" 
                             alt="Profile Picture">
                    </td>
                    <td><?php echo htmlspecialchars($user['Name']); ?></td>
                    <td><?php echo htmlspecialchars($user['Location']); ?></td>
                    <td><?php echo htmlspecialchars($user['PhoneNo']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role']); ?></td>
                    <td>
                        <a href="?action=delete&id=<?php echo $user['User_ID']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this user?');">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No users found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html> 