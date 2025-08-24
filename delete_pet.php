<?php
session_start();
include("includes/db.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check for a pet ID. Using $_REQUEST to handle both GET and POST for flexibility.
$pet_id = $_REQUEST['id'] ?? $_REQUEST['pet_id'] ?? null;

// Ensure a pet ID is provided
if (!$pet_id) {
    $_SESSION['error'] = "Invalid request: No pet ID provided.";
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Start a transaction to ensure both deletions happen or neither does
$conn->begin_transaction();

try {
    // Before deleting the pet, get the photo path to delete the file later.
    $pet_photo_query = $conn->prepare("SELECT photo FROM pets WHERE id = ? AND user_id = ?");
    $pet_photo_query->bind_param("ii", $pet_id, $user_id);
    $pet_photo_query->execute();
    $pet_photo_query->bind_result($photo_path);
    $pet_photo_query->fetch();
    $pet_photo_query->close();

    // First, delete all comments associated with the pet to satisfy the foreign key constraint.
    $delete_comments_stmt = $conn->prepare("DELETE FROM lost_pet_comments WHERE pet_id = ?");
    $delete_comments_stmt->bind_param("i", $pet_id);
    $delete_comments_stmt->execute();
    $delete_comments_stmt->close();
    
    // Next, delete the pet itself.
    // We check that the user owns the pet to prevent unauthorized deletions.
    $delete_pet_stmt = $conn->prepare("DELETE FROM pets WHERE id = ? AND user_id = ?");
    $delete_pet_stmt->bind_param("ii", $pet_id, $user_id);
    $delete_pet_stmt->execute();

    if ($delete_pet_stmt->affected_rows > 0) {
        // If the pet was deleted, commit the transaction and delete the photo file.
        $conn->commit();
        if ($photo_path && file_exists($photo_path)) {
            unlink($photo_path);
        }
        $_SESSION['message'] = "Pet post deleted successfully.";
    } else {
        // Rollback if the pet was not deleted (e.g., user didn't own the pet)
        $conn->rollback();
        $_SESSION['error'] = "Error deleting pet or you do not have permission to delete this post.";
    }
    $delete_pet_stmt->close();

} catch (mysqli_sql_exception $e) {
    // Rollback on any error and set an error message
    $conn->rollback();
    $_SESSION['error'] = "Failed to delete post: " . $e->getMessage();
}

// Redirect back to the dashboard
header("Location: dashboard.php");
exit;
?>
