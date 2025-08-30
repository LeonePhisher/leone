<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'university_management');

// Application settings
define('APP_NAME', 'HTU University Management');
define('PRIMARY_COLOR', '#050589'); // Blue
define('SECONDARY_COLOR', '#FF8B00'); // Orange-Brown
define('ACCENT_COLOR', '#F5D200'); // Yellow
define('BG_COLOR', '#FFFFFF');

// Start session
session_start();

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Password hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Redirect function
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Get current user role
function currentUserRole() {
    return $_SESSION['user']['role'] ?? null;
}

// Check if user has required role
function hasRole($requiredRole) {
    return currentUserRole() === $requiredRole;
}

// Import students from CSV
function importStudentsFromCSV($filePath) {
    $conn = getDBConnection();
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        // Skip header
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $fullName = trim($data[0], '"');
            $nameParts = explode(',', $fullName);
            $lastName = trim($nameParts[0]);
            $firstName = isset($nameParts[1]) ? trim($nameParts[1]) : '';
            
            $gender = trim($data[1], '"');
            $birthTown = trim($data[2], '"');
            $birthDate = DateTime::createFromFormat('d.m.Y', trim($data[3], '"'))->format('Y-m-d');
            $street = trim($data[4], '"');
            $hometown = trim($data[5], '"');
            $class = trim($data[6], '"');
            $startDate = DateTime::createFromFormat('d.m.Y', trim($data[7], '"'))->format('Y-m-d');
            $password = trim($data[8], '"');
            $position = trim($data[9], '"');
            
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
            
            $stmt->execute();
        }
        fclose($handle);
    }
    
    $conn->close();
}
?>