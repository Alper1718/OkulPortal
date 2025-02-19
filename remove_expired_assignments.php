<?php
include('db_connect.php');

// Get current date
$today = date('Y-m-d');

// Remove assignments where the due date has passed
$stmt = $conn->prepare("DELETE FROM assignments WHERE due_date < ?");
$stmt->bind_param("s", $today);
$stmt->execute();

echo "Expired assignments removed.";
?>
