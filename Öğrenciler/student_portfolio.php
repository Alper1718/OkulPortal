<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
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
$stmt = $conn->prepare("
    SELECT assignments.title, assignments.description, assignments.due_date, users.username AS teacher
    FROM assignments
    JOIN users ON assignments.created_by = users.username
    WHERE assignments.class = ? AND assignments.due_date >= ?
");
$stmt->bind_param("ss", $class, $today);
$stmt->execute();
$assignments = $stmt->get_result();

// Fetch global school broadcasts
$stmt = $conn->prepare("SELECT content, created_at FROM global_broadcasts ORDER BY created_at DESC");
$stmt->execute();
$global_broadcasts = $stmt->get_result();

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
        }
    </style>
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2>Ödevleriniz:</h2>
    <ul>
    <?php
    if ($assignments->num_rows > 0) {
        while ($row = $assignments->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['title']) . "</strong>: " . htmlspecialchars($row['description']) . 
            " (Son Tarih: " . htmlspecialchars($row['due_date']) . ") - Ödevi Veren Öğretmen: " . htmlspecialchars($row['teacher']) . "</li>";
        }
    } else {
        echo "<li>Mevcut ödev yok.</li>";
    }
    ?>
    </ul>

    <h2>Okul Genel Duyuruları:</h2>
    <ul>
    <?php
    // Display global broadcasts
    if ($global_broadcasts->num_rows > 0) {
        while ($row = $global_broadcasts->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['content']) . " (Yayınlandığı Tarih: " . htmlspecialchars($row['created_at']) . ")</li>";
        }
    } else {
        echo "<li>Henüz duyuru yok.</li>";
    }
    ?>
    </ul>
    </p>
</body>
</html>
