<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'pharmacy') {
    header("Location: login.php");
    exit;
}

$prescription_id = $_GET['prescription_id'];
$stmt = $pdo->prepare("SELECT * FROM quotations WHERE prescription_id = ?");
$stmt->execute([$prescription_id]);
if ($stmt->rowCount() > 0) {
    $error = "Quotation already exists.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pharmacy_id = $_SESSION['user_id'];
    $drugs = $_POST['drug_name'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price_per_unit'];
    $total_amount = 0;
    foreach ($quantities as $i => $qty) {
        $total_amount += $qty * $prices[$i];
    }
    $stmt = $pdo->prepare("INSERT INTO quotations (prescription_id, pharmacy_id, total_amount) VALUES (?, ?, ?)");
    $stmt->execute([$prescription_id, $pharmacy_id, $total_amount]);
    $quotation_id = $pdo->lastInsertId();
    foreach ($drugs as $i => $drug) {
        $stmt = $pdo->prepare("INSERT INTO quotation_details (quotation_id, drug_name, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
        $stmt->execute([$quotation_id, $drug, $quantities[$i], $prices[$i]]);
    }
    $user_email = $pdo->query("SELECT email FROM users WHERE user_id = (SELECT user_id FROM prescriptions WHERE prescription_id = $prescription_id)")->fetchColumn();
    mail($user_email, "New Quotation", "A quotation has been prepared for your prescription #$prescription_id.");
    $message = "Quotation sent successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prepare Quotation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function addDrugRow() {
            const table = document.getElementById('drugTable').getElementsByTagName('tbody')[0];
            const row = table.insertRow();
            row.innerHTML = '<td><input type="text" name="drug_name[]" required></td><td><input type="number" name="quantity[]" required></td><td><input type="number" step="0.01" name="price_per_unit[]" required></td>';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Prepare Quotation for Prescription #<?php echo $prescription_id; ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <form method="POST">
            <table id="drugTable">
                <thead>
                    <tr>
                        <th>Drug Name</th>
                        <th>Quantity</th>
                        <th>Price per Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="drug_name[]" required></td>
                        <td><input type="number" name="quantity[]" required></td>
                        <td><input type="number" step="0.01" name="price_per_unit[]" required></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addDrugRow()">Add Drug</button>
            <button type="submit">Send Quotation</button>
        </form>
    </div>
</body>
</html>