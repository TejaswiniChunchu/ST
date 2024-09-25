<?php
// Enable error reporting for development (disable or adjust in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display of errors to users

// Include the database connection file
include('database/connection.php');
session_start(); // Start the session if not already started

// Ensure the user is authenticated
if (!isset($_SESSION['Userid']) || !is_numeric($_SESSION['Userid'])) {
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['Userid']; // Cast to integer to ensure it's numeric

try {
    // Prepare and execute a SQL query to fetch user data for the logged-in user
    $query = "
        SELECT id, username, password, firstname, lastname, email, contactnumber, address, MajorName1, MajorName2 , StudentYear
        FROM users 
        WHERE id = :userid
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userid', $userId, PDO::PARAM_INT); // Bind the user ID parameter
    $stmt->execute();

    // Fetch the result as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch majors for the select box
    $majorsQuery = "SELECT MajorID, MajorName FROM Majors";
    $majorsStmt = $conn->prepare($majorsQuery);
    $majorsStmt->execute();
    $majors = $majorsStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update user data
        $username = $_POST['username'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $contactnumber = $_POST['contactnumber'];
        $address = $_POST['address'];
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password']; // Only hash if password is provided
        $majorName1 = $_POST['major1']; // Get selected MajorName1
        $majorName2 = $_POST['major2']; // Get selected MajorName2

        $update_query = "
            UPDATE users 
            SET username = :username, firstname = :firstname, lastname = :lastname, email = :email, contactnumber = :contactnumber, address = :address, password = :password, MajorName1 = :majorname1, MajorName2 = :majorname2 
            WHERE id = :userid
        ";
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $update_stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $update_stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $update_stmt->bindParam(':contactnumber', $contactnumber, PDO::PARAM_STR);
        $update_stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $update_stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $update_stmt->bindParam(':majorname1', $majorName1, PDO::PARAM_STR); // Update MajorName1
        $update_stmt->bindParam(':majorname2', $majorName2, PDO::PARAM_STR); // Update MajorName2
        $update_stmt->bindParam(':userid', $userId, PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            echo "Profile updated successfully!";
            // Refresh user data
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $_SESSION['Userid'] = $user['id'];
        $_SESSION['userFirstname'] = $user['firstname'];
        $_SESSION['userLastname'] = $user['lastname'];
        $_SESSION['user'] = $username;
        $_SESSION['MajorName1'] = $user['MajorName1'];
        $_SESSION['MajorName2'] = $user['MajorName2'];
        $_SESSION['StudentYear'] = $user['StudentYear'];
    
            // Initialize MajorID variables
            $_SESSION['MajorID1'] = null;
            $_SESSION['MajorID2'] = null;
    
            // Get MajorID for MajorName1 if it's not null
            if (!empty($user['MajorName1'])) {
                $queryMajor1 = 'SELECT MajorID, Department FROM Majors WHERE MajorName = :majorName1';
                $stmtMajor1 = $conn->prepare($queryMajor1);
                $stmtMajor1->bindParam(':majorName1', $user['MajorName1']);
                $stmtMajor1->execute();
    
                if ($stmtMajor1->rowCount() > 0) {
                    $major1 = $stmtMajor1->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['MajorID1'] = $major1['MajorID'];
                    $_SESSION['Department'] = $major1['Department'];
                }
            }
    
            // Get MajorID for MajorName2 if it's not null
            if (!empty($user['MajorName2'])) {
                $queryMajor2 = 'SELECT MajorID, Department FROM Majors WHERE MajorName = :majorName2';
                $stmtMajor2 = $conn->prepare($queryMajor2);
                $stmtMajor2->bindParam(':majorName2', $user['MajorName2']);
                $stmtMajor2->execute();
    
                if ($stmtMajor2->rowCount() > 0) {
                    $major2 = $stmtMajor2->fetch(PDO::FETCH_ASSOC);
                    $_SESSION['MajorID2'] = $major2['MajorID'];
                    $_SESSION['Department'] = $major1['Department'];
                }
            }



        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }

} catch (PDOException $e) {
    // Log error details to a file
    error_log('Database query failed: ' . $e->getMessage(), 3, 'logs/errors.log'); // Adjust path as needed
    echo 'An error occurred while fetching user data. Please try again later.';
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <style>
        .form-container {
            margin-left: 250px; /* Adjust according to your sidebar width */
            padding: 20px;
            box-sizing: border-box;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label, input, select {
            margin: 10px 0;
        }

        input[type="submit"] {
            width: 100px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="enrol.php">Enroll in Course</a></li>
            <li><a href="enrolled-courses.php">Enrolled Courses</a></li>
            <li><a href="edit-profile.php">Edit Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="form-container">
        <h1>Edit Profile</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="contactnumber">Contact Number:</label>
            <input type="text" name="contactnumber" value="<?php echo htmlspecialchars($user['contactnumber'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="address">Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">

            <label for="major1">Major 1:</label>
            <?php if (!empty($user['MajorName1'])): ?>
                <input type="text" name="major1" value="<?php echo htmlspecialchars($user['MajorName1'], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
            <?php else: ?>
                <select name="major1" >
                    <option value="">Select Major</option>
                    <?php foreach ($majors as $major): ?>
                        <option value="<?php echo htmlspecialchars($major['MajorName'], ENT_QUOTES, 'UTF-8'); ?>" 
                        <?php if (trim($user['MajorName1']) === trim($major['MajorName'])) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($major['MajorName'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <label for="major2">Major 2:</label>
<?php if (!empty($user['MajorName2'])): ?>
    <input type="text" name="major2" value="<?php echo htmlspecialchars($user['MajorName2'], ENT_QUOTES, 'UTF-8'); ?>" required readonly>
<?php else: ?>
    <select name="major2" >
        <option value="">Select Major</option>
        <?php foreach ($majors as $major): ?>
            <option value="<?php echo htmlspecialchars($major['MajorName'], ENT_QUOTES, 'UTF-8'); ?>" 
            <?php if (trim($user['MajorName2']) === trim($major['MajorName'])) echo 'selected'; ?>
            <?php if (trim($user['MajorName1']) === trim($major['MajorName'])) echo 'disabled'; ?>>
                <?php echo htmlspecialchars($major['MajorName'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

            <input type="submit" value="Update Profile">
        </form>
    </div>
</body>
</html>
