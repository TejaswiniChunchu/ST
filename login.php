<?php
session_start(); // Start the session

include('database/connection.php'); // Include the database connection

if ($_POST) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Modify the query to select MajorName1 and MajorName2
    $query = 'SELECT id, firstname, lastname, MajorName1, MajorName2, StudentYear FROM users WHERE username = :username AND password = :password';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); // This should be hashed for security
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Store user data in session
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

        // Redirect based on the username content
        if (stripos($username, 'admin') !== false) {
            header('Location: dashboard_admin.php');
        } else {
            header('Location: dashboard.php');
        }
        exit();
    } else {
        $error_message = 'Username or Password is incorrect.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<h1 class="app-name">Student Enrollment</h1>
<div class="login-container">
    <h2>Login</h2>
    <?php
    if (!empty($error_message)) {
        echo '<p class="message">' . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    ?>
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
