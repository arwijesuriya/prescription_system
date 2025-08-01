<?php
    session_start();
    include '../config/db.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
        header("Location: login.php");
        exit;
    }

    // Handle logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Get user details
    $stmt = $pdo->prepare("SELECT name, email, address, contact_no, dob FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>

<body style="font-family: 'Segoe UI', sans-serif; background: #f4f4f4; padding: 40px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px; color: #333;">User Profile</h2>

        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
        <p><strong>Contact No:</strong> <?php echo htmlspecialchars($user['contact_no']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>

        <a href="?logout=true"
            style="display: inline-block; margin-top: 30px; background: #d32f2f; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
            Logout
        </a>
        <a href="dashboard.php"
            style="margin-left: 10px; background: #0097a7; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
            Back to Dashboard
        </a>
    </div>
</body>

</html>