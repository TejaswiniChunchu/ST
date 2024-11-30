<?php
session_start();
include('database/connection.php');

$filename = 'students.txt';

function readStudentsFile($filename) {
    $students = [];
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        while (($line = fgetcsv($file)) !== FALSE) {
            $students[] = $line;
        }
        fclose($file);
    }
    return $students;
}

$currentIndex = isset($_SESSION['current_index']) ? $_SESSION['current_index'] : 0;
$students = readStudentsFile($filename);

if ($currentIndex >= count($students)) {
    $currentStudent = array_fill(0, 10, ''); // Adjust for the number of columns in your CSV
} else {
    $currentStudent = $students[$currentIndex];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Students</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; display: flex; min-height: 100vh; margin: 0; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; padding: 20px; box-sizing: border-box; position: fixed; top: 0; left: 0; height: 100%; }
        .sidebar ul { list-style-type: none; padding: 0; }
        .sidebar ul li { margin: 20px 0; }
        .sidebar ul li a { color: white; text-decoration: none; font-size: 18px; display: block; padding: 10px; }
        .sidebar ul li a:hover { background-color: #e96852; }
        .main-content { margin-left: 250px; padding: 20px; box-sizing: border-box; flex-grow: 1; }
        .form-container { background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1); }
        .form-container h2 { margin-top: 0; }
        .form-container label { display: block; margin: 10px 0 5px; }
        .form-container input[type="text"], .form-container input[type="email"] { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-container button[type="submit"] { background-color: #4d4185; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .form-container button[type="submit"]:hover { background-color: #e96852; }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                console.log('Form data:', formData); // Log the form data
                $.ajax({
                    url: 'add_student_process.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Response:', response); // Log the server response
                        if (response.success) {
                            $('form')[0].reset();

                            // Update fields with the next row of data from students.txt
                            var currentIndex = <?php echo $currentIndex; ?> + 1;
                            var students = <?php echo json_encode($students); ?>;
                            if (currentIndex < students.length) {
                                var nextStudent = students[currentIndex];
                                $('input[name="student_id"]').val(nextStudent[0]);
                                $('input[name="username"]').val(nextStudent[1]);
                                $('input[name="password"]').val(nextStudent[2]);
                                $('input[name="role"]').val(nextStudent[3]);
                                $('input[name="firstname"]').val(nextStudent[4]);
                                $('input[name="lastname"]').val(nextStudent[5]);
                                $('input[name="admin_email"]').val(nextStudent[6]);
                                $('input[name="contactnumber"]').val(nextStudent[7]);
                                $('input[name="address"]').val(nextStudent[8]);
                                $('input[name="studentyear"]').val(nextStudent[9]);

                                // Update session index
                                $.ajax({
                                    url: 'update_index.php',
                                    method: 'POST',
                                    data: { current_index: currentIndex },
                                    success: function(response) {
                                        console.log('Index updated:', response);
                                    }
                                });
                            } else {
                                // Clear form fields for manual entry
                                $('input[name="student_id"]').val('');
                                $('input[name="username"]').val('');
                                $('input[name="password"]').val('');
                                $('input[name="role"]').val('');
                                $('input[name="firstname"]').val('');
                                $('input[name="lastname"]').val('');
                                $('input[name="admin_email"]').val('');
                                $('input[name="contactnumber"]').val('');
                                $('input[name="address"]').val('');
                                $('input[name="studentyear"]').val('');
                            }

                            // Fetch updated data for all_users.php
                            fetchStudents();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error); // Log AJAX errors
                    }
                });
            });

            function fetchStudents() {
                $.ajax({
                    url: 'fetch_students.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var tableRows = '';
                        data.forEach(function(student) {
                            tableRows += `
                                <tr>
                                    <td>${student.id}</td>
                                    <td>${student.username}</td>
                                    <td>${student.password}</td>
                                    <td>${student.role}</td>
                                    <td>${student.firstname}</td>
                                    <td>${student.lastname}</td>
                                    <td>${student.email}</td>
                                    <td>${student.contactnumber}</td>
                                    <td>${student.address}</td>
                                    <td>${student.StudentYear}</td>
                                </tr>
                            `;
                        });
                        $('#students-table tbody').html(tableRows);
                    },
                    error: function(xhr, status, error) {
                        console.error('Fetch Students AJAX Error:', error); // Log AJAX fetch errors
                    }
                });
            }
        });
    </script>
</head>
<body>
    <div class="sidebar">
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
        <div class="form-container">
            <h2>Add Students</h2>
            <form>
                <label for="student_id">ID:</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($currentStudent[0]); ?>" required>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentStudent[1]); ?>" required>
                <label for="password">Password:</label>
                <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($currentStudent[2]); ?>" required>
                <label for="role">Role:</label>
                <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($currentStudent[3]); ?>" required>
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($currentStudent[4]); ?>" required>
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($currentStudent[5]); ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentStudent[6]); ?>" required>
                <label for="contactnumber">Contact Number:</label>
                <input type="text" id="contactnumber" name="contactnumber" value="<?php echo htmlspecialchars($currentStudent[7]); ?>" required>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($currentStudent[8]); ?>" required>
                <label for="studentyear">Student Year:</label>
                <input type="text" id="studentyear" name="studentyear" value="<?php echo htmlspecialchars($currentStudent[9]); ?>" required>
                <button type="submit" name="add_student">Add Student</button>
            </form>
        </div>
    </div>
</body>
</html>
