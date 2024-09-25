<?php
// Enable error reporting for development (disable or adjust in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include('database/connection.php');
session_start(); // Start the session if not already started

// Ensure the user is authenticated and MajorID1 or MajorID2 is set
if (!isset($_SESSION['Userid'])) {
    // Redirect to login if the user is not authenticated
    header('Location: login.php');
    exit();
}

// Retrieve the MajorIDs and other details from the session
$majorID1 = $_SESSION['MajorID1'] ?? null; 
$majorID2 = $_SESSION['MajorID2'] ?? null; 
$studentYear = $_SESSION['StudentYear'] ?? null;
$Department = $_SESSION['Department'] ?? null; 

if (empty($majorID1) && empty($majorID2)) {
    $message = "Choose major"; // Set the message
} else {
    $subjects = []; // Initialize an empty array for subjects

    try {
        // Fetch subjects based on MajorID1
        if ($majorID1 !== null) {
            $query1 = "
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description
                FROM Subjects s
                JOIN SubjectMajors sm ON s.SubjectID = sm.SubjectID
                WHERE sm.MajorID = :majorID1 
            ";

            $stmt1 = $conn->prepare($query1);
            $stmt1->bindParam(':majorID1', $majorID1, PDO::PARAM_INT);
            $stmt1->execute();
            $subjects1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            $subjects = array_merge($subjects, $subjects1);
        }

        // Fetch subjects based on MajorID2
        if ($majorID2 !== null) {
            $query2 = "
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description
                FROM Subjects s
                JOIN SubjectMajors sm ON s.SubjectID = sm.SubjectID
                WHERE sm.MajorID = :majorID2
            ";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':majorID2', $majorID2, PDO::PARAM_INT);
            $stmt2->execute();
            $subjects2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $subjects = array_merge($subjects, $subjects2);
        }

        // Fetch subjects based on Electives
        if ($Department !== null && $studentYear !== null) {
            $query3 = "
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description
                FROM Subjects s
                JOIN Electives e ON s.SubjectID = e.subjectID
                WHERE e.Department = :department AND e.StudentYear = :studentYear
            ";

            $stmt3 = $conn->prepare($query3);
            $stmt3->bindParam(':department', $Department, PDO::PARAM_STR);
            $stmt3->bindParam(':studentYear', $studentYear, PDO::PARAM_INT);
            $stmt3->execute();
            $subjects3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

            // Append Elective subjects to the main subjects array, marking them as "Elective"
            foreach ($subjects3 as $subject) {
                // Only add if the SubjectID is not already in the main subjects array
                if (!in_array($subject['SubjectID'], array_column($subjects, 'SubjectID'))) {
                    $subject['CourseType'] = 'Elective'; // Set course type as Elective
                    $subjects[] = $subject;
                }
            }
        }

        // Remove duplicates by SubjectID
        $uniqueSubjects = [];
        foreach ($subjects as $subject) {
            $uniqueSubjects[$subject['SubjectID']] = $subject;
        }
        // Resetting the subjects array to contain only unique subjects
        $subjects = array_values($uniqueSubjects);

    } catch (PDOException $e) {
        // Log error details to a file
        error_log('Database query failed: ' . $e->getMessage(), 3, 'logs/errors.log'); // Adjust path as needed
        // Display a user-friendly message
        echo 'An error occurred while fetching subjects. Please try again later.';
        exit(); // Stop further execution
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <style>
        /* Additional styling for the table */
        .table-container {
            margin-left: 250px; /* Adjust according to your sidebar width */
            padding: 20px;
            box-sizing: border-box;
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
            color: #7869B5; /* Change this color as needed */
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
            <li><a href="enrol.php">Enroll in Course</a></li>
            <li><a href="enrolled-courses.php">Enrolled Courses</a></li>
            <li><a href="edit-profile.php">Edit Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="table-container">
        <h1>Course List</h1>
        <table>
            <thead>
                <tr>
                    <th>Subject ID</th>
                    <th>Subject Name</th>
                    <th>Prerequisite</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Course Type</th>
                    <th>Enrollment Status</th>
                    <th>Credit Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="8">No subjects found. Select the Major in edit profile.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects as $subject): ?>
                        <!-- Check if the student's year matches the subject's study year -->
                        <?php if ($subject['StudyYear'] == $studentYear): ?>
                            <tr onclick="navigateToCourse('<?php echo addslashes($subject['SubjectID']); ?>', '<?php echo addslashes($subject['CourseType']); ?>')">
                                <td><?php echo htmlspecialchars($subject['SubjectID'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['SubjectName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['Prerequisite'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['StudyYear'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['Sem'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['CourseType'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['EnrollmentStatus'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($subject['CreditHours'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
      function navigateToCourse(courseid, courseType) {
    if (!courseid) {
        console.error('Invalid subject ID');
        return;
    }
    if (!courseType) {
        console.error('Invalid course type');
        return;
    }
    const url = `course-details.php?id=${encodeURIComponent(courseid)}&type=${encodeURIComponent(courseType)}`;
    window.location.href = url;
}

    </script>
</body>
</html>
