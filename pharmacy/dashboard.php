<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacy') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT p.*, u.name FROM prescriptions p JOIN users u ON p.user_id = u.user_id WHERE p.prescription_id NOT IN (SELECT prescription_id FROM quotations)");
$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Available Prescriptions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Upload Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $p): ?>
                    <tr>
                        <td><?php echo $p['prescription_id']; ?></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo $p['upload_time']; ?></td>
                        <td><a href="send_quotation.php?prescription_id=<?php echo $p['prescription_id']; ?>">Prepare Quotation</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>