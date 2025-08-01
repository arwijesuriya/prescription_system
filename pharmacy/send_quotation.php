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
    header("Location: dashboard.php");
    exit;
}

$images = $pdo->prepare("SELECT image_path FROM prescription_images WHERE prescription_id = ?");
$images->execute([$prescription_id]);
$image_paths = $images->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prepare Quotation</title>
    <script>
    let total = 0;

    function addDrugRow() {
        const name = document.getElementById("drug").value;
        const qty = parseFloat(document.getElementById("qty").value);
        const price = parseFloat(document.getElementById("price").value);

        if (!name || isNaN(qty) || isNaN(price)) return alert("Please fill all fields");

        const table = document.getElementById("drugTableBody");
        const row = table.insertRow();
        row.innerHTML = `
                <td><input type="hidden" name="drug_name[]" value="${name}">${name}</td>
                <td><input type="hidden" name="quantity[]" value="${qty}">${price} x ${qty}</td>
                <td><input type="hidden" name="price_per_unit[]" value="${price}">${(price * qty).toFixed(2)}</td>
            `;

        total += price * qty;
        document.getElementById("totalCell").textContent = total.toFixed(2);

        document.getElementById("drug").value = "";
        document.getElementById("qty").value = "";
        document.getElementById("price").value = "";
    }
    </script>
</head>

<body
    style="margin:0;padding:20px;font-family:Segoe UI,sans-serif;background:linear-gradient(to right,#e0f7fa,#e8f5e9);display:flex;justify-content:center;align-items:flex-start;min-height:100vh;">
    <div
        style="background:#fff;padding:30px;border-radius:12px;box-shadow:0 8px 16px rgba(0,0,0,0.1);width:100%;max-width:1000px;">
        <h2 style="text-align:center;">Prepare Quotation for Prescription #<?php echo $prescription_id; ?></h2>

        <?php if (isset($error)) echo "<p style='background:#ffe0e0;color:#b20000;padding:10px;border-radius:6px;text-align:center;'>$error</p>"; ?>

        <form method="POST">
            <div style="display:flex;gap:20px;margin-bottom:20px;">
                <div style="flex:1;">
                    <?php if (!empty($image_paths)): ?>
                    <img src="../uploads/<?php echo $image_paths[0]; ?>"
                        style="width:100%;height:300px;object-fit:contain;border:1px solid #ccc;">
                    <div style="display:flex;gap:10px;margin-top:10px;">
                        <?php foreach ($image_paths as $img): ?>
                        <img src="../uploads/<?php echo $img; ?>"
                            style="width:60px;height:60px;object-fit:cover;border:2px solid #ccc;border-radius:4px;">
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div
                        style="width:100%;height:300px;background:#f8f8f8;display:flex;justify-content:center;align-items:center;border:1px solid #ccc;">
                        No Prescription Image</div>
                    <?php endif; ?>
                </div>

                <div style="flex:1;">
                    <table style="width:100%;border-collapse:collapse;margin-top:10px;">
                        <thead>
                            <tr style="background:#f0f0f0;">
                                <th style="padding:10px;text-align:left;border-bottom:1px solid #ccc;">Drug</th>
                                <th style="padding:10px;text-align:left;border-bottom:1px solid #ccc;">Quantity</th>
                                <th style="padding:10px;text-align:left;border-bottom:1px solid #ccc;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="drugTableBody"></tbody>
                        <tfoot>
                            <tr style="font-weight:bold;background:#f9f9f9;">
                                <td colspan="2" style="text-align:right;padding:10px;">Total</td>
                                <td style="padding:10px;" id="totalCell">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <input type="text" id="drug" placeholder="Drug Name"
                    style="flex:1;padding:10px;border:1px solid #ccc;border-radius:6px;">
                <input type="number" id="qty" placeholder="Quantity"
                    style="flex:1;padding:10px;border:1px solid #ccc;border-radius:6px;">
                <input type="number" id="price" step="0.01" placeholder="Price per Unit"
                    style="flex:1;padding:10px;border:1px solid #ccc;border-radius:6px;">
                <button type="button" onclick="addDrugRow()"
                    style="padding:10px 16px;background:#00796b;color:white;border:none;border-radius:6px;cursor:pointer;">Add</button>
            </div>

            <button type="submit"
                style="margin-top:20px;padding:10px 16px;background:#0097a7;color:white;border:none;border-radius:6px;cursor:pointer;width:100%;">Send
                Quotation</button>
        </form>
    </div>
</body>

</html>