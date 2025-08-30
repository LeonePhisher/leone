<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$currentRole = currentUserRole();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel - <?= APP_NAME ?></title>
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
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info span {
            margin-right: 15px;
        }
        .logout-btn {
            background-color: <?= SECONDARY_COLOR ?>;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .container {
            display: flex;
            min-height: calc(100vh - 80px);
        }
        .sidebar {
            width: 250px;
            background-color: <?= ACCENT_COLOR ?>;
            padding: 20px;
        }
        .sidebar h3 {
            color: <?= PRIMARY_COLOR ?>;
            border-bottom: 2px solid <?= PRIMARY_COLOR ?>;
            padding-bottom: 10px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar li {
            margin-bottom: 10px;
        }
        .sidebar a {
            color: black;
            text-decoration: none;
            font-weight: bold;
            display: block;
            padding: 8px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: rgba(0,0,0,0.1);
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card h3 {
            color: <?= PRIMARY_COLOR ?>;
            margin-top: 0;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <img src="Logo_Worldskills_Ghana.png" alt="Logo" class="logo">
            <span>HTU University Control Panel</span>
        </div>
        <div class="user-info">
            <span>Welcome, <?= $_SESSION['user']['username'] ?? $_SESSION['user']['name'] ?></span>
            <span>(<?= ucfirst($currentRole) ?>)</span>
            <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <h3>Navigation</h3>
            <ul>
                <li><a href="#" onclick="showSection('dashboard')">Dashboard</a></li>
                
                <?php if (hasRole('admin') || hasRole('lecturer')): ?>
                    <li><a href="#" onclick="showSection('persons')">Manage Persons</a></li>
                <?php endif; ?>
                
                <?php if (hasRole('admin')): ?>
                    <li><a href="#" onclick="showSection('halls')">Manage Halls</a></li>
                    <li><a href="#" onclick="showSection('reports')">Reports</a></li>
                    <li><a href="#" onclick="showSection('users')">Manage Users</a></li>
                <?php endif; ?>
                
                <li><a href="#" onclick="showSection('profile')">My Profile</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="card">
                <h3>Dashboard</h3>
                <p>Welcome to the HTU University Management System.</p>
                <p>Select an option from the sidebar to get started.</p>
                
                <?php
                $conn = getDBConnection();
                
                // Count students
                $result = $conn->query("SELECT COUNT(*) as total FROM Persons WHERE position = 'student'");
                $students = $result->fetch_assoc()['total'];
                
                // Count lecturers
                $result = $conn->query("SELECT COUNT(*) as total FROM Persons WHERE position = 'lecturer'");
                $lecturers = $result->fetch_assoc()['total'];
                
                // Count halls
                $result = $conn->query("SELECT COUNT(*) as total FROM Halls");
                $halls = $result->fetch_assoc()['total'];
                
                $conn->close();
                ?>
                
                <div style="display: flex; gap: 20px; margin-top: 20px;">
                    <div style="flex: 1; background-color: #f0f0f0; padding: 15px; border-radius: 5px;">
                        <h4>Students</h4>
                        <p style="font-size: 24px; font-weight: bold;"><?= $students ?></p>
                    </div>
                    <div style="flex: 1; background-color: #f0f0f0; padding: 15px; border-radius: 5px;">
                        <h4>Lecturers</h4>
                        <p style="font-size: 24px; font-weight: bold;"><?= $lecturers ?></p>
                    </div>
                    <div style="flex: 1; background-color: #f0f0f0; padding: 15px; border-radius: 5px;">
                        <h4>Halls</h4>
                        <p style="font-size: 24px; font-weight: bold;"><?= $halls ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Persons Section -->
            <div id="persons-section" class="card hidden">
                <h3>Manage Persons</h3>
                <?php include 'persons.php'; ?>
            </div>
            
            <!-- Halls Section -->
            <div id="halls-section" class="card hidden">
                <h3>Manage Halls</h3>
                <p>Halls management content goes here.</p>
            </div>
            
            <!-- Reports Section -->
            <div id="reports-section" class="card hidden">
                <h3>Reports</h3>
                <p>Reports content goes here.</p>
            </div>
            
            <!-- Users Section -->
            <div id="users-section" class="card hidden">
                <h3>Manage Users</h3>
                <p>Users management content goes here.</p>
            </div>
            
            <!-- Profile Section -->
            <div id="profile-section" class="card hidden">
                <h3>My Profile</h3>
                <p>Profile information goes here.</p>
            </div>
        </div>
    </div>
    
    <script>
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.main-content .card').forEach(el => {
                el.classList.add('hidden');
            });
            
            // Show selected section
            document.getElementById(section + '-section').classList.remove('hidden');
        }
    </script>
</body>
</html>