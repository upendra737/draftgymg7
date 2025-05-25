<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include("db_connect.php");

// Get data counts
$user_count = 0;
$member_count = 0;
$support_count = 0;

try {
    $result = $conn->query("SELECT COUNT(*) AS total FROM users");
    $user_count = $result->fetch_assoc()['total'];

    $member_result = $conn->query("SELECT COUNT(*) AS total FROM members");
    $member_count = $member_result->fetch_assoc()['total'];

    $support_result = $conn->query("SELECT COUNT(*) AS total FROM support");
    $support_count = $support_result->fetch_assoc()['total'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Gym Management</title>

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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    cursor: default;
  }
  .card:hover {
    transform: translateY(-12px);
    box-shadow: 0 15px 40px rgba(255, 204, 0, 0.6);
  }

  .card h2 {
    font-size: 1.6rem;
    margin-bottom: 12px;
    font-weight: 700;
    color: #ffcc00;
  }
  .card p {
    font-size: 1.4rem;
    color: #ddd;
    margin: 0;
  }

  .actions {
    margin-top: 40px;
    text-align: center;
  }
  .actions a {
    display: inline-block;
    margin: 12px 15px;
    padding: 12px 25px;
    background-color: #ffcc00;
    color: #121212;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    transition: background-color 0.3s ease;
  }
  .actions a:hover {
    background-color: #e6b800;
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
      grid-template-columns: repeat(2, 1fr);
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
      grid-template-columns: 1fr;
    }
    .actions a {
      margin: 10px 5px;
      padding: 12px 18px;
      font-size: 1rem;
    }
  }
</style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <nav>
    <a href="manage_members.php"><i class="fa fa-users"></i> Members</a>
    <a href="view_support.php"><i class="fa fa-headset"></i> Support</a>
    <a href="add_admin.php"><i class="fa fa-user-shield"></i> Add Admin</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </nav>
</header>

<div class="container">
  <div class="welcome">Welcome, Admin <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</div>

  <div class="grid">
    <div class="card">
      <h2>Total Admins</h2>
      <p><?php echo $user_count; ?></p>
    </div>
    <div class="card">
      <h2>Total Members</h2>
      <p><?php echo $member_count; ?></p>
    </div>
    <div class="card">
      <h2>Support Queries</h2>
      <p><?php echo $support_count; ?></p>
    </div>
  </div>

  <div class="actions">
    <a href="manage_members.php"><i class="fa fa-users"></i> Manage Members</a>
    <a href="view_support.php"><i class="fa fa-headset"></i> View Support Messages</a>
    <a href="add_admin.php"><i class="fa fa-user-shield"></i> Add New Admin</a>
    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
  </div>
</div>

<footer>
  &copy; <?php echo date('Y'); ?> Your Gym. Stay strong and healthy!
</footer>

</body>
</html>
