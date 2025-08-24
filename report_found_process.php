<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in, and set user_id accordingly.
    // If not logged in, user_id will be NULL.
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Sanitize and validate input
    $pet_name = !empty($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : 'Unknown';
    $breed = !empty($_POST['breed']) ? htmlspecialchars($_POST['breed']) : 'Unknown';
    $description = htmlspecialchars($_POST['description']);
    $last_seen_location = htmlspecialchars($_POST['last_seen_location']);
    $last_seen_date = htmlspecialchars($_POST['last_seen_date']);
    
    // Check for optional location data
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;

    // Handle image upload
    $photo_path = null;
    if (isset($_FILES['photos']) && $_FILES['photos']['error'][0] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['photos']['name'][0]);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['photos']['tmp_name'][0], $upload_file)) {
            $photo_path = $upload_file;
        } else {
            die("Error uploading file.");
        }
    }

    // Insert data into the found_pets table, including optional latitude, longitude, and user_id.
    // The 'i' in the bind_param handles both integer and null values for the user_id.
    $sql = "INSERT INTO found_pets (user_id, pet_name, breed, description, last_seen_location, last_seen_date, photo, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // The bind_param now uses a single check for the user_id variable.
    $stmt->bind_param("issssssss", $user_id, $pet_name, $breed, $description, $last_seen_location, $last_seen_date, $photo_path, $latitude, $longitude);
    
    if ($stmt->execute()) {
        // Redirect to a dedicated thank you page instead of a popup
        header("Location: thank_you.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, redirect back to the form
    header("Location: report_found.php");
}
?>
