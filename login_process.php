<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gym_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);

// Prepare correct table based on role
if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
} else {
    $stmt = $conn->prepare("SELECT id, username, password, role FROM members WHERE username = ?");
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Since you don't hash passwords (for assignment), compare plain text
    if ($password === $user['password']) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $role;  // important: use $role from form to avoid confusion

        // Redirect based on role selected at login
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: member_dashboard.php");
        }
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    echo "Invalid username or password.";
}

$stmt->close();
$conn->close();
?>
