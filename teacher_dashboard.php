<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $class = $_POST['class'];
    $file_path = null;

    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $target_dir = "uploads/assignments/";
        $target_file = $target_dir . basename($_FILES["assignment_file"]["name"]);
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            echo "Dosya yüklenirken bir hata oluştu.";
        }
    }

    $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date, class, created_by, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $description, $due_date, $class, $username, $file_path);
    $stmt->execute();
    echo "Ödev başarıyla eklendi.";
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğretmen Kontrol Paneli</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
</head>
<body>
    <form method="POST">
        <div class="imgcontainer">
            <img src="okul.jpg" alt="Okul Logosu" class="avatar"/> <!-- .avatar sınıfını ekledik -->
        </div>

        <div class="elementler">
            <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>
            <h2>Ödev Ekle</h2>
            <label for="title">Ödev Başlığı:</label>
            <input type="text" name="title" required>

            <label for="description">Açıklama:</label>
            <textarea name="description" rows="4" cols="50" required></textarea>

            <label for="due_date">Son Teslim Tarihi:</label>
            <input type="date" name="due_date" required>

            <label for="class">Sınıf Seçin:</label>
            <select name="class" required>
                <option value="9/A">9/A</option>
                <option value="9/B">9/B</option>
                <option value="10/A">10/A</option>
                <option value="10/B">10/B</option>
                <option value="11/A">11/A</option>
                <option value="11/B">11/B</option>
            </select>

            <button type="submit">Ödev Ekle</button>
        </div>
        <div>
            <h2>Mevcut Ödevler</h2>
            <ul>
            <?php
            $stmt = $conn->prepare("SELECT title, description, due_date, class FROM assignments WHERE created_by = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($row['title']) . " - " . htmlspecialchars($row['description']) . " (Sınıf: " . htmlspecialchars($row['class']) . ", Son Tarih: " . htmlspecialchars($row['due_date']) . ")</li>";
            }
            ?>
            </ul>
        </div>
    </form>
    <form action = "logout.php" method = "post" onsubmit="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">
        <button style="background-color: red" type="submit">Çıkış Yap</button>
    </form>

    
</body>
</html>
