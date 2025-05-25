test_error.php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Hello from PHP";

$conn = new mysqli("localhost", "root", "", "gym_management");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT COUNT(*) as total FROM users");
$data = $result->fetch_assoc();
echo "<br>Total users: " . $data['total'];
?>
