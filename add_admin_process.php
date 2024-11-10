<?php
// Include the database connection file
include('database/connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required fields are set
    if (isset($_POST['admin_id']) && isset($_POST['admin_name']) && isset($_POST['admin_email'])) {
        $admin_id = $_POST['admin_id'];
        $admin_name = $_POST['admin_name'];
        $admin_email = $_POST['admin_email'];

        try {
            // Prepare the SQL statement
            $sql = "INSERT INTO admins (id, name, email) VALUES (:id, :name, :email)";
            $stmt = $conn->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':id', $admin_id);
            $stmt->bindParam(':name', $admin_name);
            $stmt->bindParam(':email', $admin_email);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to all_admins.php after successful insertion
                header("Location: all_admins.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
} else {
    echo "Invalid request.";
}
?>
