<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Flash message logic
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$userId = $_SESSION['user_id'];

// Fetch all plans
$plans = [];
$resultPlans = $conn->query("SELECT * FROM plans");
if ($resultPlans->num_rows > 0) {
    while ($row = $resultPlans->fetch_assoc()) {
        $plans[] = $row;
    }
}

// Get current user's selected plan ID from session first
$currentPlanId = isset($_SESSION['membership_type']) ? intval($_SESSION['membership_type']) : null;

if (!$currentPlanId) {
    // Fallback to database if session not set
    $stmtUser = $conn->prepare("SELECT membership_type FROM users WHERE id = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $user = $resultUser->fetch_assoc();
    $stmtUser->close();

    $currentPlanId = isset($user['membership_type']) ? intval($user['membership_type']) : null;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Membership Plan</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        .container { max-width: 700px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .plan { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 10px; background: #f9f9f9; }
        .plan h3 { margin-top: 0; }
        .plan button, .plan a.button-link {
            padding: 10px 20px;
            background-color: #28a745; 
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .plan button:disabled {
            background-color: #aaa;
            cursor: default;
        }
        .notification {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #d6e9c6;
            border-radius: 6px;
        }
        .current-plan {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Select a Membership Plan</h2>

    <?php if (!empty($message)) : ?>
        <div class="notification" id="notification">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($plans)): ?>
        <p>No plans available.</p>
    <?php else: ?>
        <?php foreach ($plans as $row): ?>
            <div class="plan">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p>Duration: <?= htmlspecialchars($row['duration']) ?> days</p>
                <p>Price: $<?= htmlspecialchars($row['price']) ?></p>

                <?php if ($currentPlanId == $row['id']): ?>
                    <span class="current-plan">Current Plan</span>
                <?php else: ?>
                    <a href="checkout.php?plan_id=<?= $row['id'] ?>" class="button-link">Choose & Pay</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    setTimeout(() => {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }, 3000);
</script>
</body>
</html>
