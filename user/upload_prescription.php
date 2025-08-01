<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = filter_input(INPUT_POST, 'note', FILTER_SANITIZE_STRING);
    $delivery_address = filter_input(INPUT_POST, 'delivery_address', FILTER_SANITIZE_STRING);
    $delivery_time_slot = $_POST['delivery_time_slot'];
    $user_id = $_SESSION['user_id'];

    // Insert prescription
    $stmt = $pdo->prepare("INSERT INTO prescriptions (user_id, note, delivery_address, delivery_time_slot) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $note, $delivery_address, $delivery_time_slot]);
    $prescription_id = $pdo->lastInsertId();

    if (isset($_FILES['images']) && count($_FILES['images']['name']) <= 5) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                move_uploaded_file($tmp_name, '../uploads/' . $filename);

                // Insert image record
                $stmt = $pdo->prepare("INSERT INTO prescription_images (prescription_id, image_path) VALUES (?, ?)");
                $stmt->execute([$prescription_id, $filename]);
            }
        }

        // Redirect to dashboard after successful upload
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        $error = "Maximum 5 images allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Prescription</title>
</head>

<body
    style="margin: 0;padding: 20px;box-sizing: border-box;font-family: 'Segoe UI', sans-serif;background: linear-gradient(to right, #e0f7fa, #e8f5e9);min-height: 100vh;display: flex;justify-content: center;align-items: flex-start;overflow-y: auto; ">

    <div style="background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.1);
                    width: 100%; max-width: 600px; ">
        <h2 style=" text-align: center; color: #333; margin-bottom: 20px;">Upload Prescription</h2>

        <?php
            if (isset($message)) {
                echo "<p style='background: #e0ffe0; color: #007700; padding: 10px; margin-bottom: 15px; border-radius: 6px;'>$message</p>";
            }
        ?>
        <?php
            if (isset($error)) {
                echo "<p style='background: #ffe0e0; color: #b20000; padding: 10px; margin-bottom: 15px; border-radius: 6px;'>$error</p>";
            }
        ?>

        <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
            <label for="images" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Prescription Images (max 5)
            </label>
            <input type="file" id="images" name="images[]" multiple accept="image/*" required
                style="margin-bottom: 15px; padding: 8px; border: 1px solid #ccc; border-radius: 8px;">

            <label for="note" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Note
            </label>
            <textarea id="note" name="note" placeholder="Enter any special instructions"
                style="margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 8px; resize: vertical;"></textarea>

            <label for="delivery_address" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Delivery Address
            </label>
            <input type="text" id="delivery_address" name="delivery_address" required
                placeholder="Enter delivery address"
                style="margin-bottom: 15px; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">

            <label for="delivery_time_slot" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Delivery Time Slot
            </label>
            <select id="delivery_time_slot" name="delivery_time_slot" required
                style="margin-bottom: 20px; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                <option value="8:00-10:00">8:00 - 10:00</option>
                <option value="10:00-12:00">10:00 - 12:00</option>
                <option value="12:00-14:00">12:00 - 14:00</option>
            </select>

            <button type="submit"
                style="background-color: #0097a7; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                Upload
            </button>
        </form>
    </div>

</body>

</html>