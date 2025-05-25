<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include("db_connect.php");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        // In real app, hash the password before storing
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $message = "✅ New admin added successfully.";
        } else {
            $message = "⚠️ Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "⚠️ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f8fa;
            padding: 40px 20px;
            color: #333;
        }
        .container {
            max-width: 450px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 30px 40px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 4px;
            margin-bottom: 20px;
            border: 1px solid #ccd0d5;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }
        button {
            width: 100%;
            background-color: #3498db;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 16px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Add New Admin</h1>
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required autofocus />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">Add Admin</button>
    </form>
    <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
