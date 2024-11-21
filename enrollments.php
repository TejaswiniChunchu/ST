<?php
include('database/connection.php');
session_start();

// Fetch enrollment data
$query = "SELECT * FROM Enrollments";
$stmt = $conn->prepare($query);
$stmt->execute();
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the form submission for accepting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'], $_POST['result'], $_POST['accept'])) {
    $enrollmentId = (int)$_POST['enrollment_id'];
    $result = $_POST['result'];

    // Update the result in the database
    $updateQuery = "UPDATE Enrollments SET results = :result WHERE EnrollmentID = :enrollment_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':result', $result, PDO::PARAM_STR);
    $updateStmt->bindParam(':enrollment_id', $enrollmentId, PDO::PARAM_INT);
    $updateStmt->execute();

    // Redirect to avoid form resubmission
    header('Location: enrollments.php');
    exit();
}

// Handle the form submission for declining
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id'], $_POST['decline'])) {
    $enrollmentId = (int)$_POST['enrollment_id'];

    // Delete the enrollment from the database
    $deleteQuery = "DELETE FROM Enrollments WHERE EnrollmentID = :enrollment_id";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':enrollment_id', $enrollmentId, PDO::PARAM_INT);
    $deleteStmt->execute();

    // Redirect to avoid form resubmission
    header('Location: enrollments.php');
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
    </style>
</head>
<body>
<div class="sidebar">
        <!-- Sidebar content -->
        <ul>
        <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="add_students.php">Add Students</a></li>
            <li><a href="all_students.php">All Students</a></li>
            <li><a href="all_users.php">All Users</a></li>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="enrollments.php">Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
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
                        <td><?php echo htmlspecialchars($enrollment['EnrollmentID'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['userid'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['SubjectID'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['Semester'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($enrollment['Status'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form method="post" action="enrollments.php">
                                <input type="text" name="result" value="<?php echo htmlspecialchars($enrollment['results'], ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <td>
                                <input type="hidden" name="enrollment_id" value="<?php echo htmlspecialchars($enrollment['EnrollmentID'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="accept">Accept</button>
                            </form>
                            <form method="post" action="enrollments.php" style="display:inline;">
                                <input type="hidden" name="enrollment_id" value="<?php echo htmlspecialchars($enrollment['EnrollmentID'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="decline">Decline</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
