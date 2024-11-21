<?php
include('database/connection.php');
session_start();

// Fetch accepted courses with updated results
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
        e.results,
        s.CreditHours, 
        s.Description,
        s.credits
    FROM 
        Enrollments e 
    INNER JOIN 
        Subjects s ON e.SubjectID = s.SubjectID 
    WHERE 
        e.userid = :userid AND e.Status = 'Accepted'
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $_SESSION['Userid'], PDO::PARAM_INT);
$stmt->execute();
$acceptedCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrolled Courses</title>
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
            background-color: #7869B5; /* Purple sidebar background */
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
        h1 {
            color: #7869B5; /* Purple heading */
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
            background-color: #7869B5; /* Purple column headers */
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e96852;
            cursor: pointer; /* Change cursor to pointer on hover */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="enrolled-courses.php">Enrolled Courses</a></li>
            <li><a href="edit-profile.php">Edit Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h1>Enrolled Courses</h1>
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
                    <th>Credits</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($acceptedCourses)): ?>
                    <tr>
                        <td colspan="9">No enrolled courses found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($acceptedCourses as $course): ?>
                        <tr onclick="navigateToCourse(
                            '<?php echo addslashes($course['SubjectID']); ?>', 
                            '<?php echo addslashes($course['SubjectName']); ?>', 
                            '<?php echo addslashes($course['Prerequisite']); ?>', 
                            '<?php echo addslashes($course['StudyYear']); ?>', 
                            '<?php echo addslashes($course['Sem']); ?>', 
                            '<?php echo addslashes($course['CourseType']); ?>', 
                            '<?php echo addslashes($course['Status']); ?>', 
                            '<?php echo addslashes($course['CreditHours']); ?>',
                            '<?php echo addslashes($course['results']); ?>',
                            '<?php echo addslashes($course['Description']); ?>'
                        )">
                            <td><?php echo htmlspecialchars($course['SubjectID'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['SubjectName'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['Prerequisite'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['StudyYear'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['Sem'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['CourseType'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['Status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['credits'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($course['results'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
