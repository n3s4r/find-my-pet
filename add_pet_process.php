<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pet_name = $_POST['pet_name'];
$breed = $_POST['breed'];
$description = $_POST['description'];
$last_seen_location = $_POST['last_seen_location'];
$last_seen_date = $_POST['last_seen_date'];

// Ensure uploads/ folder exists
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$photo_name = basename($_FILES["photo"]["name"]);
$unique_name = time() . "_" . $photo_name;
$target_file = $target_dir . $unique_name;

if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO pets (user_id, pet_name, breed, description, last_seen_location, last_seen_date, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $pet_name, $breed, $description, $last_seen_location, $last_seen_date, $target_file);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php?pet_added=1");
    exit;
} else {
    echo "Error: Could not upload image.";
}
?>
