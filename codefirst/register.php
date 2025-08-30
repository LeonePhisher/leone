<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error = "Please fill all fields";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $conn = getDBConnection();
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Hash password
            $passwordHash = hashPassword($password);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO Users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $passwordHash, $role);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <style>
        /* Similar styling to login.php */
    </style>
</head>
<body>
    <div class="header">
        <img src="Logo_Worldskills_Ghana.png" alt="Logo" class="logo">
        <h1>Register New User</h1>
    </div>
    
    <div class="container">
        <h2>Register</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
            <p><a href="login.php">Go to Login</a></p>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Register</button>
                
                <div class="links">
                    <a href="login.php">Back to Login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>