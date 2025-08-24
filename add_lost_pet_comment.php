<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $pet_id = $_POST['pet_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO lost_pet_comments (pet_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $pet_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: lost_feed.php");
exit;
?>
