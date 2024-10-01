<?php
session_start();
include('database/connection.php');

// Store the referring page URL
$_SESSION['referrer'] = $_SERVER['HTTP_REFERER'];

// Initialize variables
$courseId = $courseName = $prerequisite = $studyYear = $semester = $courseType = $enrollmentStatus = $creditHours = null;

if (isset($_GET['id'], $_GET['name'], $_GET['prerequisite'], $_GET['year'], $_GET['semester'], $_GET['courseType'], $_GET['status'], $_GET['creditHours'], $_GET['description'])) {
    $courseId = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
    $courseName = htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');
    $prerequisite = htmlspecialchars($_GET['prerequisite'], ENT_QUOTES, 'UTF-8');
    $studyYear = htmlspecialchars($_GET['year'], ENT_QUOTES, 'UTF-8');
    $semester = htmlspecialchars($_GET['semester'], ENT_QUOTES, 'UTF-8');
    $courseType = htmlspecialchars($_GET['courseType'], ENT_QUOTES, 'UTF-8');
    $enrollmentStatus = trim(htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8'));
    $creditHours = htmlspecialchars($_GET['creditHours'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_GET['description'], ENT_QUOTES, 'UTF-8');
} else {
    echo "No course details available.";
    exit; // Stop execution if the required data is not present
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['Userid'])) {
        $userId = $_SESSION['Userid'];

        // Prepare SQL statement to insert enrollment
        $insertStmt = $conn->prepare("INSERT INTO Enrollments (userid, SubjectID, Semester, Status) VALUES (:userid, :subjectId, :semester, 'Waiting')");
        $insertStmt->bindParam(':userid', $userId, PDO::PARAM_INT);
        $insertStmt->bindParam(':subjectId', $courseId, PDO::PARAM_STR); // Assuming SubjectID is a string
        $insertStmt->bindParam(':semester', $semester, PDO::PARAM_STR); // Use semester from GET parameters

        if ($insertStmt->execute()) {
            // Redirect to the enrolled courses page or another confirmation page
            header("Location: dashboard.php");
            exit();
        } else {
            throw new Exception('Failed to enroll in the subject. Please try again.');
        }
    } else {
        echo "User is not logged in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Details</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <style>
        .container {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #7869B5;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
        }
        .main-content {
            margin-left: 250px; /* Adjust according to your sidebar width */
            padding: 20px;
            box-sizing: border-box;
            flex: 1;
        }
        .details-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            color: #7869B5;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-item label {
            font-weight: bold;
        }
        .enroll-button {
            background-color: #7869B5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .enroll-button:hover {
            background-color: #e96852;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="enrol.php">Enroll in Course</a></li>
                <li><a href="enrolled-courses.php">Enrolled Courses</a></li>
                <li><a href="edit-profile.php">Edit Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h1>Subject Details</h1>
            <div class="details-container">
                <div class="detail-item">
                    <label for="subjectid">Subject ID:</label>
                    <span><?php echo $courseId; ?></span>
                </div>
                <div class="detail-item">
                    <label for="subjectName">Subject Name:</label>
                    <span><?php echo $courseName; ?></span>
                </div>
                <div class="detail-item">
                    <label for="prerequisite">Prerequisite:</label>
                    <span><?php echo $prerequisite; ?></span>
                </div>
                <div class="detail-item">
                    <label for="year">Year:</label>
                    <span><?php echo $studyYear; ?></span>
                </div>
                <div class="detail-item">
                    <label for="sem">Semester:</label>
                    <span><?php echo $semester; ?></span>
                </div>
                <div class="detail-item">
                    <label for="coursetype">Course Type:</label>
                    <span><?php echo $courseType; ?></span>
                </div>
                <div class="detail-item">
                    <label for="enrollmentStatus">Enrollment Status:</label>
                    <span><?php echo $enrollmentStatus; ?></span>
                </div>
                <div class="detail-item">
                    <label for="creditHours">Credit Hours:</label>
                    <span><?php echo $creditHours; ?></span>
                </div>
                <div class="detail-item">
                    <label for="creditHours">Description:</label>
                    <span><?php echo $description; ?></span>
                </div>
                <?php
                // Check enrollment status
                if (strcasecmp($enrollmentStatus, 'Available') === 0): // Case-insensitive comparison
                ?>
                    <form action="" method="post">
                        <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($courseId, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="enroll-button">Enroll Now</button>
                        <a href="dashboard.php" class="enroll-button">Cancel</a>
                    </form>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($_SESSION['referrer'], ENT_QUOTES, 'UTF-8'); ?>" class="enroll-button">Back</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
