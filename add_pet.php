<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Pet | Find My Pet</title>
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

        .form-icon {
            display: inline-block;
            font-size: 3.5rem;
            line-height: 1;
            padding: 15px;
            background-color: #e8eaff;
            color: #4353ff;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .form-card h3 {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 1.5rem;
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
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>

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
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container d-flex justify-content-center align-items-center mt-5">
    <div class="form-card">
        <div class="text-center">
            <div class="form-icon">📋</div>
            <h3>Report a Lost Pet</h3>
            <p class="text-muted mb-4">Please fill out the details below to create a report.</p>
        </div>
        
        <form action="add_pet_process.php" method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="pet_name" class="form-label">Pet's Name</label>
                    <input type="text" class="form-control" name="pet_name" id="pet_name" required>
                </div>
                <div class="col-md-6">
                    <label for="breed" class="form-label">Breed</label>
                    <input type="text" class="form-control" name="breed" id="breed" required>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Description (color, size, unique marks)</label>
                    <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                </div>
                <div class="col-md-6">
                    <label for="last_seen_location" class="form-label">Last Seen Location</label>
                    <input type="text" class="form-control" name="last_seen_location" id="last_seen_location" required>
                </div>
                <div class="col-md-6">
                    <label for="last_seen_date" class="form-label">Last Seen Date</label>
                    <input type="date" class="form-control" name="last_seen_date" id="last_seen_date" required>
                </div>
                <div class="col-12">
                    <label for="photo" class="form-label">Pet Photo (Recent and clear)</label>
                    <input type="file" class="form-control" name="photo" id="photo" accept="image/*" required>
                </div>
                <div class="col-12 d-grid mt-4">
                    <button type="submit" class="btn btn-custom">Submit Report</button>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>