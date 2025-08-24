<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("includes/db.php");

$user_id = $_SESSION['user_id'];
$pet_id = $_GET['id'] ?? null;

// Ensure a pet ID is provided and it belongs to the logged-in user
if (!$pet_id) {
    header("Location: dashboard.php");
    exit;
}

// Fetch pet details to pre-fill the form
$pet_query = $conn->prepare("SELECT * FROM pets WHERE id = ? AND user_id = ?");
$pet_query->bind_param("ii", $pet_id, $user_id);
$pet_query->execute();
$pet_result = $pet_query->get_result();
$pet = $pet_result->fetch_assoc();
$pet_query->close();

if (!$pet) {
    echo "Pet not found or you don't have permission to edit it.";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = $_POST['pet_name'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $last_seen_location = $_POST['last_seen_location'] ?? '';
    $last_seen_date = $_POST['last_seen_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $photo = $pet['photo']; // Keep existing photo by default

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if file is a real image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    $update_query = $conn->prepare("UPDATE pets SET pet_name = ?, breed = ?, last_seen_location = ?, last_seen_date = ?, description = ?, photo = ? WHERE id = ? AND user_id = ?");
    $update_query->bind_param("ssssssii", $pet_name, $breed, $last_seen_location, $last_seen_date, $description, $photo, $pet_id, $user_id);

    if ($update_query->execute()) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . $update_query->error;
    }
    $update_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 5rem;
            padding-bottom: 2rem;
        }
        .dashboard-card {
            background: #ffffff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            max-width: 900px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card-img-top {
            border-radius: 15px;
            height: 250px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .btn-primary {
            background-color: #4353ff;
            border-color: #4353ff;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 700;
            transition: transform 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            background-color: #3545e0;
            border-color: #3545e0;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 700;
            transition: transform 0.2s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-3px);
            background-color: #5a6268;
            border-color: #5a6268;
        }
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="dashboard-card">
        <h3 class="text-center mb-4">Edit Pet Information</h3>
        <?php if (!empty($pet['photo'])): ?>
            <img src="<?= htmlspecialchars($pet['photo']) ?>" class="img-fluid card-img-top" alt="Pet Photo">
        <?php endif; ?>
        <form method="POST" action="edit_pet.php?id=<?= $pet['id'] ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="pet_name" class="form-label">Pet Name</label>
                <input type="text" class="form-control" id="pet_name" name="pet_name" value="<?= htmlspecialchars($pet['pet_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="breed" class="form-label">Breed</label>
                <input type="text" class="form-control" id="breed" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_seen_location" class="form-label">Last Seen Location</label>
                <input type="text" class="form-control" id="last_seen_location" name="last_seen_location" value="<?= htmlspecialchars($pet['last_seen_location']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_seen_date" class="form-label">Last Seen Date</label>
                <input type="date" class="form-control" id="last_seen_date" name="last_seen_date" value="<?= htmlspecialchars($pet['last_seen_date']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($pet['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Update Photo</label>
                <input class="form-control" type="file" id="photo" name="photo">
                <small class="text-muted">Leave blank to keep the current photo.</small>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>