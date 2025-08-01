<?php
    session_start();
    include '../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM pharmacies WHERE email = ?");
        $stmt->execute([$email]);
        $pharmacy = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($pharmacy && password_verify($password, $pharmacy['password'])) {
            $_SESSION['user_id'] = $pharmacy['pharmacy_id'];
            $_SESSION['user_type'] = 'pharmacy';
            header("Location: dashboard.php");
            exit;
        } 
        else {
            $error = "Invalid credentials.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Pharmacy Login</title>
    </head>
    <body style="margin: 0; padding: 20px; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); min-height: 100vh; display: flex; justify-content: center; align-items: center; overflow-y: auto; ">

    <div style="background-color: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); width: 100%; max-width: 400px; ">
        <form method="POST" style="display: flex; flex-direction: column;">
            <h2 style="margin-bottom: 20px; color: #333; text-align: center;">
                Pharmacy Login
            </h2>

            <?php 
                if (isset($error)) { 
                    echo "<p style='background: #ffe0e0; color: #b20000; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center;'>$error</p>"; 
                }
            ?>

            <label for="email" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Email
            </label>
            <input 
                type="email" 
                id="email" 
                name="email"  
                placeholder="Enter your email"
                style="padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 8px;"
                required
            >

            <label for="password" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                Password
            </label>
            <input 
                type="password" 
                id="password" 
                name="password"  
                placeholder="Enter your password"
                style="padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 8px;"
                required
            >

            <button type="submit" style="background-color: #0097a7; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                Login
            </button>

            <p style="margin-top: 15px; text-align: center; color: #555;">
                Don't have an account? <a href="register.php" style="color: #0097a7; text-decoration: none;">Register here</a>.
            </p>
        </form>
    </div>

    </body>
</html>