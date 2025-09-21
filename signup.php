<?php
// signup.php - signup form and handler
include 'config.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('ssss', $name, $email, $password, $role);

    if ($stmt->execute()) {
        $success = "✅ Account created successfully! You can now <a href='login.php'>Login</a>.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: url("bgrr.jpg") no-repeat center center/cover;
      font-family: 'Poppins', sans-serif;
      position: relative;
    }

    /* ✅ Blur overlay */
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(3px); /* control blur level */
      background-color: rgba(0,0,0,0.3); /* optional dark overlay */
      z-index: 0;
    }

    .card {
      position: relative;
      z-index: 1; /* keeps card above blur */
      background: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      padding: 2rem;
      max-width: 420px;
      width: 100%;
      backdrop-filter: blur(4px); /* slight glass effect */
    }

    .btn-success {
      width: 100%;
    }
    a {
      text-decoration: none;
      color: #28a745;
      font-weight: 500;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="card shadow">
  <h2 class="text-center fw-bold mb-3">Sign Up</h2>
  
  <?php if($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <?php if($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php else: ?>
    <form method="post">
      <div class="mb-3"><input name="name" type="text" class="form-control" placeholder="Name" required></div>
      <div class="mb-3"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
      <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
      <div class="mb-3">
        <select name="role" class="form-select" required>
          <option value="donor">Donor</option>
          <option value="receiver">Receiver</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button class="btn btn-success w-100" type="submit">Create Account</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
  <?php endif; ?>
</div>
</body>
</html>
