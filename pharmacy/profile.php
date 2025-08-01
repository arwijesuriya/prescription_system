<?php
    session_start();
    include '../config/db.php';

    // Ensure pharmacy is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacy') {
        header("Location: ../login.php");
        exit;
    }

    // Handle logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    $pharmacy_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT name, email FROM pharmacies WHERE pharmacy_id = ?");
    $stmt->execute([$pharmacy_id]);
    $pharmacy = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pharmacy Profile</title>
</head>

<body style="font-family: 'Segoe UI', sans-serif; background: #f9f9f9; padding: 40px;">
    <div
        style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #333; margin-bottom: 20px;">Pharmacy Profile</h2>

        <p><strong>Name:</strong> <?php echo htmlspecialchars($pharmacy['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($pharmacy['email']); ?></p>

        <a href="profile.php?logout=true"
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