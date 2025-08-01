<?php
    session_start();
    include '../config/db.php';

    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>User Dashboard</title>
    </head>

    <body style="margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: flex-start; overflow-y: auto; ">
        <div style="background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); width: 100%; max-width: 900px; ">
            <h2 style="text-align: center; color: #333; margin-bottom: 20px;">Welcome, User</h2>

            <p style="text-align: right; margin-bottom: 20px;">
                <a href="upload_prescription.php" style="background-color: #0097a7; color: #fff; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; ">
                    Upload New Prescription
                </a>
            </p>

            <h3 style="color: #444;">
                Your Prescriptions
            </h3>

            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; ">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">ID</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Upload Time</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Status</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #ccc;">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php 
                        foreach ($prescriptions as $p): 
                    ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <?php echo $p['prescription_id']; ?>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <?php echo $p['upload_time']; ?>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <?php
                                $stmt = $pdo->prepare("SELECT status FROM quotations WHERE prescription_id = ?");
                                $stmt->execute([$p['prescription_id']]);
                                $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $quotation ? htmlspecialchars($quotation['status']) : 'No quotation yet';
                            ?>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <?php if ($quotation && $quotation['status'] === 'pending'): ?>
                                <a href="view_quotation.php?prescription_id=<?php echo $p['prescription_id']; ?>" style="color: #0097a7; text-decoration: none; font-weight: 500; ">View Quotation</a>
                            <?php 
                                else: 
                            ?>
                                —
                            <?php 
                                endif; 
                            ?>
                        </td>
                    </tr>
                    <?php 
                        endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>