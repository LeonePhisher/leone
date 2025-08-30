<?php
require_once 'database.php';

$db = new Database('localhost', 'root', '', 'car_rentals');

$data = [
    'Fullname' => trim($_POST['Fullname'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'Phone' => trim($_POST['Phone'] ?? ''),
    'Vehicle_type' => $_POST['Vehicle_type'] ?? '',
    'Pickup_date' => $_POST['Pickup_date'] ?? '',
    'Return_date' => $_POST['Return_date'] ?? '',
    'Comment' => trim($_POST['Comment'] ?? '')
];

// Validation
$errors = [];
if (empty($data['Fullname']) || !preg_match('/^[a-zA-Z ]+$/', $data['Fullname'])) {
    $errors[] = "Invalid Full Name";
}
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid Email";
}
if (empty($data['Phone']) || !preg_match('/^[0-9]+$/', $data['Phone'])) {
    $errors[] = "Invalid Phone";
}
if (empty($data['Vehicle_type'])) {
    $errors[] = "Vehicle Type is required";
}
if (empty($data['Pickup_date'])) {
    $errors[] = "Pickup Date is required";
}
if (empty($data['Return_date'])) {
    $errors[] = "Return Date is required";
}

if (!empty($errors)) {
    die(json_encode( $errors));
}

if ($db->saveBooking($data)) {
    header('Location: viewbooking.php');
    exit;
} else {
    die("Error saving booking");
}
?>