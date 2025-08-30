<?php
require_once 'config.php';

if (!isLoggedIn() || (!hasRole('admin') && !hasRole('lecturer'))) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthDate = $_POST['birth_date'] ?? null;
    $birthTown = $_POST['birth_town'] ?? '';
    $street = $_POST['street'] ?? '';
    $hometown = $_POST['hometown'] ?? '';
    $class = $_POST['class'] ?? '';
    $position = $_POST['position'] ?? '';
    $startDate = $_POST['start_date'] ?? null;
    $password = $_POST['password'] ?? '';
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($position) || empty($password)) {
        $_SESSION['error_message'] = "First name, last name, position and password are required";
        redirect('control.php');
    }
    
    $conn = getDBConnection();
    
    // Insert new person
    $stmt = $conn->prepare("
        INSERT INTO Persons (
            first_name, last_name, gender, birth_date, birth_town,
            street, hometown, class, position, start_date, password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        "sssssssssss",
        $firstName, $lastName, $gender, $birthDate, $birthTown,
        $street, $hometown, $class, $position, $startDate, $password
    );
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Person added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding person: " . $conn->error;
    }
    
    $conn->close();
    redirect('control.php');
} else {
    redirect('control.php');
}