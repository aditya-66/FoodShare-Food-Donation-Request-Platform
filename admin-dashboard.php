<?php
// admin-dashboard.php - simple admin view
include 'config.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: login.php');
  exit;
}
$donations = $conn->query("SELECT d.id, d.food_name, d.quantity, d.expiry, d.location, u.name AS donor_name, d.status FROM donations d JOIN users u ON d.donor_id = u.id ORDER BY d.id DESC");
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container"><a class="navbar-brand text-success fw-bold" href="index.html">FoodShare</a>
    <div class="d-flex">
      <span class="me-3">Admin</span>
      <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-5">
<h2 class="text-center mb-4">Admin Dashboard</h2>
<table class="table table-bordered">
  <thead class="table-success"><tr><th>Donation</th><th>Donor</th><th>Quantity</th><th>Expiry</th><th>Location</th><th>Status</th></tr></thead>
  <tbody>
    <?php while($row = $donations->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['food_name']); ?></td>
      <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
      <td><?php echo htmlspecialchars($row['quantity']); ?></td>
      <td><?php echo htmlspecialchars($row['expiry']); ?></td>
      <td><?php echo htmlspecialchars($row['location']); ?></td>
      <td><?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div></body></html>
