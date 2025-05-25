<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in and is a member
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

// Database connection (your db.php content)
$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password is empty
$dbname = "gym_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get posted values and sanitize
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);

    // Validate required fields
    if (empty($name) || empty($email)) {
        $message = "Please fill in all required fields (Name and Email).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Prepare update statement
        $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $gender, $user_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully.";
        } else {
            $message = "Error updating profile.";
        }
        $stmt->close();
    }
}

// Fetch current profile data
$stmt = $conn->prepare("SELECT username, name, email, phone, gender, role, join_date FROM members WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $name, $email, $phone, $gender, $role, $join_date);
$stmt->fetch();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Profile - Gym Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Rubik', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f9f9f9;
        }
        header {
            background: #222;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header h1 {
            margin: 0;
        }
        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 600;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            background: #fff;
            padding: 20px 30px;
            margin-top: 20px;
            border-radius: 8px;
            max-width: 500px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }
        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 10px 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[readonly], input[disabled] {
            background: #eee;
            cursor: not-allowed;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #ffcc00;
            border: none;
            padding: 12px 25px;
            font-weight: 700;
            cursor: pointer;
            border-radius: 6px;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #e6b800;
        }
        .message {
            margin-top: 15px;
            color: green;
            font-weight: 600;
        }
        .error {
            color: red;
        }
        footer {
            margin-top: 40px;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <h1>Your Gym Portal</h1>
    <nav>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="attendance.php"><i class="fa fa-calendar-check"></i> Attendance</a>
        <a href="plans.php"><i class="fa fa-dumbbell"></i> Plans</a>
        <a href="support.php"><i class="fa fa-life-ring"></i> Support</a>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Your Profile</h2>

    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username (not editable):</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" readonly>

        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="" <?= $gender === null || $gender === "" ? "selected" : "" ?>>-- Select --</option>
            <option value="Male" <?= $gender === "Male" ? "selected" : "" ?>>Male</option>
            <option value="Female" <?= $gender === "Female" ? "selected" : "" ?>>Female</option>
            <option value="Other" <?= $gender === "Other" ? "selected" : "" ?>>Other</option>
        </select>

        <label>Role (not editable):</label>
        <input type="text" value="<?= htmlspecialchars($role) ?>" readonly>

        <label>Join Date (not editable):</label>
        <input type="text" value="<?= htmlspecialchars($join_date ?? '') ?>" readonly>

        <input type="submit" value="Update Profile">
    </form>
</div>

<footer>
    &copy; <?= date('Y') ?> Your Gym. Stay strong and healthy!
</footer>

</body>
</html>
