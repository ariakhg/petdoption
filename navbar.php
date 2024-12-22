<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get user profile data
function getUserProfile($conn, $userId, $role) {
    try {
        if ($role === 'User') {
            $stmt = $conn->prepare("SELECT Name, ProfilePic FROM individualusers WHERE User_ID = ?");
        } else if ($role === 'Center') {
            $stmt = $conn->prepare("SELECT CenterName as Name, ProfilePic FROM adoptioncenters WHERE Center_ID = ?");
        } else {
            $stmt = $conn->prepare("SELECT Name, ProfilePic FROM admin WHERE Admin_ID = ?");
        }
        
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching profile: " . $e->getMessage());
        return null;
    }
}

// Get user profile if logged in
$userProfile = null;
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    require_once 'config/connection.php';
    $userProfile = getUserProfile($conn, $_SESSION['user_id'], $_SESSION['role']);
}
?>

<nav class="navbar">
    <div class="nav-container">
        <!-- Left side of navbar -->
        <div class="nav-links">
            <img src="assets/logo.png" alt="Petdoption Logo">
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'User'): ?>
                    <a href="findAPet.php">Find a Pet</a>
                    <a href="petListing.php">My Pet Listings</a>
                    <a href="petListingForm.php">List a Pet</a>
                    <a href="volunteeringForm.php">Volunteer</a>
                <?php elseif ($_SESSION['role'] === 'Center'): ?>
                    <a href="petListing.php">My Pet Listings</a>
                    <a href="petListingForm.php">List a Pet</a>
                <?php elseif ($_SESSION['role'] === 'Admin'): ?>
                    <a href="adminDashboard.php">Admin Dashboard</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Right side of navbar -->
        <div class="nav-links">
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'User'): ?>
                    <a class="nav-bar-icon" href="">
                        <img src="assets/saved-pets-icon.png" alt="Saved Pets">
                    </a>
                <?php endif; ?>
                <a href="userProfile.php">
                    <img class="nav-profile" src="<?php echo htmlspecialchars($userProfile['ProfilePic']); ?>" 
                    alt="Profile Picture" style="border-radius: 50%; width: 2.3rem; height: 2.3rem; object-fit: cover;">
                </a>
                <a href="handlers/logout.php">Log Out</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>