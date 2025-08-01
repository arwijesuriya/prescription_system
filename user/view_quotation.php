<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

$prescription_id = $_GET['prescription_id'];
$stmt = $pdo->prepare("SELECT q.*, p.name AS pharmacy_name FROM quotations q JOIN pharmacies p ON q.pharmacy_id = p.pharmacy_id WHERE q.prescription_id = ? AND q.prescription_id IN (SELECT prescription_id FROM prescriptions WHERE user_id = ?)");
$stmt->execute([$prescription_id, $_SESSION['user_id']]);
$quotation = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quotation) exit("No quotation found.");

$stmt = $pdo->prepare("SELECT * FROM quotation_details WHERE quotation_id = ?");
$stmt->execute([$quotation['quotation_id']]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE quotations SET status = ? WHERE quotation_id = ?");
    $stmt->execute([$status, $quotation['quotation_id']]);
    $pharmacy_email = $pdo->query("SELECT email FROM pharmacies WHERE pharmacy_id = {$quotation['pharmacy_id']}")->fetchColumn();
    mail($pharmacy_email, "Quotation $status", "The quotation for prescription #$prescription_id has been $status.");
    $message = "Quotation $status successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quotation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Quotation for Prescription #<?php echo $prescription_id; ?></h2>
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <p>Pharmacy: <?php echo htmlspecialchars($quotation['pharmacy_name']); ?></p>
        <p>Total Amount: $<?php echo $quotation['total_amount']; ?></p>
        <table>
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>Quantity</th>
                    <th>Price per Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $d): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['drug_name']); ?></td>
                        <td><?php echo $d['quantity']; ?></td>
                        <td>$<?php echo $d['price_per_unit']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($quotation['status'] === 'pending'): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="status" value="accepted">
                <button type="submit">Accept</button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="status" value="rejected">
                <button type="submit">Reject</button>
            </form>
        <?php else: ?>
            <p>Status: <?php echo ucfirst($quotation['status']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>