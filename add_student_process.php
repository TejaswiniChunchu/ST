<?php
// Include the database connection file
include('database/connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $student_id = $_POST['student_id'];
    $student_name = $_POST['student_name'];
    $student_email = $_POST['student_email'];
    $student_major = $_POST['student_major'];

    // Insert the data into the database
    $sql = "INSERT INTO students (id, name, email, major) VALUES (:id, :name, :email, :major)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $student_id);
    $stmt->bindParam(':name', $student_name);
    $stmt->bindParam(':email', $student_email);
    $stmt->bindParam(':major', $student_major);

    if ($stmt->execute()) {
        // Redirect to all_students.php after successful insertion
        header("Location: all_students.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>