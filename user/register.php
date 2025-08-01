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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Register</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="container">
            <h2>User Registration</h2>
            <?php 
                if (isset($error)) { 
                    echo "<p class='error'>$error</p>"; 
                } 
            ?>
            
            <form method="POST">
                <!-- Name -->
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <!-- Email -->
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <!-- Address -->
                <label for="address">Address:</label>
                <textarea id="address" name="address" required></textarea>
                
                <!-- Contact -->
                <label for="contact_no">Contact No:</label>
                <input type="text" id="contact_no" name="contact_no" required>
                
                <!-- DOB -->
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required>
                
                <!-- Password -->
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Register</button>
            </form>
        </div>
    </body>
</html>