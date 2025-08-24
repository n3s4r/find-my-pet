<?php
session_start();
include("includes/db.php");

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$stmt = $conn->prepare("SELECT full_name, phone, profile_photo, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $phone, $profile_photo, $bio);
$stmt->fetch();
$stmt->close();

$success = $error = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['full_name']);
    $new_phone = trim($_POST['phone']);
    $new_bio = trim($_POST['bio']);
    
    // This is the correct input name from the form
    $file_input_name = 'profile_pic'; 

    // Handle profile picture upload if provided
    if (!empty($_FILES[$file_input_name]['name'])) {
        $target_dir = "uploads/profile_pics/";
        if (!is_dir($target_dir)) {
            // Use 0755 for better security
            mkdir($target_dir, 0755, true);
        }
        $file_name = time() . "_" . basename($_FILES[$file_input_name]["name"]);
        $target_file = $target_dir . $file_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
                // If upload is successful, update the $profile_photo variable to be saved in the DB
                $profile_photo = $target_file;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    // Only proceed with DB update if there was no upload error
    if (empty($error)) {
        $update_stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, profile_photo=?, bio=? WHERE id=?");
        $update_stmt->bind_param("ssssi", $new_name, $new_phone, $profile_photo, $new_bio, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Profile updated successfully!";
            // Update current page variables to reflect the change immediately
            $full_name = $new_name;
            $phone = $new_phone;
            $bio = $new_bio;
        } else {
            $error = "Error updating profile. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 70px; /* Space for fixed navbar */
            padding-bottom: 2rem;
        }
        .navbar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background-color: #fff;
        }
        .form-card {
            width: 100%;
            max-width: 700px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            border: none;
            background-color: #fff;
            animation: fadeInUp 0.8s ease-out;
        }
        .profile-pic-preview {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 5px 20px rgba(67, 83, 255, 0.25);
        }
        .form-card h3 {
            color: #343a40;
            font-weight: 700;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease-in-out;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #8a96ff;
            box-shadow: 0 0 0 4px rgba(67, 83, 255, 0.15);
        }
        .btn-custom {
            background-color: #4353ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .btn-custom:hover {
            background-color: #3545e0;
            color: #fff;
            transform: translateY(-2px);
        }
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">🐾 Find My Pet</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container d-flex justify-content-center align-items-center mt-5">
    <div class="form-card">
        <div class="text-center">
            <h3>👤 Edit Your Profile</h3>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success mt-4"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger mt-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="text-center my-4">
            <img src="<?= (!empty($profile_photo) && file_exists($profile_photo)) ? htmlspecialchars($profile_photo) : 'uploads/default-avatar.png' ?>" class="profile-pic-preview">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" class="form-control">
                </div>
                <div class="col-12">
                    <label for="bio" class="form-label">Bio (A little about yourself)</label>
                    <textarea id="bio" name="bio" class="form-control" rows="3"><?= htmlspecialchars($bio) ?></textarea>
                </div>
                <div class="col-12">
                    <label for="profile_pic" class="form-label">Update Profile Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic" class="form-control">
                </div>
                <div class="col-12 d-grid mt-3">
                    <button type="submit" class="btn btn-custom">Update Profile</button>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>