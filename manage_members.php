<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include("db_connect.php");

$result = $conn->query("SELECT id, username, email FROM members ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manage Members</title>
<style>
  /* Reset some default styles */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f7f8;
    margin: 20px;
    color: #333;
  }
  h1 {
    text-align: center;
    color: #007BFF;
    margin-bottom: 30px;
  }
  table {
    border-collapse: collapse;
    width: 90%;
    margin: 0 auto 40px auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
  }
  th, td {
    padding: 14px 20px;
    text-align: left;
  }
  th {
    background-color: #007BFF;
    color: white;
    font-weight: 600;
  }
  tr:nth-child(even) {
    background-color: #f9fafb;
  }
  tr:hover {
    background-color: #e9f2ff;
  }
  a {
    color: #007BFF;
    text-decoration: none;
    font-weight: 600;
    margin-right: 10px;
  }
  a:hover {
    text-decoration: underline;
  }
  .container {
    max-width: 1000px;
    margin: auto;
  }
  .back-link {
    display: block;
    text-align: center;
    margin-top: 10px;
    font-weight: 600;
  }
  /* Responsive */
  @media (max-width: 600px) {
    table, thead, tbody, th, td, tr { 
      display: block; 
    }
    tr {
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      background: #fff;
      padding: 10px;
    }
    th {
      display: none;
    }
    td {
      padding-left: 50%;
      position: relative;
      text-align: right;
    }
    td::before {
      content: attr(data-label);
      position: absolute;
      left: 20px;
      width: 45%;
      padding-left: 10px;
      font-weight: 600;
      text-align: left;
      color: #555;
    }
  }
</style>
</head>
<body>
<div class="container">
  <h1>Manage Members</h1>
  <table>
      <thead>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td data-label="ID"><?php echo htmlspecialchars($row['id']); ?></td>
            <td data-label="Username"><?php echo htmlspecialchars($row['username']); ?></td>
            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
            <td data-label="Actions">
                <a href="edit_member.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="delete_member.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this member?');">Delete</a>
            </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
  </table>
  <a href="admin_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
