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
$success = false;

// Fetch the pet's data from the found_pets table
$stmt = $conn->prepare("SELECT * FROM found_pets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $pet_id, $user_id);
$stmt->execute();
$pet_result = $stmt->get_result();

if ($pet_result->num_rows === 0) {
    echo "Pet not found or you do not have permission to edit it.";
    exit;
}

$pet = $pet_result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pet_name = !empty($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : 'Unknown';
    $breed = !empty($_POST['breed']) ? htmlspecialchars($_POST['breed']) : 'Unknown';
    $description = htmlspecialchars($_POST['description']);
    $last_seen_date = htmlspecialchars($_POST['last_seen_date']);
    
    // Handle optional location data
    $last_seen_location = isset($_POST['last_seen_location']) ? htmlspecialchars($_POST['last_seen_location']) : 'Location not shared';
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? $_POST['longitude'] : null;
    
    $photo_path = $pet['photo'];
    
    // Handle new photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        // Delete old photo if it exists
        if (!empty($pet['photo']) && file_exists($pet['photo'])) {
            unlink($pet['photo']);
        }

        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_file)) {
            $photo_path = $upload_file;
        } else {
            echo "Error uploading new photo.";
            exit;
        }
    }

    // Update the record in the database
    $update_stmt = $conn->prepare("UPDATE found_pets SET pet_name = ?, breed = ?, description = ?, last_seen_location = ?, last_seen_date = ?, photo = ?, latitude = ?, longitude = ? WHERE id = ?");
    $update_stmt->bind_param("sssssssii", $pet_name, $breed, $description, $last_seen_location, $last_seen_date, $photo_path, $latitude, $longitude, $pet_id);

    if ($update_stmt->execute()) {
        $success = true;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error updating record: " . $update_stmt->error;
        exit;
    }
    $update_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Found Pet | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(-45deg, #f5f7fa, #c3cfe2, #e8eaff, #4353ff);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card-container {
            width: 100%;
            max-width: 650px;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out;
        }
        .form-label { font-weight: 700; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #4353ff;
            box-shadow: 0 0 0 4px rgba(67, 83, 255, 0.25);
        }
        .btn-custom {
            background-color: #4353ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn-custom:hover { background-color: #3545e0; transform: translateY(-3px); }
        .current-photo {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
<main class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card-container text-center">
        <h3 class="mb-4">✏️ Edit Found Pet Report</h3>
        <form action="edit_found_pet.php?id=<?= htmlspecialchars($pet_id) ?>" method="POST" enctype="multipart/form-data">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="pet_name" class="form-label">Pet Name (if known)</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" value="<?= htmlspecialchars($pet['pet_name']) ?>" placeholder="E.g., Buddy">
                </div>
                <div class="col-md-6">
                    <label for="breed" class="form-label">Breed</label>
                    <input type="text" class="form-control" id="breed" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>" placeholder="E.g., Golden Retriever">
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($pet['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="last_seen_location" class="form-label">Location Found</label>
                <input type="text" class="form-control" id="last_seen_location" name="last_seen_location" value="<?= htmlspecialchars($pet['last_seen_location']) ?>" placeholder="E.g., City Park">
            </div>
            <div class="mb-3">
                <label for="last_seen_date" class="form-label">Date Found</label>
                <input type="date" class="form-control" id="last_seen_date" name="last_seen_date" value="<?= htmlspecialchars($pet['last_seen_date']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Update Photo</label>
                <?php if (!empty($pet['photo'])): ?>
                    <div class="mb-2">
                        <small class="text-muted">Current Photo:</small><br>
                        <img src="<?= htmlspecialchars($pet['photo']) ?>" alt="Current Pet Photo" class="current-photo">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            
            <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($pet['latitude']) ?>">
            <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($pet['longitude']) ?>">

            <div class="text-center">
                <button type="submit" class="btn btn-custom px-4 py-2">Save Changes</button>
            </div>
        </form>
    </div>
</main>
<?php if ($success): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Report Updated!',
        text: 'The found pet report has been successfully updated.',
        confirmButtonColor: '#4353ff'
    });
</script>
<?php endif; ?>
</body>
</html>
