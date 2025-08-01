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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pharmacy Dashboard</title>
</head>

<body
    style=" margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: flex-start; overflow-y: auto; ">

    <div
        style="background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); width: 100%; max-width: 900px;">
        <h2 style="text-align: center; color: #333; margin-bottom: 20px;">Available Prescriptions</h2>

        <p style="text-align: center; margin-top: -10px; margin-bottom: 20px;">
            <a href="profile.php" style="color: #0097a7; text-decoration: none; font-weight: 500;">Profile</a>
        </p>


        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">ID</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">User</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Upload Time</th>
                    <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $p): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $p['prescription_id']; ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <?php echo htmlspecialchars($p['name']); ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo $p['upload_time']; ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">
                        <a href="send_quotation.php?prescription_id=<?php echo $p['prescription_id']; ?>"
                            style=" background-color: #0097a7;color: white;padding: 8px 14px;text-decoration:none;border-radius: 6px;font-size: 14px;">
                            Prepare Quotation
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>