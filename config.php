<?php
$servername = "localhost";
$username = "root";   // or your MySQL user
$password = "";       // your MySQL password
$dbname = "foodwaste";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
