<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $class = $_POST['class'];

    // Insert assignment into the database
    $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date, class, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $due_date, $class, $username);
    $stmt->execute();
    echo "Ödev başarıyla eklendi.";
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğretmen Kontrol Paneli</title>
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2>Ödev Ekle</h2>
    <form method="POST">
        <label for="title">Ödev Başlığı:</label><br>
        <input type="text" name="title" required><br><br>

        <label for="description">Açıklama:</label><br>
        <textarea name="description" rows="4" cols="50" required></textarea><br><br>

        <label for="due_date">Son Teslim Tarihi:</label><br>
        <input type="date" name="due_date" required><br><br>

        <label for="class">Sınıf Seçin:</label><br>
        <select name="class" required>
            <option value="9/A">9/A</option>
            <option value="9/B">9/B</option>
            <option value="10/A">10/A</option>
            <option value="10/B">10/B</option>
            <option value="11/A">11/A</option>
            <option value="11/B">11/B</option>
        </select><br><br>

        <button type="submit">Ödev Ekle</button>
    </form>

    <h2>Mevcut Ödevler</h2>
    <ul>
    <?php
    // Display existing assignments created by the teacher
    $stmt = $conn->prepare("SELECT title, description, due_date, class FROM assignments WHERE created_by = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['title']) . " - " . htmlspecialchars($row['description']) . " (Sınıf: " . htmlspecialchars($row['class']) . ", Son Tarih: " . htmlspecialchars($row['due_date']) . ")</li>";
    }
    ?>
    </ul>
</body>
</html>
