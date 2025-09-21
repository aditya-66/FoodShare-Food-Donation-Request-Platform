<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';  // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data safely
    $donor_id       = $_POST['donor_id'] ?? null;
    $donor_name     = $_POST['donor_name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $food_name      = $_POST['food_name'] ?? '';
    $food_type      = $_POST['food_type'] ?? '';
    $quantity       = $_POST['quantity'] ?? 0;
    $pickup_date    = $_POST['pickup_date'] ?? '';
    $location       = $_POST['location'] ?? '';
    $notes          = $_POST['notes'] ?? '';
    $expiry         = $_POST['expiry'] ?? null;

    // ---------- IMAGE UPLOAD HANDLING ----------
    $imagePath = null; // default if no image
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] == 0) {
        $targetDir = "uploads/";
        
        // Make sure uploads/ exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Unique filename
        $fileName = time() . "_" . basename($_FILES["food_image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $targetFilePath)) {
            $imagePath = $targetFilePath; // Save relative path
        }
    }

    // ---------- INSERT INTO DB ----------
    $sql = "INSERT INTO donations 
            (donor_id, donor_name, contact_number, food_name, food_type, quantity, pickup_date, location, notes, image_path, status, created_at, expiry) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW(), ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("❌ Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "issssisssss", 
        $donor_id, 
        $donor_name, 
        $contact_number, 
        $food_name, 
        $food_type, 
        $quantity, 
        $pickup_date, 
        $location, 
        $notes, 
        $imagePath, 
        $expiry
    );

    if ($stmt->execute()) {
        echo "<div style='color:green; font-size:18px;'>✅ Donation added successfully!</div>";
        echo "<a href='browse.php'>Browse Donations</a>";
    } else {
        echo "<div style='color:red; font-size:18px;'>❌ Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
