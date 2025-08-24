<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("includes/db.php");

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, phone, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone, $profile_photo);
$stmt->fetch();
$stmt->close();

// Corrected queries to get notification counts from the 'pets' and 'found_pets' tables
$lost_pets_count_query = "SELECT COUNT(*) AS total_lost FROM pets";
$lost_pets_count_result = $conn->query($lost_pets_count_query);
$lost_pets_count_row = $lost_pets_count_result->fetch_assoc();
$lost_pets_count = $lost_pets_count_row['total_lost'];

$found_pets_count_query = "SELECT COUNT(*) AS total_found FROM found_pets";
$found_pets_count_result = $conn->query($found_pets_count_query);
$found_pets_count_row = $found_pets_count_result->fetch_assoc();
$found_pets_count = $found_pets_count_row['total_found'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <!-- ADDED: Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ADDED: Wavy background animation from other pages */
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(-45deg, #e8eaff, #4353ff, #f5f7fa, #c3cfe2);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            color: #333;
            padding-top: 70px; /* Space for fixed navbar */
            padding-bottom: 2rem;
        }

        /* ADDED: Keyframes for the background animation */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navbar styling for consistency with other pages */
        .navbar {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #333 !important;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 700;
            margin: 0 5px;
            transition: color 0.3s ease;
            position: relative; /* Needed for notification badge positioning */
        }
        
        .nav-link:hover {
            color: #4353ff !important;
        }

        /* ADDED: Notification badge styling */
        .notification-badge {
            position: absolute;
            top: 5px;
            right: 0px;
            padding: 3px 6px;
            border-radius: 50%;
            background-color: red;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            line-height: 1;
        }

        /* Dropdown toggle styling */
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            font-weight: 400;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #e8eaff;
            color: #4353ff;
        }

        /* NEW: Hover effect for the logout link */
        .logout-link:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 900px;
            margin: 50px auto 3rem auto;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 5px 20px rgba(67, 83, 255, 0.25);
            transition: transform 0.3s ease;
        }
        
        .profile-pic:hover { transform: scale(1.05); }
        
        .dashboard-card h3 {
            color: #4353ff;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .action-card {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            text-align: left;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            border: 1px solid #e9ecef;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 83, 255, 0.15);
            color: #4353ff;
            background-color: #fff;
        }
        
        .action-icon {
            font-size: 2.5rem;
            margin-right: 20px;
            line-height: 1;
        }
        
        .action-card h4 { margin-bottom: 5px; font-weight: 700; font-size: 1.2rem; }
        
        .action-card p { margin-bottom: 0; font-size: 0.9rem; color: #6c757d; }
        
        .pet-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #fff;
        }
        
        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        
        .pet-card .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 220px;
            object-fit: cover;
        }
        
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">🐾 Find My Pet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="lost_feed.php">
                        Lost Pets
                        <?php if ($lost_pets_count > 0): ?>
                            <span class="notification-badge"><?= $lost_pets_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="found_feed.php">
                        Found Pets
                        <?php if ($found_pets_count > 0): ?>
                            <span class="notification-badge"><?= $found_pets_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="nearby_services.php">Nearby Services</a>
                </li>
                <!-- UPDATED: Settings dropdown with text -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Settings
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- UPDATED: Logout link with a new class for the hover effect -->
                        <li><a class="dropdown-item logout-link" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="dashboard-card">
        <div class="row align-items-center g-5">
            <div class="col-lg-4 text-center">
                <img src="<?= !empty($profile_photo) ? htmlspecialchars($profile_photo) : 'uploads/default-avatar.png' ?>"
                    alt="Profile Photo" class="profile-pic">
                <h3>Welcome,<br><?= htmlspecialchars($full_name) ?>!</h3>
                <p class="text-muted mb-1"><?= htmlspecialchars($email) ?></p>
                <p class="text-muted"><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                <a href="logout.php" class="btn btn-outline-danger mt-3">Logout</a>
            </div>

            <div class="col-lg-8">
                <div class="action-grid">
                    <a href="add_pet.php" class="action-card">
                        <div class="action-icon">🐾</div>
                        <div>
                            <h4>Report a Lost Pet</h4>
                            <p>Fill out a form to report your missing pet and alert the community.</p>
                        </div>
                    </a>
                    <a href="report_found.php" class="action-card">
                        <div class="action-icon">🐾</div>
                        <div>
                            <h4>Report a Found Pet</h4>
                            <p>Found a pet? Report it here to help reunite it with its owner.</p>
                        </div>
                    </a>
                    <a href="lost_feed.php" class="action-card">
                        <div class="action-icon">🔍</div>
                        <div>
                            <h4>View Lost Pets Feed</h4>
                            <p>Browse recent listings of pets that have been lost.</p>
                        </div>
                    </a>
                    <a href="found_feed.php" class="action-card">
                        <div class="action-icon">🔍</div>
                        <div>
                            <h4>View Found Pets Feed</h4>
                            <p>Browse recent listings of pets that have been found by others.</p>
                        </div>
                    </a>
                    <a href="nearby_services.php" class="action-card">
                        <div class="action-icon">🗺️</div>
                        <div>
                            <h4>Find Vets & Pet Shops</h4>
                            <p>Locate nearby veterinary clinics and pet supply stores.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="pet-list-section">
        <h4 class="mb-4 text-center section-title">🐶 Your Reported Lost Pets</h4>

        <?php
        // Updated to only show YOUR reported lost pets from the `pets` table
        $pet_query = $conn->prepare("SELECT * FROM pets WHERE user_id = ? ORDER BY created_at DESC");
        $pet_query->bind_param("i", $user_id);
        $pet_query->execute();
        $pet_result = $pet_query->get_result();
        ?>

        <?php if ($pet_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($pet = $pet_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card pet-card h-100">
                            <?php if (!empty($pet['photo'])): ?>
                                <img src="<?= htmlspecialchars($pet['photo']) ?>" class="card-img-top" alt="Pet Photo">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($pet['pet_name']) ?> (<?= htmlspecialchars($pet['breed']) ?>)</h5>
                                <p class="card-text text-muted" style="flex-grow: 1;"><?= htmlspecialchars($pet['description']) ?></p>
                                <p><small><strong>Last Seen:</strong> <?= htmlspecialchars($pet['last_seen_location']) ?> on <?= date("F j, Y", strtotime($pet['last_seen_date'])) ?></small></p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_pet.php?id=<?= $pet['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pet?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 px-3" style="background-color: #fff; border-radius: 15px;">
                <p class="lead text-muted">You haven't reported any lost pets yet.</p>
                <a href="add_pet.php" class="btn btn-primary mt-2">Report Your First Pet!</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- NEW SECTION: Your Reported Found Pets -->
    <div class="pet-list-section mt-5">
        <h4 class="mb-4 text-center section-title">🐾 Your Reported Found Pets</h4>

        <?php
        // New query to show YOUR reported found pets from the `found_pets` table
        $found_pet_query = $conn->prepare("SELECT * FROM found_pets WHERE user_id = ? ORDER BY created_at DESC");
        $found_pet_query->bind_param("i", $user_id);
        $found_pet_query->execute();
        $found_pet_result = $found_pet_query->get_result();
        ?>

        <?php if ($found_pet_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($found_pet = $found_pet_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card pet-card h-100">
                            <?php if (!empty($found_pet['photo'])): ?>
                                <img src="<?= htmlspecialchars($found_pet['photo']) ?>" class="card-img-top" alt="Pet Photo">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($found_pet['pet_name']) ?> (<?= htmlspecialchars($found_pet['breed']) ?>)</h5>
                                <p class="card-text text-muted" style="flex-grow: 1;"><?= htmlspecialchars($found_pet['description']) ?></p>
                                <p><small><strong>Found Location:</strong> <?= htmlspecialchars($found_pet['last_seen_location']) ?> on <?= date("F j, Y", strtotime($found_pet['last_seen_date'])) ?></small></p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_found_pet.php?id=<?= $found_pet['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="delete_found_pet.php?id=<?= $found_pet['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pet?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 px-3" style="background-color: #fff; border-radius: 15px;">
                <p class="lead text-muted">You haven't reported any found pets yet.</p>
                <a href="report_found.php" class="btn btn-primary mt-2">Report a Found Pet!</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
