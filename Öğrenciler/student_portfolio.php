<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

// Fetch student data (assignments, broadcasts)
$sql = "SELECT assignments, broadcasts FROM student_portfolio WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student_data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Student Portfolio</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <h2>Your Assignments:</h2>
    <p><?php echo nl2br(htmlspecialchars($student_data['assignments'])); ?></p>

    <h2>School Broadcasts:</h2>
    <p><?php echo nl2br(htmlspecialchars($student_data['broadcasts'])); ?></p>
</body>
</html>
