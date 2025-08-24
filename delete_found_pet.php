<?php
session_start();
include("includes/db.php");

// Check if the user is logged in and if an ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$pet_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// First, get the photo path to delete the file
$stmt_get_photo = $conn->prepare("SELECT photo FROM found_pets WHERE id = ? AND user_id = ?");
$stmt_get_photo->bind_param("ii", $pet_id, $user_id);
$stmt_get_photo->execute();
$photo_result = $stmt_get_photo->get_result();

if ($photo_result->num_rows > 0) {
    $pet = $photo_result->fetch_assoc();
    $photo_path = $pet['photo'];

    // Delete the record from the database
    $stmt_delete = $conn->prepare("DELETE FROM found_pets WHERE id = ? AND user_id = ?");
    $stmt_delete->bind_param("ii", $pet_id, $user_id);

    if ($stmt_delete->execute()) {
        // If deletion from DB is successful, delete the photo file
        if (!empty($photo_path) && file_exists($photo_path)) {
            unlink($photo_path);
        }
        header("Location: dashboard.php");
    } else {
        echo "Error deleting record: " . $stmt_delete->error;
    }
    $stmt_delete->close();
} else {
    echo "Pet not found or you do not have permission to delete it.";
}

$stmt_get_photo->close();
$conn->close();
?>
