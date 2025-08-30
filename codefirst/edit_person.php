<?php
require_once 'config.php';

if (!isLoggedIn() || (!hasRole('admin') && !hasRole('lecturer'))) {
    redirect('login.php');
}

// Check if person ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('control.php');
}

$personId = (int)$_GET['id'];
$conn = getDBConnection();

// Fetch person data
$stmt = $conn->prepare("SELECT * FROM Persons WHERE person_id = ?");
$stmt->bind_param("i", $personId);
$stmt->execute();
$person = $stmt->get_result()->fetch_assoc();

if (!$person) {
    $conn->close();
    redirect('control.php');
}

// Handle form submission
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
    if (empty($firstName) || empty($lastName) || empty($position)) {
        $error = "First name, last name and position are required";
    } else {
        // Prepare update statement
        $sql = "UPDATE Persons SET 
                first_name = ?, 
                last_name = ?, 
                gender = ?, 
                birth_date = ?, 
                birth_town = ?, 
                street = ?, 
                hometown = ?, 
                class = ?, 
                position = ?, 
                start_date = ?";
        
        $params = [
            $firstName, $lastName, $gender, $birthDate, $birthTown,
            $street, $hometown, $class, $position, $startDate
        ];
        
        $types = "ssssssssss";
        
        // Add password if provided
        if (!empty($password)) {
            $sql .= ", password = ?";
            $params[] = $password;
            $types .= "s";
        }
        
        $sql .= " WHERE person_id = ?";
        $params[] = $personId;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success = "Person updated successfully!";
            // Refresh person data
            $stmt = $conn->prepare("SELECT * FROM Persons WHERE person_id = ?");
            $stmt->bind_param("i", $personId);
            $stmt->execute();
            $person = $stmt->get_result()->fetch_assoc();
        } else {
            $error = "Error updating person: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Person - <?= APP_NAME ?></title>
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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: <?= PRIMARY_COLOR ?>;
            margin-top: 0;
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
        input[type="date"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
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
        .btn-secondary {
            background-color: #6c757d;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="Logo_Worldskills_Ghana.png" alt="Logo" class="logo">
            <span>Edit Person</span>
        </div>
    </div>
    
    <div class="container">
        <h2>Edit Person: <?= htmlspecialchars($person['first_name'] . ' ' . htmlspecialchars($person['last_name']) ?></h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?= htmlspecialchars($person['first_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?= htmlspecialchars($person['last_name']) ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="M" <?= $person['gender'] === 'M' ? 'selected' : '' ?>>Male</option>
                        <option value="F" <?= $person['gender'] === 'F' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Position:</label>
                    <select id="position" name="position" required>
                        <option value="student" <?= $person['position'] === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="lecturer" <?= $person['position'] === 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                        <?php if (hasRole('admin')): ?>
                            <option value="administrator" <?= $person['position'] === 'administrator' ? 'selected' : '' ?>>Administrator</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="birth_date">Birth Date:</label>
                    <input type="date" id="birth_date" name="birth_date" 
                           value="<?= htmlspecialchars($person['birth_date']) ?>">
                </div>
                <div class="form-group">
                    <label for="birth_town">Birth Town:</label>
                    <input type="text" id="birth_town" name="birth_town" 
                           value="<?= htmlspecialchars($person['birth_town']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" 
                           value="<?= htmlspecialchars($person['street']) ?>">
                </div>
                <div class="form-group">
                    <label for="hometown">Hometown:</label>
                    <input type="text" id="hometown" name="hometown" 
                           value="<?= htmlspecialchars($person['hometown']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="class">Class:</label>
                    <input type="text" id="class" name="class" 
                           value="<?= htmlspecialchars($person['class']) ?>">
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" 
                           value="<?= htmlspecialchars($person['start_date']) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password (leave blank to keep current):</label>
                <input type="text" id="password" name="password">
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn">Save Changes</button>
                <a href="control.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>