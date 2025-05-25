<?php
// Show errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

// 1) Authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

$memberId = (int)$_SESSION['user_id'];
$errorMsg  = '';
$successMsg = '';
$subject   = '';
$message   = '';

// 2) Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']  ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($subject === '' || $message === '') {
        $errorMsg = 'Please fill in both Subject and Message.';
    } else {
        $ins = $conn->prepare(
            "INSERT INTO support_messages (member_id, subject, message, created_at)
             VALUES (?, ?, ?, NOW())"
        );
        if (!$ins) {
            $errorMsg = 'Prepare failed: ' . $conn->error;
        } else {
            $ins->bind_param('iss', $memberId, $subject, $message);
            if ($ins->execute()) {
                $successMsg = '✅ Your message has been sent!';
                $subject = $message = ''; // clear fields
            } else {
                $errorMsg = 'Insert failed: ' . $ins->error;
            }
            $ins->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Support - Gym Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body { font-family:'Rubik',sans-serif; background:#f4f4f4; color:#333; margin:0; padding:0; }
    header { background:#222; color:#ffcc00; padding:15px 30px; display:flex; align-items:center; }
    header h1 { margin:0; font-size:1.5em; flex:1; }
    nav a { color:#ffcc00; margin-left:15px; text-decoration:none; font-weight:600; }
    nav a:hover { text-decoration:underline; }
    .container { max-width:600px; margin:40px auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { margin-top:0; color:#222; text-align:center; }
    label { display:block; font-weight:600; margin-top:20px; }
    input,textarea { width:100%; padding:12px; margin-top:8px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:1em; }
    button { background:#ffcc00; border:none; padding:12px 25px; border-radius:6px; cursor:pointer; font-weight:700; transition:background .3s; }
    button:hover { background:#e6b800; }
    .message { padding:12px; border-radius:6px; margin-bottom:20px; }
    .error { background:#ffe0e0; color:#900; }
    .success { background:#e0ffe0; color:#070; }
    footer { text-align:center; padding:15px; background:#222; color:#ffcc00; margin-top:40px; }
  </style>
</head>
<body>

<header>
  <h1>Your Gym Portal</h1>
  <nav>
    <a href="profile.php"><i class="fa fa-user"></i></a>
    <a href="attendance.php"><i class="fa fa-calendar-check"></i></a>
    <a href="plans.php"><i class="fa fa-dumbbell"></i></a>
    <a href="support.php"><i class="fa fa-life-ring"></i></a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </nav>
</header>

<div class="container">
  <h2>Contact Support</h2>

  <?php if ($errorMsg): ?>
    <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
  <?php elseif ($successMsg): ?>
    <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
  <?php endif; ?>

  <form method="post" action="support.php">
    <label for="subject">Subject</label>
    <input type="text" id="subject" name="subject" required value="<?= htmlspecialchars($subject) ?>">

    <label for="message">Message</label>
    <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($message) ?></textarea>

    <button type="submit"><i class="fa fa-paper-plane"></i> Send Message</button>
  </form>
</div>

<footer>
  &copy; <?= date('Y') ?> Your Gym. Stay strong and healthy!
</footer>

</body>
</html>
