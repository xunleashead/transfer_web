<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $message = "❌ Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["plan"] = $user["plan"];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "❌ Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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

        .signup-button {
            margin-top: 10px;
            background: #28a745;
        }

        .signup-button:hover {
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
    <h2>Login</h2>

    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <form action="signup.php" method="get" style="margin-top: 10px; width: 300px;">
        <button class="signup-button" type="submit">📝 Don’t have an account? Sign Up</button>
    </form>
</body>
</html>
