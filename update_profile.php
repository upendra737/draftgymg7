update_profile.php
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Simple validation (you can expand this)
    if (!$name || !$email) {
        $_SESSION['message'] = "Name and Email are required.";
        header("Location: profile.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE members SET name=?, email=?, phone=?, gender=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $gender, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit;
}
?>
