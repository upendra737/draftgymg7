<?php
// Show errors for development
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

// 1) Auth check
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='member'){
    header("Location: login.php");
    exit;
}

$memberId = (int)$_SESSION['user_id'];
$errorMsg = '';
$successMsg = '';

// 2) Handle Check-In
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['checkin'])){
    // Prevent double check-in
    $today = date('Y-m-d');
    $chk = $conn->prepare("
        SELECT 1 FROM attendance 
        WHERE member_id = ? AND checkin_date = ?
    ");
    $chk->bind_param("is", $memberId, $today);
    $chk->execute();
    $chk->store_result();
    if($chk->num_rows > 0){
        $errorMsg = "You’ve already checked in today.";
    } else {
        $ins = $conn->prepare("
            INSERT INTO attendance (member_id, checkin_date, checkin_time)
            VALUES (?, ?, ?)
        ");
        $timeNow = date('H:i:s');
        $ins->bind_param("iss", $memberId, $today, $timeNow);
        if($ins->execute()){
            $successMsg = "✅ Checked in at $timeNow";
        } else {
            $errorMsg = "Check-in failed: " . $ins->error;
        }
        $ins->close();
    }
    $chk->close();
}

// 3) Fetch all attendance
$stmt = $conn->prepare("
    SELECT checkin_date, checkin_time 
    FROM attendance 
    WHERE member_id = ? 
    ORDER BY checkin_date DESC, checkin_time DESC
");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$records = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Attendance - Gym Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body { font-family:'Rubik',sans-serif; background:#f4f4f4; color:#333; margin:0; padding:0; }
    header { background:#222; color:#ffcc00; padding:15px 30px; display:flex; align-items:center; }
    header h1 { margin:0; font-size:1.5em; flex:1; }
    nav a { color:#ffcc00; margin-left:15px; text-decoration:none; font-weight:600; }
    nav a:hover { text-decoration:underline; }
    .container { max-width:800px; margin:40px auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { margin-top:0; color:#222; text-align:center; }
    .message { padding:12px; border-radius:6px; margin-bottom:20px; }
    .error { background:#ffe0e0; color:#900; }
    .success { background:#e0ffe0; color:#070; }
    form { text-align:center; margin-bottom:25px; }
    button { background:#ffcc00; border:none; padding:12px 25px; border-radius:6px; cursor:pointer; font-weight:700; transition:background .3s; }
    button:hover { background:#e6b800; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
    th { background:#3498db; color:#fff; }
    tr:hover { background:#f1f1f1; }
    .today { background:#fffbe6; }
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
  <h2>Attendance Records</h2>

  <?php if($errorMsg): ?>
    <div class="message error"><?=htmlspecialchars($errorMsg)?></div>
  <?php elseif($successMsg): ?>
    <div class="message success"><?=htmlspecialchars($successMsg)?></div>
  <?php endif; ?>

  <form method="post" action="attendance.php">
    <button type="submit" name="checkin"><i class="fa fa-sign-in-alt"></i> Check In for Today</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Time</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $today = date('Y-m-d');
      while($row = $records->fetch_assoc()):
        $cls = $row['checkin_date']==$today ? 'today' : '';
      ?>
        <tr class="<?=$cls?>">
          <td><?=htmlspecialchars($row['checkin_date'])?></td>
          <td><?=htmlspecialchars($row['checkin_time'])?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<footer>
  &copy; <?=date('Y')?> Your Gym. Stay strong and healthy!
</footer>

</body>
</html>
