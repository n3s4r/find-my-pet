<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $report_id = $_POST['report_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO found_report_comments (report_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $report_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: found_feed.php");
exit;
