<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
            <li><a href="add_admins.php">Add Admins</a></li>
            <li><a href="all_admins.php">All Admins</a></li>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="other_profiles.php">Other Profiles</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>My Profile</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include the database connection file
                include('database/connection.php');

                // Fetch the profile data for "Admin User"
                $sql = "SELECT id, name, email, contactnumber, address FROM profiles WHERE name = 'Admin User'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);

                // Display the profile data
                if ($profile) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($profile['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($profile['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($profile['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($profile['contactnumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($profile['address']) . "</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='5'>No profile found for Admin User.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
