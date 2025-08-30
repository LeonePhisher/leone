<?php
session_start();
require_once 'database.php';

// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: adminlogin.php');
//     exit;
// }

$db = new Database('localhost', 'root', '', 'car_rentals');
$id = $_GET['id'] ?? null;

if ($id && $db->deleteBooking($id)) {
    header('Location: viewbooking.php?success=Booking deleted successfully');
} else {
    header('Location: viewbooking.php?error=Failed to delete booking');
}
exit;
?>