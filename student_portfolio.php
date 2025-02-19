<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT class FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$class = $user_data['class'];

$today = date('d-m-Y');

$stmt = $conn->prepare("
    SELECT assignments.title, assignments.description, assignments.due_date, users.username AS teacher
    FROM assignments
    JOIN users ON assignments.created_by = users.username
    WHERE assignments.class = ? AND assignments.due_date >= ?
");
$stmt->bind_param("ss", $class, $today);
$stmt->execute();
$assignments = $stmt->get_result();

$stmt = $conn->prepare("SELECT content, created_at FROM global_broadcasts ORDER BY created_at DESC");
$stmt->execute();
$global_broadcasts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Portföyü</title>
    <link rel="stylesheet" href="student_portfolio.css">
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2>Ödevleriniz:</h2>
    <ul>
    <?php
    if ($assignments->num_rows > 0) {
        while ($row = $assignments->fetch_assoc()) {
            echo "<li>";
            echo "<strong>" . htmlspecialchars($row['title']) . "</strong>"; // Assignment title
            echo "<span>" . htmlspecialchars($row['description']) . "</span>"; // Assignment description
            $formatted_date = (new DateTime($row['due_date'])) -> format('d-m-Y');
            echo "<span  style=color:tomato;> Son Tarih: " . htmlspecialchars($formatted_date) . "</span>"; // Due date
            echo "<span> Ödevi Veren Öğretmen: " . htmlspecialchars($row['teacher']) . "</span>"; // Teacher name
            echo "</li>";
        }
    } else {
        echo "<li>Mevcut ödev yok.</li>";
    }
    ?>
    </ul>

    <h2>Okul Genel Duyuruları:</h2>
    <ul>
    <?php
    if ($global_broadcasts->num_rows > 0) {
        while ($row = $global_broadcasts->fetch_assoc()) {
            echo "<li>";
            echo htmlspecialchars($row['content']);
            $formatted_date = (new DateTime($row['due_date'])) -> format('d-m-Y');
            echo "<span style=color:tomato; > Yayınlandığı Tarih: " . htmlspecialchars($formatted_date) . "</span>";
            echo "</li>";
        }
    } else {
        echo "<li>Henüz duyuru yok.</li>";
    }
    ?>
    </ul>
    
    <form action = "logout.php" method = "post" onsubmit="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">
        <button style="background-color: red" type="submit">Çıkış Yap</button>
    </form>

</body>
</html>
