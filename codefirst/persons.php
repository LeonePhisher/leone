<?php
// Display success/error messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="error">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
require_once 'config.php';

if (!isLoggedIn() || (!hasRole('admin') && !hasRole('lecturer'))) {
    redirect('login.php');
}

$conn = getDBConnection();

// Handle CSV import
if (isset($_POST['import_csv'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['csv_file']['tmp_name'];
        importStudentsFromCSV($tmpName);
        $message = "CSV imported successfully!";
    } else {
        $error = "Error uploading file";
    }
}

// Handle search
$searchResults = [];
if (isset($_GET['search'])) {
    $searchTerm = "%{$_GET['search']}%";
    $positionFilter = $_GET['position'] ?? '';
    $classFilter = $_GET['class'] ?? '';
    
    $sql = "SELECT * FROM Persons WHERE 
            (first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
    
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = "sss";
    
    if (!empty($positionFilter)) {
        $sql .= " AND position = ?";
        $params[] = $positionFilter;
        $types .= "s";
    }
    
    if (!empty($classFilter)) {
        $sql .= " AND class LIKE ?";
        $params[] = "%$classFilter%";
        $types .= "s";
    }
    
    $sql .= " ORDER BY last_name, first_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $searchResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get unique classes for filter
$classes = [];
$result = $conn->query("SELECT DISTINCT class FROM Persons WHERE class IS NOT NULL ORDER BY class");
while ($row = $result->fetch_assoc()) {
    $classes[] = $row['class'];
}

$conn->close();
?>

<div class="persons-management">
    <div class="actions">
        <button onclick="showImportForm()">Import CSV</button>
        <button onclick="showAddForm()">Add New Person</button>
    </div>
    
    <!-- CSV Import Form -->
    <div id="import-form" style="display: none; margin: 20px 0; padding: 15px; background: #f5f5f5;">
        <h4>Import CSV File</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit" name="import_csv">Import</button>
            <button type="button" onclick="hideImportForm()">Cancel</button>
        </form>
    </div>
    
    <!-- Add New Person Form -->
    <div id="add-form" style="display: none; margin: 20px 0; padding: 15px; background: #f5f5f5;">
        <h4>Add New Person</h4>
        <form method="POST" action="add_person.php">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div>
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                <div>
                    <label>Gender:</label>
                    <select name="gender">
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div>
                    <label>Position:</label>
                    <select name="position">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <?php if (hasRole('admin')): ?>
                            <option value="administrator">Administrator</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label>Birth Date:</label>
                    <input type="date" name="birth_date">
                </div>
                <div>
                    <label>Birth Town:</label>
                    <input type="text" name="birth_town">
                </div>
                <div>
                    <label>Street:</label>
                    <input type="text" name="street">
                </div>
                <div>
                    <label>Hometown:</label>
                    <input type="text" name="hometown">
                </div>
                <div>
                    <label>Class:</label>
                    <input type="text" name="class">
                </div>
                <div>
                    <label>Start Date:</label>
                    <input type="date" name="start_date">
                </div>
                <div>
                    <label>Password:</label>
                    <input type="text" name="password" required>
                </div>
            </div>
            <div style="margin-top: 15px;">
                <button type="submit">Save</button>
                <button type="button" onclick="hideAddForm()">Cancel</button>
            </div>
        </form>
    </div>
    
    <!-- Search Form -->
    <div style="margin: 20px 0;">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by name..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            
            <select name="position">
                <option value="">All Positions</option>
                <option value="student" <?= ($_GET['position'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                <option value="lecturer" <?= ($_GET['position'] ?? '') === 'lecturer' ? 'selected' : '' ?>>Lecturer</option>
                <option value="administrator" <?= ($_GET['position'] ?? '') === 'administrator' ? 'selected' : '' ?>>Administrator</option>
            </select>
            
            <select name="class">
                <option value="">All Classes</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class) ?>" 
                        <?= ($_GET['class'] ?? '') === $class ? 'selected' : '' ?>>
                        <?= htmlspecialchars($class) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Search</button>
            <button type="button" onclick="location.href='persons.php'">Reset</button>
        </form>
    </div>
    
    <!-- Search Results -->
    <?php if (!empty($_GET['search']) || !empty($_GET['position']) || !empty($_GET['class'])): ?>
        <h4>Search Results</h4>
        
        <?php if (empty($searchResults)): ?>
            <p>No results found.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
                <thead>
                    <tr style="background-color: <?= PRIMARY_COLOR ?>; color: white;">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Class</th>
                        <th>Birth Date</th>
                        <th>Hometown</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $person): ?>
                        <tr>
                            <td><?= htmlspecialchars($person['person_id']) ?></td>
                            <td><?= htmlspecialchars($person['first_name'] . ' ' . $person['last_name']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($person['position'])) ?></td>
                            <td><?= htmlspecialchars($person['class']) ?></td>
                            <td><?= htmlspecialchars($person['birth_date']) ?></td>
                            <td><?= htmlspecialchars($person['hometown']) ?></td>
                            <td>
                                <a href="edit_person.php?id=<?= $person['person_id'] ?>">Edit</a>
                                <?php if (hasRole('admin')): ?>
                                    | <a href="delete_person.php?id=<?= $person['person_id'] ?>" 
                                       onclick="return confirm('Are you sure?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    function showImportForm() {
        document.getElementById('import-form').style.display = 'block';
        document.getElementById('add-form').style.display = 'none';
    }
    
    function hideImportForm() {
        document.getElementById('import-form').style.display = 'none';
    }
    
    function showAddForm() {
        document.getElementById('add-form').style.display = 'block';
        document.getElementById('import-form').style.display = 'none';
    }
    
    function hideAddForm() {
        document.getElementById('add-form').style.display = 'none';
    }
</script>
<style>
.success {
    color: green;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #e6ffe6;
    border: 1px solid #99ff99;
    border-radius: 4px;
}

.error {
    color: red;
    margin-bottom: 15px;
    padding: 10px;
    background-color: #ffebeb;
    border: 1px solid #ff9999;
    border-radius: 4px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th {
    background-color: <?= PRIMARY_COLOR ?>;
    color: white;
    padding: 10px;
    text-align: left;
}

table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #e6e6e6;
}

.actions {
    margin-bottom: 20px;
}

.actions button {
    background-color: <?= SECONDARY_COLOR ?>;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10px;
}

.actions button:hover {
    opacity: 0.9;
}
    </style>