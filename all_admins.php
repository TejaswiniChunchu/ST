<?php
session_start();

// Include the database connection file
include('database/connection.php');

// Initialize the search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch admins data from the users table where role is 'admin' and match search criteria
$sql = "SELECT id, username, password, role, firstname, lastname, email, contactnumber, address 
        FROM users 
        WHERE role = 'admin'
        AND (id LIKE :search 
        OR firstname LIKE :search 
        OR lastname LIKE :search 
        OR CONCAT(firstname, ' ', lastname) LIKE :search 
        OR email LIKE :search)";
$stmt = $conn->prepare($sql);
$stmt->execute(['search' => '%' . $search . '%']);
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Admins</title>
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
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-bar input[type="submit"] {
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar input[type="submit"]:hover {
            background-color: #e96852;
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
            <li><a href="enrollments.php">Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>All Admins</h1>
        <div class="search-bar">
            <form method="GET" action="all_admins.php">
                <input type="text" name="search" placeholder="Search by ID, name, or email" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Search">
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($admins) {
                    foreach ($admins as $admin) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['id']); ?></td>
                        <td><?php echo htmlspecialchars($admin['username']); ?></td>
                        <td><?php echo htmlspecialchars($admin['password']); ?></td>
                        <td><?php echo htmlspecialchars($admin['role']); ?></td>
                        <td><?php echo htmlspecialchars($admin['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($admin['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['contactnumber']); ?></td>
                        <td><?php echo htmlspecialchars($admin['address']); ?></td>
                    </tr>
                <?php } } else { ?>
                    <tr><td colspan="9">No admins found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
