<?php
session_start();
include('db_connect.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab form data
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    // Prepare SQL query
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User authenticated
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Could be student, teacher, etc.

        // Redirect based on role or username
        if ($_SESSION['role'] == 'student') {
            header("Location: student_portfolio.php");
        } elseif ($_SESSION['role'] == 'teacher') {
            header("Location: teacher_dashboard.php");
        } else {
            header("Location: generic_dashboard.php");
        }
        exit;
    } else {
        // Invalid credentials
        echo "Invalid username or password";
    }
}
?>
