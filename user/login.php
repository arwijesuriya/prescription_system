<?php
    session_start();
    include '../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = 'user';
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Login | Prescription System</title>
    </head>

    <body style="margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; background: linear-gradient(to right, #e0f7fa, #e8f5e9); height: 100vh; display: flex; justify-content: center; align-items: center;">

        <div style="background-color: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); width: 100%; max-width: 400px;">
            <form method="POST" style="display: flex; flex-direction: column;">
                <h2 style="margin-bottom: 20px; color: #333; text-align: center;">
                    User Login
                </h2>

                <?php 
                    if (isset($error)) { 
                        echo "<p style='background: #ffe0e0; color: #b20000; padding: 10px; margin-bottom: 15px; border-radius: 6px; text-align: center;'>$error</p>"; 
                    } 
                ?>

                <label for="email" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                    Email Address
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    required placeholder="Enter your email"
                    style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 8px; outline: none;"
                >

                <label for="password" style="margin-bottom: 5px; font-weight: 500; color: #555;">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required placeholder="Enter your password"
                    style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 8px; outline: none;"
                >

                <button type="submit" style="width: 100%; background-color: #0097a7; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                    Login
                </button>
            </form>
        </div>
    </body>
</html>