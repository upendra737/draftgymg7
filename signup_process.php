<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gym_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive and sanitize input
$username = htmlspecialchars(trim($_POST['username']));
$email = htmlspecialchars(trim($_POST['email']));
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);

// Check if passwords match
if ($password !== $confirm_password) {
    echo "Passwords do not match. Redirecting...";
    header("refresh:2; url=login.php");
    exit;
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM members WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Username already taken. Redirecting...";
    $stmt->close();
    $conn->close();
    header("refresh:2; url=login.php");
    exit;
}
$stmt->close();

// Insert new member (you can hash password later if needed)
$stmt = $conn->prepare("INSERT INTO members (username, email, password, role) VALUES (?, ?, ?, 'member')");
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'member';

    header("Location: member_dashboard.php");
    exit;
} else {
    echo "Signup failed. Please try again.";
}

$stmt->close();
$conn->close();
?>
