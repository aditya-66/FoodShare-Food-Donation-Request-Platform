<?php
// login.php - simple login (only confirms success)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
session_start();

// ✅ Initialize variables to avoid "undefined variable" warnings
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUser = trim($_POST['email']);
    $password = $_POST['password'];

    // Try login against donors, users, admins
    $queries = [
        ["SELECT id, password, 'donor' AS role, name FROM donors WHERE email = ?", 's'],
        ["SELECT id, password, role, name FROM users WHERE email = ?", 's'],
        ["SELECT id, password, 'admin' AS role, username AS name FROM admins WHERE username = ? OR email = ?", 'ss'],
    ];

    $user = null;

    foreach ($queries as $q) {
        [$sql, $bind] = $q;
        $stmt = $conn->prepare($sql);
        if (!$stmt) continue; // skip if table doesn't exist

        if ($bind === 's') {
            $stmt->bind_param('s', $emailOrUser);
        } else {
            $stmt->bind_param('ss', $emailOrUser, $emailOrUser);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $stmt->close();
            break;
        }
        $stmt->close();
    }

    if (!$user) {
        $error = "No account found with that email/username.";
    } else {
        $hash = $user['password'];
        $passwordIsValid = false;

        if (!empty($hash) && password_verify($password, $hash)) {
            $passwordIsValid = true;
        } elseif ($password === $hash) { // plain-text fallback
            $passwordIsValid = true;
        }

        if ($passwordIsValid) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            $success = "✅ Login successful! Welcome, " . htmlspecialchars($_SESSION['name']) . ".";
        } else {
            $error = "Invalid password.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: url('https://images.unsplash.com/photo-1490818387583-1baba5e638af?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Zm9vZCUyMGJhY2tncm91bmR8ZW58MHx8MHx8fDA%3D') no-repeat center center fixed;
      background-size: cover;
    }
    /* ✅ Blur overlay */
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(3px); /* 🔥 3px blur */
      background-color: rgba(0, 0, 0, 0.2); /* optional overlay for contrast */
      z-index: 0;
    }
    .card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 12px;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
<div class="card shadow p-4" style="width: 420px;">
  <h2 class="text-center">Login</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?><br>
      <a href="index.html" class="btn btn-link">Go to Home</a> |
      <a href="donate.html" class="btn btn-link">Donate Food</a> |
      <a href="browse.php" class="btn btn-link">Browse Donations</a>
    </div>
  <?php else: ?>
    <form method="post">
      <div class="mb-3">
        <input name="email" type="text" class="form-control" placeholder="Email or username" required>
      </div>
      <div class="mb-3">
        <input name="password" type="password" class="form-control" placeholder="Password" required>
      </div>
      <button class="btn btn-success w-100" type="submit">Login</button>
    </form>
    <p class="mt-3 text-center">Don't have an account? <a href="signup.php">Sign Up</a></p>
  <?php endif; ?>
</div>
</body>
</html>
