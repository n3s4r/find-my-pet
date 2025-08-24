<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("includes/db.php");

// *** NEW: Check for database connection errors immediately ***
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $profile_photo);
$stmt->fetch();
$stmt->close();

// Query to get all found pets, ordered by the most recent ones first.
$all_found_pets_query = "SELECT * FROM found_pets ORDER BY created_at DESC";
$all_found_pets_result = $conn->query($all_found_pets_query);

// *** NEW: Add more detailed error handling for the main query ***
if (!$all_found_pets_result) {
    // Show a detailed error message and stop execution
    die("Error fetching found pets: " . $conn->error);
}

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
    <title>Found Pets Feed | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(-45deg, #e8eaff, #4353ff, #f5f7fa, #c3cfe2);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            color: #333;
            padding-top: 70px;
            padding-bottom: 2rem;
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
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
            position: relative;
        }
        .nav-link:hover {
            color: #4353ff !important;
        }
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
        .feed-container {
            background: #ffffff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 1200px;
            margin: 50px auto 3rem auto;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .feed-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #fff;
        }
        .feed-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        .feed-card .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 250px;
            object-fit: cover;
        }
        .feed-card .card-body h5 {
            font-weight: 700;
            color: #4353ff;
        }
        .feed-card .card-body p {
            font-size: 0.9rem;
        }
        .comments-section {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
            margin-top: 20px;
        }
        .comment {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
            margin-bottom: 10px;
        }
        .comment-author {
            font-weight: bold;
            color: #4353ff;
            font-size: 0.9rem;
        }
        .comment-date {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .comment-text {
            font-size: 0.9rem;
            margin-top: 5px;
        }
        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        /* Dropdown styling */
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
        .logout-link:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Settings
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item logout-link" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container feed-container">
    <div class="text-center mb-5">
        <h2 style="font-weight: 700; color: #4353ff;">Found Pets in Your Community</h2>
        <p class="text-muted lead">Browse the list of pets that have been reported found by other users.</p>
    </div>

    <?php if ($all_found_pets_result->num_rows > 0): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while ($pet = $all_found_pets_result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card feed-card h-100">
                        <?php if (!empty($pet['photo'])): ?>
                            <img src="<?= htmlspecialchars($pet['photo']) ?>" class="card-img-top" alt="Pet Photo">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($pet['pet_name']) ?> (<?= htmlspecialchars($pet['breed']) ?>)</h5>
                            <p class="card-text text-muted"><?= htmlspecialchars($pet['description']) ?></p>
                            <ul class="list-unstyled mt-3">
                                <li><strong>Found On:</strong> <?= date("F j, Y", strtotime($pet['last_seen_date'])) ?></li>
                                <li><strong>Found At:</strong> <?= htmlspecialchars($pet['last_seen_location']) ?></li>
<!--                                <li><strong>Contact:</strong> <?= isset($pet['contact_info']) ? htmlspecialchars($pet['contact_info']) : 'N/A' ?></li>
                        -->
                            </ul>

                            <!-- Comments Section -->
                            <div class="comments-section">
                                <h6 class="mb-3">Comments</h6>
                                <?php
                                // Fix: Removed 'frc.created_at' as it seems to be an invalid column name.
                                $comments_query = $conn->prepare("SELECT frc.comment, u.full_name FROM found_report_comments frc JOIN users u ON frc.user_id = u.id WHERE frc.report_id = ?");
                                $comments_query->bind_param("i", $pet['id']);
                                $comments_query->execute();
                                $comments_result = $comments_query->get_result();

                                if ($comments_result->num_rows > 0) {
                                    while ($comment = $comments_result->fetch_assoc()) {
                                        echo '<div class="comment">';
                                        echo '<div class="d-flex justify-content-between">';
                                        echo '<span class="comment-author">' . htmlspecialchars($comment['full_name']) . '</span>';
                                        echo '</div>';
                                        echo '<p class="comment-text">' . htmlspecialchars($comment['comment']) . '</p>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="text-muted small">No comments yet.</p>';
                                }
                                $comments_query->close();
                                ?>

                                <!-- Comment Form -->
                                <form action="add_comment.php" method="POST" class="mt-3">
                                    <input type="hidden" name="report_id" value="<?= htmlspecialchars($pet['id']) ?>">
                                    <div class="mb-2">
                                        <textarea name="comment" class="form-control" rows="2" placeholder="Add a comment..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 px-3">
            <p class="lead text-muted">No found pets have been reported yet. Be the first to report one!</p>
            <a href="report_found.php" class="btn btn-primary mt-2">Report a Found Pet</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
