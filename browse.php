<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php'; // should contain $conn

// Fetch donations with donor info (fallback to donor_name from donations table)
$sql = "SELECT d.id, d.food_name, d.quantity, d.expiry, d.location, d.image_path, d.created_at,
               COALESCE(u.name, d.donor_name) AS donor_name
        FROM donations d
        LEFT JOIN donors u ON d.donor_id = u.id
        ORDER BY d.created_at DESC";

$result = $conn->query($sql);
if (!$result) {
    die("❌ Error fetching donations: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse Donations - FoodShare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
.container { margin-top: 50px; }
.card { border-radius: 12px; overflow: hidden; }
.card img { height: 200px; object-fit: cover; }
</style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4 text-success"><b>Available Donations</b></h2>
    <div class="row g-4">

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()):
            $donor_name = $row['donor_name'] ?? 'Anonymous';

            // ✅ Fix: Ensure correct path for uploaded images
            $image = !empty($row['image_path']) 
                ? "uploads/" . basename($row['image_path']) 
                : "https://via.placeholder.com/400x200?text=No+Image";

            // ✅ Expiry = created_at + 2 days (always set)
            $expiry = date('d M Y, H:i', strtotime($row['created_at'] . ' +2 days'));
        ?>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="food" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($row['food_name']); ?> 
                            (<?php echo htmlspecialchars($row['quantity']); ?>)
                        </h5>
                        <p class="card-text">
                            <strong>Donor:</strong> <?php echo htmlspecialchars($donor_name); ?><br>
                            <strong>Pickup Location:</strong> <?php echo htmlspecialchars($row['location']); ?><br>
                            <strong>Expiry:</strong> <?php echo $expiry; ?>
                        </p>
                        <a href="request.html" class="btn btn-success w-100">Request</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">No donations available at the moment.</div>
    <?php endif; ?>

    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
