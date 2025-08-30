<?php
// session_start();
require_once 'database.php';

$db = new Database('localhost', 'root', '', 'car_rentals');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $data = [
        'Fullname' => trim($_POST['Fullname'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'Phone' => trim($_POST['Phone'] ?? ''),
        'Vehicle_type' => $_POST['Vehicle_type'] ?? '',
        'Pickup_date' => $_POST['Pickup_date'] ?? '',
        'Return_date' => $_POST['Return_date'] ?? '',
        'Comment' => trim($_POST['Comment'] ?? '')
    ];
    
    if ($db->updateBooking($id, $data)) {
        header('Location: viewbooking.php');
        exit;
    } else {
        $error = "Failed to update booking";
    }
}

$id = $_GET['id'] ?? null;
$booking = $id ? $db->getBooking($id) : null;

if (!$booking) {
    header('Location: viewbooking.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h1 class="mb-4">Edit Booking</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="id" value="<?= $booking['id'] ?>">
        
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="Fullname" class="form-control" value="<?= htmlspecialchars($booking['Fullname']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($booking['email']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="Phone" class="form-control" value="<?= htmlspecialchars($booking['Phone']) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Vehicle Type</label>
            <select name="Vehicle_type" class="form-control" required>
                <option value="Selection_Offer_A" <?= $booking['Vehicle_type'] === 'Selection_Offer_A' ? 'selected' : '' ?>>Select Offer A</option>
                <option value="Selection_offer_B" <?= $booking['Vehicle_type'] === 'Selection_offer_B' ? 'selected' : '' ?>>Select Offer B</option>
                <option value="Selection_offer_C" <?= $booking['Vehicle_type'] === 'Selection_offer_C' ? 'selected' : '' ?>>Select Offer C</option>
                <option value="Selection_offer_D" <?= $booking['Vehicle_type'] === 'Selection_offer_D' ? 'selected' : '' ?>>Select Offer D</option>
            </select>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Pickup Date</label>
                <input type="date" name="Pickup_date" class="form-control" value="<?= htmlspecialchars($booking['Pickup_date']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Return Date</label>
                <input type="date" name="Return_date" class="form-control" value="<?= htmlspecialchars($booking['Return_date']) ?>" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="Comment" class="form-control" rows="3"><?= htmlspecialchars($booking['Comment']) ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Booking</button>
        <a href="viewbooking.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>
</html>