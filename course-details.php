<?php
session_start();
include('database/connection.php');

// Store the referring page URL
$_SESSION['referrer'] = $_SERVER['HTTP_REFERER'];

try {
    // Handle GET request to fetch subject details
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $subjectId = trim($_GET['id']);
        error_log("Received subject ID: " . htmlspecialchars($subjectId, ENT_QUOTES, 'UTF-8')); // Debugging output

        // Prepare and execute SQL query to fetch subject details
        $stmt = $conn->prepare("SELECT * FROM Subjects WHERE SubjectID = :subjectId");
        $stmt->bindParam(':subjectId', $subjectId, PDO::PARAM_STR);
        $stmt->execute();
        $subject = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debugging output to check if subject was found
        if (!$subject) {
            error_log('No subject found for ID: ' . htmlspecialchars($subjectId, ENT_QUOTES, 'UTF-8'));
            throw new Exception('Subject not found.');
        }
    } else {
        error_log('Invalid subject ID: ' . (isset($_GET['id']) ? $_GET['id'] : 'Not set'));
        throw new Exception('Invalid subject ID.');
    }

    // Initialize enrollment status
    $enrollmentStatus = null;

    // Check if the user is logged in
    if (isset($_SESSION['Userid'])) {
        $userId = $_SESSION['Userid'];
        $semester = $subject['Sem'];

        // Check if the user is enrolled in this subject for the given semester
        $checkEnrollmentStmt = $conn->prepare("SELECT Status FROM Enrollments WHERE userid = :userid AND SubjectID = :subjectId AND Semester = :semester");
        $checkEnrollmentStmt->bindParam(':userid', $userId, PDO::PARAM_INT);
        $checkEnrollmentStmt->bindParam(':subjectId', $subjectId, PDO::PARAM_STR);
        $checkEnrollmentStmt->bindParam(':semester', $semester, PDO::PARAM_STR);
        $checkEnrollmentStmt->execute();

        if ($checkEnrollmentStmt->rowCount() > 0) {
            // If the user is already enrolled, fetch the status
            $enrollment = $checkEnrollmentStmt->fetch(PDO::FETCH_ASSOC);
            $enrollmentStatus = $enrollment['Status']; // Status from Enrollments table
        }
    }

    // Handle POST request to enroll in the course
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['courseid'])) {
            $courseId = $_POST['courseid'];

            // Check if the user is already enrolled
            if ($enrollmentStatus !== 'Enrolled') {
                // Prepare and execute SQL query to insert enrollment
                $insertStmt = $conn->prepare("INSERT INTO Enrollments (userid, SubjectID, Semester, Status) VALUES (:userid, :subjectId, :semester, 'Waiting')");
                $insertStmt->bindParam(':userid', $userId, PDO::PARAM_INT);
                $insertStmt->bindParam(':subjectId', $courseId, PDO::PARAM_STR);
                $insertStmt->bindParam(':semester', $semester, PDO::PARAM_STR);

                if ($insertStmt->execute()) {
                    // Redirect to the enrolled courses page or another confirmation page
                    header("Location: dashboard.php");
                    exit();
                } else {
                    throw new Exception('Failed to enroll in the subject. Please try again.');
                }
            } else {
                echo "You are already enrolled in this subject.";
            }
        }
    }
} catch (PDOException $e) {
    error_log('Query failed: ' . $e->getMessage());
    echo 'An error occurred while fetching subject details. Please try again later.';
    exit();
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit();
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
                        button[type="submit"], .enroll-button {
                        margin: 0;
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
                                    <span><?php echo htmlspecialchars($subject['SubjectID'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="subjectName">Subject Name:</label>
                                    <span><?php echo htmlspecialchars($subject['SubjectName'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="prerequisite">Prerequisite:</label>
                                    <span><?php echo htmlspecialchars($subject['Prerequisite'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="year">Year:</label>
                                    <span><?php echo htmlspecialchars($subject['StudyYear'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="sem">Semester:</label>
                                    <span><?php echo htmlspecialchars($subject['Sem'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="coursetype">Course Type:</label>
                                    <span><?php echo htmlspecialchars($subject['CourseType'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <div class="detail-item">
                    <label for="enrollmentStatus">Enrollment Status:</label>
                    <span>
                        <?php 
                        // Display enrollment status if available, otherwise fallback to the subject's enrollment status
                        echo htmlspecialchars($enrollmentStatus ? $enrollmentStatus : $subject['EnrollmentStatus'], ENT_QUOTES, 'UTF-8'); 
                        ?>
                    </span>
                </div>
                                <div class="detail-item">
                                    <label for="creditHours">Credit Hours:</label>
                                    <span><?php echo htmlspecialchars($subject['CreditHours'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="detail-item">
                                    <label for="description">Description:</label>
                                    <span><?php echo htmlspecialchars($subject['Description'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <?php if ($enrollmentStatus !== 'Enrolled' ||$enrollmentStatus !== 'Waiting' ): ?>
                                    <form action="" method="post" style="display: flex; gap: 10px;">
    <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($subject['SubjectID'], ENT_QUOTES, 'UTF-8'); ?>">
    <button type="submit" class="enroll-button">Enroll Now</button>
    <a href="enrol.php" class="enroll-button">Cancel</a>
</form>
<?php else: ?>
    <a href="<?php echo htmlspecialchars($_SESSION['referrer'], ENT_QUOTES, 'UTF-8'); ?>" class="enroll-button">Back</a>
<?php endif; ?>
                             </div>
                        </div>
                    </div>
                </body>
                </html>


       