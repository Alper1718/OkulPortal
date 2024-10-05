<?php
// db_connect.php

$servername = "localhost";
$username = "school_user";
$password = "secure_password"; // Replace with your actual password
$dbname = "school_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
