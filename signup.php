<?php
require_once 'config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "❌ Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $message = "❌ Username or email already taken.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $defaultPlan = "free";

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, plan) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$username, $email, $hashedPassword, $defaultPlan]);

            if ($success) {
                $message = "✅ Signup successful! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "❌ Something went wrong during signup.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            color: #333;
        }

        form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin: 15px 0;
            color: #cc0000;
            text-align: center;
        }

        .login-button {
            margin-top: 10px;
            background: #28a745;
        }

        .login-button:hover {
            background: #1e7e34;
        }

        a {
            color: #28a745;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2>Sign Up</h2>

    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Sign Up</button>
    </form>

    <form action="login.php" method="get" style="margin-top: 10px; width: 300px;">
        <button class="login-button" type="submit">🔑 Already have an account? Login</button>
    </form>
</body>
</html>
