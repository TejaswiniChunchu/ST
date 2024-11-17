<?php
// Enable error reporting for development (disable or adjust in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display of errors to users

// Include the database connection file
include('database/connection.php');
session_start(); // Start the session if not already started

// Ensure the user is authenticated
if (!isset($_SESSION['Userid']) || !is_numeric($_SESSION['Userid'])) {
    // Redirect to login if the session does not have a valid user ID
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['Userid']; 
$majorID1 = $_SESSION['MajorID1'] ?? null; 
$majorID2 = $_SESSION['MajorID2'] ?? null; 
if (empty($majorID1) && empty($majorID2)) {
    $message = "Choose major"; // Set the message
} else {
    $subjectIDs = [];
    try {
        // Prepare the SQL query
        $query = "
            SELECT SubjectID, MajorID
            FROM SubjectMajors
            WHERE MajorID IN (:majorID1, :majorID2);
        ";
    
        $stmt4 = $conn->prepare($query);
    
        // Bind the parameters
        $stmt4->bindParam(':majorID1', $majorID1, PDO::PARAM_INT);
        $stmt4->bindParam(':majorID2', $majorID2, PDO::PARAM_INT);
    
        // Execute the statement
        $stmt4->execute();
    
        // Fetch all results
        $subjectIDs = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    
        // Extract SubjectID into $subjectIDs array
       
        
    
    } catch (PDOException $e) {
        // Log error details to a file
        error_log('Database query failed: ' . $e->getMessage(), 3, '/path/to/logs/errors.log'); // Adjust path as needed
        // Display a user-friendly message
        echo 'An error occurred while fetching subject IDs. Please try again later.';
        exit(); // Stop further execution
    }

    $waitingCourses = [];
try {
    // Prepare and execute a SQL query to fetch subjects for enrollments that are waiting
    $query = "
    SELECT 
        e.EnrollmentID, 
        e.userid, 
        e.SubjectID, 
        s.SubjectName, 
        s.Prerequisite, 
        s.StudyYear, 
        s.Sem, 
        s.CourseType, 
        e.Status, 
        s.CreditHours, 
        e.results,
        s.Description,
        s.credits 
    FROM 
        Enrollments e 
    INNER JOIN 
        Subjects s ON e.SubjectID = s.SubjectID 
    WHERE 
        e.userid = :userid
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userid', $userId, PDO::PARAM_INT); // Bind the user ID parameter
    $stmt->execute();

    // Fetch all results as an associative array
    $waitingCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error details to a file
    error_log('Database query failed: ' . $e->getMessage(), 3, '/path/to/logs/error.log'); // Adjust path as needed
    // Display a user-friendly message
    echo 'An error occurred while fetching waiting subjects. Please try again later.';
    exit(); // Stop further execution
}

foreach ($waitingCourses as &$subject) {
    // Assume the subject is elective by default
    $subject['CourseType'] = 'Elective'; // Default to Elective

    // Check if StudyYear is 1, then set CourseType to Major
    if ($subject['StudyYear'] === 1) {
        $subject['CourseType'] = 'Major'; // Set to Major if StudyYear is 1
    } else {
        // If not StudyYear 1, check against each subject ID
        foreach ($subjectIDs as $id) {
            // Compare the trimmed SubjectID with id (which is a string)
            if (trim($subject['SubjectID']) === trim($id['SubjectID'])) {
                $subject['CourseType'] = 'Major'; // Assign course type if match found
                break; // Exit the inner loop on first match
            }
        }
    }
}
}

// Cast to integer to ensure it's numeric

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll History & Course List</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <style>
        /* Additional styling for the table */
        .table-container {
            margin-left: 250px; /* Adjust according to your sidebar width */
            padding: 20px;
            box-sizing: border-box;
            position: relative; /* To position the print button absolutely */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #7869B5;
            color: white;
        }

        h1 {
            color: #e96852;
        }
        h2 {
            color: #7869B5;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e96852;
            cursor: pointer; /* Change cursor to pointer on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content -->
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="enrolled-courses.php">Enrolled Courses</a></li>
            <li><a href="edit-profile.php">Edit Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Enroll History Section -->
    <div class="table-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['userFirstname'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($_SESSION['userLastname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        
        <!-- Enroll History Table -->
        <h2>Enrolled Courses</h2>
        <table id="printTable">
        <thead>
            <tr>
                <th>Subject ID</th>
                <th>Subject Name</th>
                <th>Prerequisite</th>
                <th>Year</th>
                <th>Semester</th>
                <th>Course Type</th>
                <th>Enrollment Status</th>
                <th>Credit</th>
                <th>Result</th>
               
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($waitingCourses)) {
                echo "<tr><td colspan='8'>No courses found in Enroll History.</td></tr>";
            } else {
                foreach ($waitingCourses as $course) {
                    if($course['Status'] === 'Enrolled'){
                        echo "<tr onclick=\"navigateToCourse(
                            '" . addslashes($course['SubjectID']) . "', 
                            '" . addslashes($course['SubjectName']) . "', 
                            '" . addslashes($course['Prerequisite']) . "', 
                            '" . addslashes($course['StudyYear']) . "', 
                            '" . addslashes($course['Sem']) . "', 
                            '" . addslashes($course['CourseType']) . "', 
                            '" . addslashes($course['Status']) . "', 
                            '" . addslashes($course['Credit']) . "',
                            '" . addslashes($course['results']) . "',
                            '" . addslashes($course['Description']) . "'
                            )\">";
                        echo "<td>" . htmlspecialchars($course['SubjectID'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['SubjectName'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['Prerequisite'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['StudyYear'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['Sem'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['CourseType'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['Status'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($course['credits'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>";
                        if (trim($course['results']) === 'Pass') {
                            echo "Completed";
                        } else if (trim($course['results']) === 'Fail') {
                            echo "Incomplete";
                        }
                        echo "</td>";
                        echo "</tr>";

                    }
                   
                }
            }
            ?>
        </tbody>
    </table>
    </div>

    <script>
       function navigateToCourse(subjectId, subjectName, prerequisite, studyYear, semester, courseType, enrollmentStatus, creditHours, results, description) {
    const url = `course-details.php?id=${encodeURIComponent(subjectId)}
    &name=${encodeURIComponent(subjectName)}
    &prerequisite=${encodeURIComponent(prerequisite)}
    &year=${encodeURIComponent(studyYear)}
    &semester=${encodeURIComponent(semester)}
    &courseType=${encodeURIComponent(courseType)}
    &status=${encodeURIComponent(enrollmentStatus)}
    &creditHours=${encodeURIComponent(creditHours)}
    &results=${encodeURIComponent(results)}
    &description=${encodeURIComponent(description)}`;
    window.location.href = url;
}
    </script>
</body>
</html>
