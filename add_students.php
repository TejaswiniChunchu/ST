<?php
session_start(); // Start the session if not already started

// Include the database connection file
include('database/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $student_id = $_POST['student_id'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Save password as plain text
    $role = $_POST['role'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $contactnumber = $_POST['contactnumber'];
    $address = $_POST['address'];
    $StudentYear = $_POST['StudentYear'];

    // Insert the new student into the users table in the database
    $sql = "INSERT INTO users (id, username, password, role, firstname, lastname, email, contactnumber, address, StudentYear) 
            VALUES (:id, :username, :password, :role, :firstname, :lastname, :email, :contactnumber, :address, :StudentYear)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $student_id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); // Save password as plain text
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contactnumber', $contactnumber);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':StudentYear', $StudentYear);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the all_students.php to avoid resubmission
        header("Location: all_students.php");
        exit();
    } else {
        // Handle errors
        echo "Error adding student. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Students</title>
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
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-top: 0;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container input[type="text"],
        .form-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            background-color: #4d4185;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
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
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="enrollments.php">Enrollments</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Add Students</h2>
            <form action="add_students.php" method="post">
                <label for="student_id">ID:</label>
                <input type="text" id="student_id" name="student_id" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="text" id="password" name="password" required>

                <label for="role">Role:</label>
                <input type="text" id="role" name="role" required>

                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>

                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="contactnumber">Contact Number:</label>
                <input type="text" id="contactnumber" name="contactnumber" required>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>

                <label for="StudentYear">Student Year:</label>
                <input type="text" id="StudentYear" name="StudentYear" required>

                <input type="submit" value="Add Student">
            </form>
        </div>
    </div>
</body>
</html>
