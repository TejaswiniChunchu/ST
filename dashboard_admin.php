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

// Fetch total number of students
$sql_total_students = "SELECT COUNT(*) AS total_students FROM students";
$stmt_total_students = $conn->prepare($sql_total_students);
$stmt_total_students->execute();
$total_students = $stmt_total_students->fetch(PDO::FETCH_ASSOC)['total_students'];

// Fetch number of new students (assuming new students are those added in the last 30 days)
$sql_new_students = "SELECT COUNT(*) AS new_students FROM students WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
$stmt_new_students = $conn->prepare($sql_new_students);
$stmt_new_students->execute();
$new_students = $stmt_new_students->fetch(PDO::FETCH_ASSOC)['new_students'];

// Fetch total number of admins
$sql_total_admins = "SELECT COUNT(*) AS total_admins FROM admins";
$stmt_total_admins = $conn->prepare($sql_total_admins);
$stmt_total_admins->execute();
$total_admins = $stmt_total_admins->fetch(PDO::FETCH_ASSOC)['total_admins'];

// Calculate total users (students + admins)
$total_users = $total_students + $total_admins;

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
        /* ... (Your existing styles) ... */

        /* Additional styles for the admin dashboard */
        <?php if ($isAdmin): ?>
            body {
                background-color: #f9f9f9; /* Light gray background for admin */
            }
            .sidebar {
                background-color: #2c3e50; /* Darker sidebar color for admin */
                color: white;
            }
            /* Add more admin-specific styles as needed */
        <?php endif; ?>

        /* Styles for the cards */
        .stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .stat-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 200px;
            margin: 10px;
        }
        .stat-box h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 2em;
            color: #2c3e50;
        }

        /* Colors for each card */
        .stat-box:nth-child(1) {
            background-color: #3498db; /* Blue for Total Students */
            color: white;
        }
        .stat-box:nth-child(2) {
            background-color: #2ecc71; /* Green for New Students */
            color: white;
        }
        .stat-box:nth-child(3) {
            background-color: #e74c3c; /* Red for Total Admins */
            color: white;
        }
        .stat-box:nth-child(4) {
            background-color: #f1c40f; /* Yellow for Total Users */
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
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="other_profiles.php">Other Profiles</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Dashboard Statistics Overview</h1>
        <div class="stats">
            <div class="stat-box">
                <h2>Total Students</h2>
                <p><?php echo $total_students; ?></p>
            </div>
            <div class="stat-box">
                <h2>New Students</h2>
                <p><?php echo $new_students; ?></p>
            </div>
            <div class="stat-box">
                <h2>Total Admins</h2>
                <p><?php echo $total_admins; ?></p>
            </div>
            <div class="stat-box">
                <h2>Total Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
