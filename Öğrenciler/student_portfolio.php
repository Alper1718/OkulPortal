<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    // Redirect to login page if not logged in or not a student
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

// Fetch the student's class
$stmt = $conn->prepare("SELECT class FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$class = $user_data['class'];

// Get today's date
$today = date('Y-m-d');

// Fetch assignments for the student's class that are not expired
$stmt = $conn->prepare("SELECT title, description, due_date FROM assignments WHERE class = ? AND due_date >= ?");
$stmt->bind_param("ss", $class, $today);
$stmt->execute();
$assignments = $stmt->get_result();

// Fetch school broadcasts (optional)
$stmt = $conn->prepare("SELECT broadcasts FROM student_portfolio WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$broadcasts = $stmt->get_result()->fetch_assoc()['broadcasts'];

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Portföyü</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        ul {
            list-style-type: none;
        }
        ul li {
            background-color: #f1f1f1;
            padding: 10px;
            margin: 5px 0;
            border-left: 5px solid #04AA6D;
        }
    </style>
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2>Ödevleriniz:</h2>
    <ul>
    <?php
    // Display assignments
    if ($assignments->num_rows > 0) {
        while ($row = $assignments->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['title']) . "</strong>: " . htmlspecialchars($row['description']) . " (Son Tarih: " . htmlspecialchars($row['due_date']) . " )</li>";
        }
    } else {
        echo "<li>Mevcut ödev yok.</li>";
    }
    ?>
    </ul>

    <h2>Okul Duyuruları:</h2>
    <p>
    <?php
    // Display school broadcasts
    if (!empty($broadcasts)) {
        echo nl2br(htmlspecialchars($broadcasts));
    } else {
        echo "Henüz duyuru yok.";
    }
    ?>
    </p>
</body>
</html>
