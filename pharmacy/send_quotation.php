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
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prepare Quotation</title>
    <script>
    function addDrugRow() {
        const table = document.getElementById('drugTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow();
        row.innerHTML = `
        <td><input type="text" name="drug_name[]" required style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #ccc;"></td>
        <td><input type="number" name="quantity[]" required style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #ccc;"></td>
        <td><input type="number" step="0.01" name="price_per_unit[]" required style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #ccc;"></td>
      `;
    }
    </script>
</head>

<body
    style=" margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: flex-start; ">

    <div
        style=" background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); width: 100%; max-width: 800px;">
        <h2 style="text-align: center; color: #333; margin-bottom: 20px;">
            Prepare Quotation for Prescription #<?php echo $prescription_id; ?>
        </h2>

        <?php 
            if (isset($error)) { 
                echo "<p style='background: #ffe0e0; color: #b20000; padding: 10px; border-radius: 6px; text-align: center;'>$error</p>"; 
            } 
        ?>
        <?php 
            if (isset($message)) { 
                echo "<p style='background: #e0ffe0; color: #007700; padding: 10px; border-radius: 6px; text-align: center;'>$message</p>"; 
            } 
        ?>

        <form method="POST">
            <table id="drugTable" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Drug Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Quantity</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Price per Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="drug_name[]" required
                                style="padding: 8px; width: 92%; border-radius: 6px; border: 1px solid #ccc;">
                        </td>
                        <td>
                            <input type="number" name="quantity[]" required
                                style="padding: 8px; width: 92%; border-radius: 6px; border: 1px solid #ccc;">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price_per_unit[]" required
                                style="padding: 8px; width: 92%; border-radius: 6px; border: 1px solid #ccc;">
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: center;">
                <button type="button" onclick="addDrugRow()"
                    style="background-color: #00796b;color: white;padding: 10px 18px;border: none;border-radius: 6px;font-size: 14px;cursor: pointer;margin-right: 10px; ">
                    Add Drug
                </button>

                <button type="submit"
                    style="background-color: #0097a7;color: white;padding: 10px 18px;border: none;border-radius: 6px;font-size: 14px;cursor: pointer; ">
                    Send Quotation
                </button>
            </div>
        </form>
    </div>
</body>

</html>