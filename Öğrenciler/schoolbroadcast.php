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
    // Get the broadcast content from the form
    $broadcast_content = $_POST['broadcast'];

    // Insert the broadcast into a special table for global broadcasts
    $stmt = $conn->prepare("INSERT INTO global_broadcasts (content, created_by) VALUES (?, ?)");
    $stmt->bind_param("ss", $broadcast_content, $username);
    $stmt->execute();
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        textarea {
            width: 100%;
            height: 100px;
        }
        button {
            padding: 10px;
            background-color: #04AA6D;
            color: white;
            border: none;
            cursor: pointer;
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

    <h2>Yeni Duyuru Ekle</h2>
    <form method="POST">
        <label for="broadcast">Duyuru:</label><br>
        <textarea name="broadcast" required></textarea><br><br>
        <button type="submit">Duyuru Ekle</button>
    </form>

    <h2>Mevcut Duyurular</h2>
    <ul>
    <?php
    // Display existing broadcasts
    if ($broadcasts->num_rows > 0) {
        while ($row = $broadcasts->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['created_by']) . "</strong>: " . htmlspecialchars($row['content']) . "</li>";
        }
    } else {
        echo "<li>Henüz duyuru yok.</li>";
    }
    ?>
    </ul>
</body>
</html>
