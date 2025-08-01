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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Quotation</title>
</head>

<body
    style="margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: flex-start; overflow-y: auto; ">

    <div
        style="background-color: #fff;padding: 30px;border-radius: 12px;box-shadow: 0 8px 16px rgba(0,0,0,0.1);width: 100%;max-width: 700px;">
        <h2 style="text-align: center; color: #333; margin-bottom: 20px;">
            Quotation for Prescription #<?php echo $prescription_id; ?>
        </h2>

        <?php if (isset($message)) { echo "<p style='background: #e0ffe0; color: #007700; padding: 10px; border-radius: 6px;'>$message</p>"; } ?>

        <p style="margin-bottom: 10px;"><strong>Pharmacy:</strong>
            <?php echo htmlspecialchars($quotation['pharmacy_name']); ?></p>
        <p style="margin-bottom: 20px;"><strong>Total Amount:</strong> $<?php echo $quotation['total_amount']; ?></p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Drug Name</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Quantity</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Price per Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $d): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <?php echo htmlspecialchars($d['drug_name']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $d['quantity']; ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">$<?php echo $d['price_per_unit']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($quotation['status'] === 'pending'): ?>
        <div style="text-align: center;">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="status" value="accepted">
                <button type="submit" style="background-color: #4caf50;color: white;padding: 10px 20px;border: none;border-radius: 8px;font-size: 14px; cursor: pointer;margin-right: 10px;
        ">Accept</button>
            </form>

            <form method="POST" style="display: inline;">
                <input type="hidden" name="status" value="rejected">
                <button type="submit"
                    style=" background-color: #f44336; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer;">
                    Reject
                </button>
            </form>
        </div>
        <?php else: ?>
        <p style="margin-top: 20px; font-weight: bold;">
            Status:
            <?php 
                echo ucfirst($quotation['status']); 
            ?>
        </p>
        <?php endif; ?>
    </div>

</body>

</html>