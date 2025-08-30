<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $conn = getDBConnection();
    
    // Check in Users table
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (verifyPassword($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            redirect('control.php');
        } else {
            $error = "Invalid password";
        }
    } else {
        // Check in Persons table
        $stmt = $conn->prepare("
            SELECT person_id, first_name, last_name, position, password 
            FROM Persons 
            WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR last_name LIKE ?
        ");
        $searchTerm = "%$username%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $person = $result->fetch_assoc();
            if ($password === $person['password']) { // Simple comparison for demo
                $_SESSION['user'] = [
                    'id' => $person['person_id'],
                    'name' => $person['first_name'] . ' ' . $person['last_name'],
                    'role' => $person['position']
                ];
                redirect('control.php');
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Invalid username";
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
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
        }
        .logo {
            height: 50px;
            margin-right: 15px;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: <?= PRIMARY_COLOR ?>;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background-color: <?= SECONDARY_COLOR ?>;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .links {
            margin-top: 15px;
            text-align: center;
        }
        .links a {
            color: <?= PRIMARY_COLOR ?>;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="Logo_Worldskills_Ghana.png" alt="Logo" class="logo">
        <h1>HTU University Login</h1>
    </div>
    
    <div class="container">
        <h2>Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
            
            <div class="links">
                <a href="register.php">Register</a>
                <a href="change_password.php">Change Password</a>
            </div>
        </form>
    </div>
</body>
</html>