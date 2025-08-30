<?php
require_once 'config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    redirect('login.php');
}

// Check if person ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('control.php');
}

$personId = (int)$_GET['id'];
$conn = getDBConnection();

// Check if person exists
$stmt = $conn->prepare("SELECT first_name, last_name FROM Persons WHERE person_id = ?");
$stmt->bind_param("i", $personId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $conn->close();
    redirect('control.php');
}

$person = $result->fetch_assoc();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM Persons WHERE person_id = ?");
    $stmt->bind_param("i", $personId);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Person deleted successfully!";
        redirect('control.php');
    } else {
        $error = "Error deleting person: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Person - <?= APP_NAME ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?= BG_COLOR ?>;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: <?= PRIMARY_COLOR ?>;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            height: 50px;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: <?= PRIMARY_COLOR ?>;
            margin-top: 0;
        }
        .btn {
            background-color: <?= SECONDARY_COLOR ?>;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 0 10px;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="Logo_Worldskills_Ghana.png" alt="Logo" class="logo">
            <span>Delete Person</span>
        </div>
    </div>
    
    <div class="container">
        <h2>Confirm Deletion</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <p>Are you sure you want to delete <strong><?= htmlspecialchars($person['first_name'] . ' ' . htmlspecialchars($person['last_name'])) ?></strong>?</p>
        <p>This action cannot be undone.</p>
        
        <form method="POST" style="margin-top: 20px;">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="control.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>