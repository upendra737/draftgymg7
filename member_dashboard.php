<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Member Dashboard - Gym Management</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet" />

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>
  /* Reset & base */
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0;
    font-family: 'Rubik', sans-serif;
    background: #121212; /* Dark gym-like background */
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: rgba(33, 33, 33, 0.85);
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(8px);
    border-bottom: 2px solid #ffcc00;
  }
  header h1 {
    margin: 0;
    font-weight: 700;
    font-size: 1.8rem;
    letter-spacing: 1px;
    color: #ffcc00;
  }
  nav a {
    color: #fff;
    margin-left: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: color 0.3s ease;
  }
  nav a:hover {
    color: #ffcc00;
  }

  .container {
    flex: 1;
    padding: 40px 50px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .welcome {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 30px;
    text-shadow: 0 2px 6px rgba(0,0,0,0.6);
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 columns */
    grid-template-rows: repeat(2, auto);  /* 2 rows */
    gap: 30px;
  }

  .card {
    background: rgba(255, 255, 255, 0.07);
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    backdrop-filter: blur(12px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
  }
  .card:hover {
    transform: translateY(-12px);
    box-shadow: 0 15px 40px rgba(255, 204, 0, 0.6);
  }

  .card i {
    font-size: 90px;
    color: #ffcc00;
    margin-bottom: 20px;
  }

  .card h3 {
    font-size: 1.4rem;
    margin-bottom: 12px;
    font-weight: 700;
  }
  .card p {
    font-size: 1rem;
    color: #ddd;
  }

  footer {
    text-align: center;
    padding: 25px 10px;
    background: rgba(33, 33, 33, 0.8);
    font-size: 0.9rem;
    color: #bbb;
    border-top: 1px solid #444;
  }

  /* Responsive */
  @media (max-width: 900px) {
    .grid {
      grid-template-columns: repeat(2, 1fr); /* 2 columns on smaller */
      grid-template-rows: auto;
    }
  }
  @media (max-width: 600px) {
    header {
      flex-direction: column;
      text-align: center;
    }
    nav {
      margin-top: 15px;
    }
    .grid {
      grid-template-columns: 1fr; /* single column on mobile */
    }
  }
</style>
</head>
<body>

<header>
  <h1>Welcome to Your Gym Portal</h1>
  <nav>
    <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
    <a href="attendance.php"><i class="fa fa-calendar-check"></i> Attendance</a>
    <a href="plans.php"><i class="fa fa-dumbbell"></i> Plans</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </nav>
</header>

<div class="container">
  <div class="welcome">Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</div>

  <div class="grid">
    <div class="card" onclick="location.href='profile.php'">
      <i class="fa fa-user-circle"></i>
      <h3>Your Profile</h3>
      <p>View and update your personal info</p>
    </div>

    <div class="card" onclick="location.href='attendance.php'">
      <i class="fa fa-calendar-check"></i>
      <h3>Attendance</h3>
      <p>Check your attendance records</p>
    </div>

    <div class="card" onclick="location.href='plans.php'">
      <i class="fa fa-dumbbell"></i>
      <h3>Membership Plans</h3>
      <p>Explore and upgrade your plan</p>
    </div>

    <div class="card" onclick="location.href='support.php'">
      <i class="fa fa-headset"></i>
      <h3>Support</h3>
      <p>Contact support for help</p>
    </div>
  </div>
</div>

<footer>
  &copy; <?php echo date('Y'); ?> Your Gym. Stay strong and healthy!
</footer>

</body>
</html>
