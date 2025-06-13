<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$plan = $_SESSION["plan"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 0;
        }

        header {
            background: #3f51b5;
            color: white;
            padding: 20px;
            text-align: center;
        }

        main {
            max-width: 1200px;
            margin: auto;
            padding: 30px 15px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .card h2 {
            margin-top: 0;
            color: #333;
        }

        ul.details {
            list-style: none;
            padding: 0;
        }

        ul.details li {
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .upload-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .upload-btn:hover {
            background: #218838;
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.5rem;
            }

            .card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>👋 Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    </header>

    <main>
        <div class="dashboard-grid">
            <section class="card">
                <h2>📖 About Me</h2>
                <img src="pic2.png" alt="Jeff">
                <p>I'm Jeff, a cybersecurity expert and developer. This platform helps you share and store files safely. Even if your phone or computer gets lost, your files are still here. Support us if you love the project ❤️.</p>
            </section>

            <section class="card">
                <h2>🔐 Account Info</h2>
                <ul class="details">
                    <li><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></li>
                    <li><strong>Plan:</strong> <?php echo htmlspecialchars($plan); ?></li>
                </ul>
            </section>

         <section class="card">
            <h2>📤 Upload Files</h2>
            <a class="upload-btn" href="upload.php">Upload Now</a><br><br>
            <a class="upload-btn" href="logout.php" style="background: #dc3545;">Logout</a>
        </section>

        </div>
    </main>
</body>
</html>
