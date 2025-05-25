<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['plan_id'])) {
    echo "No plan selected.";
    exit();
}

$planId = intval($_GET['plan_id']);

// Fetch plan details
$stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
$stmt->bind_param("i", $planId);
$stmt->execute();
$result = $stmt->get_result();
$plan = $result->fetch_assoc();
$stmt->close();

if (!$plan) {
    echo "Invalid plan.";
    exit();
}

// Immediately activate the plan (simulate payment)
$userId = $_SESSION['user_id'];
$stmtUpdate = $conn->prepare("UPDATE users SET membership_type = ? WHERE id = ?");
$stmtUpdate->bind_param("ii", $planId, $userId);

if ($stmtUpdate->execute()) {
    $_SESSION['message'] = "Plan activated successfully!";
    header("Location: plans.php");
    exit();
} else {
    $error = "Failed to activate plan. Please try again.";
}
$stmtUpdate->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activating Plan - <?= htmlspecialchars($plan['name']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Activating Your Plan...</h1>
    <p><strong>Plan:</strong> <?= htmlspecialchars($plan['name']) ?></p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($plan['duration']) ?> months</p>
    <p><strong>Price:</strong> $<?= htmlspecialchars($plan['price']) ?></p>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <p>You will be redirected shortly.</p>
</div>

<script>
    setTimeout(() => {
        window.location.href = 'plans.php';
    }, 2000);
</script>
</body>
</html>
