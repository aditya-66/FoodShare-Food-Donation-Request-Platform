<?php
// register_receiver.php
include 'config.php'; // make sure your config.php has correct DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect and sanitize form data
    $name = trim($_POST['name']);
    $organization = trim($_POST['organization']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password

    // Prepare SQL to insert data
    $sql = "INSERT INTO receivers (name, organization, email, phone, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $name, $organization, $email, $phone, $password);

    if ($stmt->execute()) {
        echo "<div style='text-align:center; margin-top:50px;'>
                ✅ Receiver registered successfully!<br>
                Name: $name <br>Email: $email <br>Phone: $phone
              </div>";
    } else {
        echo "Error inserting data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
