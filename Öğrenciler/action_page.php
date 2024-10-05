<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($_SESSION['role'] == 'student') {
            header("Location: student_portfolio.php");
        }
        elseif ($_SESSION['role'] == 'teacher') {
            header("Location: teacher_dashboard.php");
        } else {
            echo "Bilinmeyen rol";
        }
    } else {
        echo "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>
