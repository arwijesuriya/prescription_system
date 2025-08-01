<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pharmacies WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $error = "Email already exists.";
    } else {
        // Insert new pharmacy
        $stmt = $pdo->prepare("INSERT INTO pharmacies (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $message = "Registration successful! Please log in.";
        // Redirect to login page after a short delay
        header("Refresh: 2; url=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Registration</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Pharmacy Registration</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($message)) echo "<p class='success'>$message</p>"; ?>
        <form method="POST">
            <label for="name">Pharmacy Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </div>
</body>
</html>