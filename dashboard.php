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

$userId = (int)$_SESSION['Userid']; // Cast to integer to ensure it's numeric



// Now we will include the earlier query to fetch the Major and Elective subjects

$majorID1 = $_SESSION['MajorID1'] ?? null; 
$majorID2 = $_SESSION['MajorID2'] ?? null; 
$studentYear = $_SESSION['StudentYear'] ?? null;
$Department = $_SESSION['Department'] ?? null;




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
    

$waitingCourses = []; // Initialize an empty array for waiting courses

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
        e.results,
        s.CreditHours, 
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


    $subjects = []; // Initialize an empty array for subjects

    try {

        $query11 = "
        SELECT 
            s.SubjectID, 
            s.SubjectName, 
            COALESCE(s.Prerequisite, '') AS Prerequisite, 
            s.StudyYear, 
            s.Sem, 
            s.CourseType, 
            s.EnrollmentStatus, 
            s.CreditHours, 
            s.Description,
            s.credits

        FROM 
            Subjects s 
        WHERE 
            s.StudyYear = 1
    ";

    // Prepare and execute the statement
    $stmt0 = $conn->prepare($query11);
    $stmt0->execute();

    // Fetch the results as an associative array
    $subjects0 = $stmt0->fetchAll(PDO::FETCH_ASSOC);

    // Assuming $subjects is already defined (e.g., $subjects = [];)
    $subjects = array_merge($subjects, $subjects0);
       
        // Fetch subjects based on MajorID1
        if ($majorID1 !== null) {
            $query1 = "
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description, s.credits
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
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description, s.credits
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
                SELECT s.SubjectID, s.SubjectName, COALESCE(s.Prerequisite, '') AS Prerequisite, s.StudyYear, s.Sem, s.CourseType, s.EnrollmentStatus, s.CreditHours, s.Description, s.credits
                FROM Subjects s
                JOIN Electives e ON s.SubjectID = e.SubjectID
                WHERE e.Department = :department
            ";

            $stmt3 = $conn->prepare($query3);
            $stmt3->bindParam(':department', $Department, PDO::PARAM_STR);
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
 

// Reassign the filtered array back to $waitingCourse
        // Remove duplicates by SubjectID
        $uniqueSubjects = [];
        foreach ($subjects as $subject) {
            $uniqueSubjects[$subject['SubjectID']] = $subject;
        }
        // Resetting the subjects array to contain only unique subjects
        $subjects = array_values($uniqueSubjects);
       // Create an associative array to map SubjectID to Status from waitingCourses
      
      
      
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
   
   

   
    foreach ($waitingCourses as $course) {
        // Check each subject for a match with the course
        foreach ($subjects as &$subject) { // Use reference (&) to modify the original subject array
            // Check if SubjectID matches (trim to avoid whitespace issues)
            if (trim($subject['SubjectID']) === trim($course['SubjectID'])) {
                // Check if results indicate "Fail" and update EnrollmentStatus accordingly
                if (isset($course['results']) && trim($course['results']) === 'Fail') {
                    $subject['EnrollmentStatus'] = 'Available';
                } else {
                    $subject['EnrollmentStatus'] = $course['Status'];
                }
                // Set the results for reference
                $subject['results'] = isset($course['results']) ? $course['results'] : '';
            }
        }
    }
    $Totalcredits = 0;
    $ResultI301 = "";

    foreach ($subjects as $subject) {
        if (trim($subject['results']) === 'Pass' && trim($subject['CourseType']) === 'Major'  &&  ($subject['StudyYear'] === 1 || $subject['StudyYear'] === 2)) {
            $Totalcredits += $subject['credits'];  // Assuming 'CreditHours' contains the credit value
        }
    }
      
      
      if($majorID1 === 8 || $majorID2=== 8){
        if($Totalcredits >= 195){
            foreach ($subjects as $subject) {
                if (trim($subject['results']) === 'Pass' && trim($subject['CourseType']) === 'Elective'  &&   ($subject['StudyYear'] === 2)) {
                    $Totalcredits += $subject['credits']; 
                    // Assuming 'CreditHours' contains the credit value
                }else if($subject['StudyYear'] === 3 ){
                    if($subject['SubjectID'] === 'I301'){
                    $ResultI301 = $subject['results'];
                    }
                    
                }
              
            }
            
        }
      }else   if($Totalcredits >= 180){
    
        foreach ($subjects as $subject) {
            if (trim($subject['results']) === 'Pass' && trim($subject['CourseType']) === 'Elective'  &&   ($subject['StudyYear'] === 2)) {
                $Totalcredits += $subject['credits']; 
                // Assuming 'CreditHours' contains the credit value
            }else if($subject['StudyYear'] === 3 ){
                if($subject['SubjectID'] === 'I301'){
                $ResultI301 = $subject['results'];
                }
                
            }
          
        }
       
    }

  
  echo $Totalcredits;
    $_SESSION['Totalcredits'] = $Totalcredits;
    $_SESSION['ResultI301'] = $ResultI301;



 

    } catch (PDOException $e) {
        // Log error details to a file
        error_log('Database query failed: ' . $e->getMessage(), 3, '/path/to/logs/errors.log'); // Adjust path as needed
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
    <title>Enroll History & Course List</title>
    <link rel="stylesheet" href="css/sidebar.css"> <!-- Adjust path if necessary -->
    <style>
        /* Styling to ensure vertical stacking of tables */
        .table-container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            display: block;
            clear: both; /* Ensure no float issues */
            margin-bottom: 20px; /* Adds spacing between the tables */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px; /* Space between tables */
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

        /* Additional styles to ensure block layout */
        .sidebar {
            float: left;
            width: 250px; /* Adjust based on your sidebar width */
            padding-top: 20px;
        }

        /* Ensure main content flows after the sidebar */
        .main-content {
            margin-left: 250px; /* Adjust based on your sidebar width */
            padding: 20px;
        }
        /* Print button styling and positioning */
        .print-button-container {
            position: absolute;
            top: 20px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            z-index: 1000; /* Ensure the button is above other content */
        }

        .print-button {
            padding: 10px 20px;
            background-color: #7869B5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #e96852; /* Hover background color */
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

    <div class="main-content">
    <div class="print-button-container">
            <button class="print-button" onclick="printTable()">Print</button>
        </div>
        <!-- Welcome Message -->
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['userFirstname'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($_SESSION['userLastname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>

        <!-- Enroll History Section -->
        <div class="table-container">
    <h2>Enroll History</h2>
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
            </tr>
        </thead>
        <tbody>
            <?php
            if (empty($waitingCourses)) {
                echo "<tr><td colspan='8'>No courses found in Enroll History.</td></tr>";
            } else {
                foreach ($waitingCourses as $course) {
                    if($course['Status'] === 'Waiting'){
                        echo "<tr onclick=\"navigateToCourse(
                            '" . addslashes($course['SubjectID']) . "', 
                            '" . addslashes($course['SubjectName']) . "', 
                            '" . addslashes($course['Prerequisite']) . "', 
                            '" . addslashes($course['StudyYear']) . "', 
                            '" . addslashes($course['Sem']) . "', 
                            '" . addslashes($course['CourseType']) . "', 
                            '" . addslashes($course['Status']) . "', 
                            '" . addslashes($course['CreditHours']) . "',
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
                        echo "</tr>";

                    }
                   
                }
            }
            ?>
        </tbody>
    </table>
</div>

        <!-- Course List Section -->
        <div class="table-container">
            <h2>Course List</h2>
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
                    </tr>
                </thead>
                <tbody>
            <?php 
           
            if (empty($subjects)): ?>
                <tr>
                    <td colspan="8">No subjects found. Select the Major in edit profile.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subjects as $subject): ?>
                   
                        <tr onclick="navigateToCourse(
                        '<?php echo addslashes($subject['SubjectID']); ?>', 
                        '<?php echo addslashes($subject['SubjectName']); ?>', 
                        '<?php echo addslashes($subject['Prerequisite']); ?>', 
                        '<?php echo addslashes($subject['StudyYear']); ?>', 
                        '<?php echo addslashes($subject['Sem']); ?>', 
                        '<?php echo addslashes($subject['CourseType']); ?>', 
                        '<?php echo addslashes($subject['EnrollmentStatus']); ?>', 
                        '<?php echo addslashes($subject['CreditHours']); ?>',
                        '<?php echo addslashes($subject['results']); ?>',
                        '<?php echo addslashes($subject['Description']); ?>'
                        )">
                            <td><?php echo htmlspecialchars($subject['SubjectID'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['SubjectName'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['Prerequisite'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['StudyYear'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['Sem'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['CourseType'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['EnrollmentStatus'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['results'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($subject['credits'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                   
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
            </table>
        </div>
    </div>

    <script>
  function navigateToCourse(subjectId, subjectName, prerequisite, studyYear, semester, courseType, enrollmentStatus, creditHours,results, description) {
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


        function printTable() {
            // Open a new window
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #7869B5; color: white; } tr:nth-child(even) { background-color: #f2f2f2; } tr:hover { background-color: #e96852; cursor: pointer; }</style>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(document.getElementById('printTable').outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close(); // necessary for IE >= 10
            printWindow.focus(); // necessary for IE >= 10

            // Print the contents of the new window
            printWindow.print();
        }
    </script>
</body>
</html>
