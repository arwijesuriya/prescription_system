<?php
    include '../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_STRING);
        $dob = $_POST['dob'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, address, contact_no, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $address, $contact_no, $dob, $password]);
            header("Location: login.php?success=1");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>User Registration | Prescription System</title>
    </head>

    <body style="margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: center; overflow-y: auto; ">

    <div style="background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); width: 100%; max-width: 500px; ">
        <form method="POST" style="display: flex; flex-direction: column;">
            <h2 style="margin-bottom: 20px; color: #333; text-align: center;">
                User Registration
            </h2>

            <?php 
                if (isset($error)) {
                    echo "<p style='background: #ffe0e0; color: #b20000; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center;'>$error</p>";
                }
            ?>

            <label for="name" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Full Name
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                placeholder="Enter your name"
                style="padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; width: 100%;"
                required 
            >

            <label for="email" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Email
            </label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="Enter your email"
                style="padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; width: 100%;"
                required
            >

            <label for="address" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Address
            </label>
            <textarea 
                id="address" 
                name="address"  
                placeholder="Enter your address"
                style="padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; width: 100%; resize: vertical;"
                required
            >
            </textarea>

            <label for="contact_no" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Contact No
            </label>
            <input 
                type="text" 
                id="contact_no" 
                name="contact_no" 
                placeholder="07XXXXXXXX"
                style="padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; width: 100%;"
                required 
            >

            <label for="dob" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Date of Birth
            </label>
            <input 
                type="date" 
                id="dob" 
                name="dob" 
                style="padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px; width: 100%;"
                required
            >

            <label for="password" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Password
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter a password"
                style="padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 8px; width: 100%;"
                required 
            >

            <button type="submit" style="background-color: #0097a7; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                Register
            </button>
        </form>
    </div>
    </body>
</html>