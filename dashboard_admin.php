<?php
// Enable error reporting for development (disable or adjust in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display of errors to users

// Include the database connection file
include('database/connection.php');
session_start(); // Start the session if not already started

// Check if the user is logged in as an admin (you can adjust this condition based on your actual admin check)
$isAdmin = isset($_SESSION['UserRole']) && $_SESSION['UserRole'] === 'admin';

// Redirect non-admin users
if (!$isAdmin) {
    header("Location: dashboard.php"); // Redirect to user dashboard or login page
    exit();
}

// Fetch total number of pending requests
$total_requests = 0;
if ($conn) {
    $sql_pending_requests = "SELECT COUNT(*) AS total_requests FROM Enrollments WHERE Status = 'waiting'";
    $stmt_pending_requests = $conn->prepare($sql_pending_requests);
    if ($stmt_pending_requests->execute()) {
        $result = $stmt_pending_requests->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $total_requests = $result['total_requests'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <!-- Additional styles specific to the admin dashboard -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9; /* Light gray background for admin */
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50; /* Darker sidebar color for admin */
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
        .stats {
            display: flex;
            justify-content: flex-start; /* Align items to the start */
            margin-top: 10px; /* Reduce the top margin to bring it closer to the heading */
        }
        .stat-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 200px;
            margin-left: 0; /* Align with the heading */
        }
        .stat-box h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 2em;
            color: #2c3e50;
        }
        .stat-box:nth-child(1) {
            background-color: #3498db; /* Blue for Pending Requests */
            color: white;
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
            <li><a href="add_admins.php">Add Admins</a></li>
            <li><a href="all_admins.php">All Admins</a></li>
            <li><a href="all_users.php">All Users</a></li>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="enrollments.php">Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Dashboard Statistics Overview</h1>
        <div class="stats">
            <div class="stat-box">
                <h2>Total Requests</h2>
                <p><?php echo htmlspecialchars($total_requests); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
