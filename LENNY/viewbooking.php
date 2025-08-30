<?php
require_once 'database.php';

$db = new Database('localhost', 'root', '', 'car_rentals');
$bookings = $db->getAllBookings();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h1 class="mb-4">All Bookings</h1>
    
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Vehicle Type</th>
                    <th>Pickup Date</th>
                    <th>Return Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['id']) ?></td>
                    <td><?= htmlspecialchars($booking['Fullname']) ?></td>
                    <td><?= htmlspecialchars($booking['email']) ?></td>
                    <td><?= htmlspecialchars($booking['Phone']) ?></td>
                    <td><?= htmlspecialchars($booking['Vehicle_type']) ?></td>
                    <td><?= htmlspecialchars($booking['Pickup_date']) ?></td>
                    <td><?= htmlspecialchars($booking['Return_date']) ?></td>
                    <td>
                        <a href="update.php?id=<?= $booking['id'] ?>" >Edit</a>
                        <a href="delete.php?id=<?= $booking['id'] ?>" class="BUTTON" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <a href="index.php" class="btn btn-primary">New Booking</a>
  
</body>
</html>