<?php
include('database/connection.php');
session_start();

// Read results from results.txt
$results = file('results.txt', FILE_IGNORE_NEW_LINES);
$currentResultIndex = isset($_SESSION['currentResultIndex']) ? $_SESSION['currentResultIndex'] : 0;

// Fetch enrollment data from the database
$query = "SELECT * FROM Enrollments";
$stmt = $conn->prepare($query);
$stmt->execute();
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the form submission for updating result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'], $_POST['result'], $_POST['submit'])) {
    $enrollmentId = (int)$_POST['enrollment_id'];
    $result = $_POST['result'];

    // Update the result in the database
    $updateQuery = "UPDATE Enrollments SET results = :result WHERE EnrollmentID = :enrollment_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':result', $result, PDO::PARAM_STR);
    $updateStmt->bindParam(':enrollment_id', $enrollmentId, PDO::PARAM_INT);
    $updateStmt->execute();

    // Refresh the current page
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

// Handle the form submission for accepting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'], $_POST['accept'])) {
    $enrollmentId = (int)$_POST['enrollment_id'];

    // Update the status in the database
    $updateQuery = "UPDATE Enrollments SET Status = 'Enrolled' WHERE EnrollmentID = :enrollment_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':enrollment_id', $enrollmentId, PDO::PARAM_INT);
    $updateStmt->execute();

    // Refresh the current page
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 20px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
        }
        .sidebar ul li a:hover {
            background-color: #e96852;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            box-sizing: border-box;
            flex-grow: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #2c3e50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        /* Add this CSS rule to remove underlines from links */
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="add_students.php">Add Students</a></li>
            <li><a href="add_admins.php">Add Admins</a></li>
            <li><a href="all_users.php">All Users</a></li>
            <li><a href="enrollments.php">Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Enrollments</h1>
        <table>
            <thead>
                <tr>
                    <th>EnrollmentID</th>
                    <th>User ID</th>
                    <th>Subject ID</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Results</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enrollments as $enrollment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($enrollment['EnrollmentID'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['userid'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['SubjectID'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['Semester'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['Status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form method="post" action="enrollments.php">
                                <input type="text" name="result" value="<?php 
                                if ($currentResultIndex < count($results)) {
                                    echo htmlspecialchars($results[$currentResultIndex++], ENT_QUOTES, 'UTF-8');
                                    $_SESSION['currentResultIndex'] = $currentResultIndex;
                                } else {
                                    echo htmlspecialchars($enrollment['results'] ?? '', ENT_QUOTES, 'UTF-8');
                                } ?>" required>
                                <input type="hidden" name="enrollment_id" value="<?php echo htmlspecialchars($enrollment['EnrollmentID'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="submit">Submit</button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="enrollments.php" style="display:inline;">
                                <input type="hidden" name="enrollment_id" value="<?php echo htmlspecialchars($enrollment['EnrollmentID'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="accept">Accept</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>