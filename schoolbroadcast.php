<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    // Redirect to login page if not logged in or not a school role
    header("Location: index.html");
    exit;
}

include('db_connect.php');
$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $broadcast_content = $_POST['broadcast'];
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['broadcast_file']) && $_FILES['broadcast_file']['error'] == 0) {
        $target_dir = "uploads/broadcasts/";
        $target_file = $target_dir . basename($_FILES["broadcast_file"]["name"]);
        if (move_uploaded_file($_FILES["broadcast_file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            echo "Dosya yüklenirken bir hata oluştu.";
        }
    }

    // Insert broadcast into the database
    $stmt = $conn->prepare("INSERT INTO global_broadcasts (content, created_by) VALUES (?, ?)");
    $stmt->bind_param("ss", $broadcast_content, $username);
    $stmt->execute();
    
    // Get the ID of the newly inserted broadcast
    $broadcast_id = $conn->insert_id;

    // Insert file information if a file was uploaded
    if ($file_path) {
        $stmt = $conn->prepare("INSERT INTO broadcast_files (broadcast_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $broadcast_id, $file_path);
        $stmt->execute();
    }

    echo "Duyuru başarıyla eklendi.";
}

// Fetch all existing broadcasts to show on the page
$stmt = $conn->prepare("SELECT content, created_by FROM global_broadcasts ORDER BY id DESC");
$stmt->execute();
$broadcasts = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Okul Genel Duyuruları</title>
    <link rel="stylesheet" href="schoolbroadcast.css">
</head>
<body>
    <h1>Hoşgeldiniz, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2>Yeni Duyuru Ekle</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="broadcast">Duyuru:</label><br>
        <textarea name="broadcast" required></textarea><br><br>

        <label for="broadcast_file">Dosya Yükleyin:</label>
        <input type="file" name="broadcast_file" accept=".pdf,.jpg,.png,.zip"><br><br>

        <button type="submit">Duyuru Ekle</button>
    </form>

    <h2>Mevcut Duyurular</h2>
    <ul>
    <?php
    while ($row = $broadcasts->fetch_assoc()) {
        echo "<li><strong>" . htmlspecialchars($row['created_by']) . "</strong>: " . htmlspecialchars($row['content']);
        if ($row['file_path']) {
            echo " - <a href='" . htmlspecialchars($row['file_path']) . "'>Dosyayı Görüntüle</a>";
        }
        echo "</li>";
    }
    ?>
    </ul>
    
     <form action = "logout.php" method = "post" onsubmit="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">
        <button style="background-color: red" type="submit">Çıkış Yap</button>
    </form>
</body>
</html>
