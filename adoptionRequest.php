<?php
session_start();
require 'config/connection.php';

try {
    // Fetch pending adoption requests
    $stmt = $conn->prepare("
        SELECT ar.*, p.Name as PetName, p.Photo as PetPhoto, u.Name as RequesterName, u.Email as RequesterEmail 
        FROM adoptionrequests ar
        JOIN pets p ON ar.Pet_ID = p.Pet_ID
        JOIN individualusers u ON ar.User_ID = u.User_ID
        WHERE ar.Status = 'Pending'
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle request actions (Accept/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $action = $_POST['action'];
        $request_id = $_POST['request_id'];
        
        try {
            // Start transaction
            $conn->beginTransaction();
            
            if ($action === 'accept') {
                // Update adoption request status
                $stmt = $conn->prepare("
                    UPDATE adoptionrequests 
                    SET Status = 'Approved'
                    WHERE Request_ID = ?
                ");
                $stmt->execute([$request_id]);

                // Update pet status to Adopted
                $stmt = $conn->prepare("
                    UPDATE pets p
                    JOIN adoptionrequests ar ON p.Pet_ID = ar.Pet_ID
                    SET p.AdoptionStatus = 'Adopted'
                    WHERE ar.Request_ID = ?
                ");
                $stmt->execute([$request_id]);

                // Reject other pending requests for the same pet
                $stmt = $conn->prepare("
                    UPDATE adoptionrequests ar1
                    JOIN adoptionrequests ar2 ON ar1.Pet_ID = ar2.Pet_ID
                    SET ar1.Status = 'Rejected'
                    WHERE ar2.Request_ID = ?
                    AND ar1.Request_ID != ?
                    AND ar1.Status = 'Pending'
                ");
                $stmt->execute([$request_id, $request_id]);

            } elseif ($action === 'reject') {
                // Update adoption request status to rejected
                $stmt = $conn->prepare("
                    UPDATE adoptionrequests 
                    SET Status = 'Rejected'
                    WHERE Request_ID = ?
                ");
                $stmt->execute([$request_id]);
            }

            $conn->commit();
            header('Location: adoptionRequest.php');
            exit();

        } catch(PDOException $e) {
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Requests</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .nav-bar-icon img {
        width: 1.5rem;
        height: 1.5rem;
    }
    a {
        text-decoration: none;
        color: #103559;
        }
    .back-link {
        color: #103559;
        font-size: 24px;
        display: inline-block;
        margin-bottom: 1rem;
        font-weight: 700;
        margin-left: 130px;
        margin-top: 30px;
        margin-bottom: 40px;
    }
    .card-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 1rem;
    }
    .card {
        background-color: #e7f3ff;
        border-radius: 50px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 1045px;
        height: 132px;
        padding: 20px;
        display: flex;
        flex-direction: row;
        /* position: relative; */
    }
    .pet-image {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        margin-top: auto;
        margin-bottom: auto;
        margin-left: 50px;
    }
    .pet-name{
        margin-top: 20px;
        margin-left: 20px;
        font-size: 40px;
        height: 54px;
    }
    .pet-details {
        margin-left: 20px;
        margin-right: 40px;
        height: 100px;
        display: grid;
        background-color: #E4F4FF;
        padding: 20px;
        border-radius: 10px;
        width: 40%; /* Adjust the width to fit the content */
    }

    .detail-item {
    text-align: left;
    }

    .detail-item h3 {
    margin: 0;
    font-size: 24px;
    color: #103559;
    font-weight: bold;
    }

    .detail-item p {
    margin: 5px 0 0;
    font-size: 14px;
    font-weight: 500;
    color: #103559;
    }

    .btn-primary {
        margin-top: 25px;
        margin-left: -80px;
        margin-right: 10px;
        background-color: var(--yellowbtn);
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        border: 1px solid #E7BD43;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        height: 45px;
        width: 153px;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: var(--activeyellow);
    }

    .btn-crud{
        margin-top: 24px;
        margin-left: 10px;
        border-radius: 50%;
        background-color: #E4F4FF;
        width: 48px; 
        height: 48px;
        border: none;
        cursor: pointer;
    }
    .crud{
        width: 48px; 
        height: 48px;
        border-radius: 50%;
    }

    footer {
        position: absolute;
        bottom: 0;
        width: 100%;
    }

</style>
<body>
    <?php include 'navbar.php'; ?>

    <a href='petListing.php' class="back-link">< Adoption Request</a>

    <?php foreach ($requests as $request): ?>
    <div class="card-container">
        <div class="card">
            <img src="<?php echo htmlspecialchars($request['PetPhoto']); ?>" 
                 alt="<?php echo htmlspecialchars($request['PetName']); ?>" 
                 class="pet-image">
            <h2 class="pet-name"><?php echo htmlspecialchars($request['PetName']); ?></h2>
            <div class="pet-details">
                <div class="detail-item">
                    <h3>Requested by</h3>
                    <p><?php echo htmlspecialchars($request['RequesterEmail']); ?></p>
                </div>
            </div>
            <button id="chatButton" class="btn-primary">Chat with User</button>
            
            <!-- View Pet Profile -->
            <a href="petProfile.php?id=<?php echo $request['Pet_ID']; ?>" class="btn-crud">
                <img src="ui/eye.png" alt="view" class="crud">
            </a>

            <!-- Reject Request -->
            <form method="POST" style="display: inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['Request_ID']; ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit" class="btn-crud" onclick="return confirm('Are you sure you want to reject this request?');">
                    <img src="ui/reject.png" alt="reject" class="crud">
                </button>
            </form>

            <!-- Accept Request -->
            <form method="POST" style="display: inline;">
                <input type="hidden" name="request_id" value="<?php echo $request['Request_ID']; ?>">
                <input type="hidden" name="action" value="accept">
                <button type="submit" class="btn-crud" onclick="return confirm('Are you sure you want to accept this request?');">
                    <img src="ui/accept.png" alt="accept" class="crud">
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Footer -->
    <footer>
        <div class="footer">
            <p>&copy; 2024 Petdoption. All rights reserved.</p>
            <img src="assets/logo.png" alt="Petdoption Logo" class="footer-logo">
            <div>
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>