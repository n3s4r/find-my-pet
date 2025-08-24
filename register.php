<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "findmypet";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";
$full_name_val = "";
$email_val = "";
$phone_val = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password_input = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Preserve input values on error
    $full_name_val = $full_name;
    $email_val = $email;
    $phone_val = $phone;

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "An account with this email already exists.";
    } elseif ($password_input !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $password = password_hash($password_input, PASSWORD_DEFAULT);

        $profile_photo = "";
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $filename = basename($_FILES["profile_photo"]["name"]);
            // Sanitize filename
            $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
            $target_file = $target_dir . time() . "_" . $safe_filename;

            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $profile_photo = $target_file;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }

        // Only proceed if there was no upload error
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, profile_photo) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $phone, $password, $profile_photo);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php' class='alert-link'>log in</a>.";
                // Clear form values on success
                $full_name_val = "";
                $email_val = "";
                $phone_val = "";
            } else {
                $error = "Error during registration. Please try again.";
            }
        }
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem 0;
        }

        .register-card {
            width: 100%;
            max-width: 550px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            border: none;
            background-color: #fff;
            animation: fadeInUp 0.8s ease-out;
        }

        .register-icon {
            display: inline-block;
            font-size: 3.5rem;
            line-height: 1;
            padding: 15px;
            background-color: #e8eaff;
            color: #4353ff;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .register-card h3 {
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
        
        .form-text-link a {
            color: #4353ff;
            font-weight: 700;
            text-decoration: none;
        }

        .form-text-link a:hover {
            text-decoration: underline;
        }
        
        /* Keyframes */
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

<main class="container d-flex justify-content-center align-items-center">
    <div class="register-card text-center">
        <div class="register-icon">📝</div>
        <h3>Create an Account</h3>
        <p class="text-muted mb-4">Join our community to help find and report pets.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" enctype="multipart/form-data" novalidate>
            <div class="mb-3 text-start">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required value="<?= htmlspecialchars($full_name_val) ?>" />
            </div>

            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?= htmlspecialchars($email_val) ?>" />
            </div>

            <div class="mb-3 text-start">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" required value="<?= htmlspecialchars($phone_val) ?>" />
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required />
            </div>

            <div class="mb-3 text-start">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required />
            </div>

            <div class="mb-4 text-start">
                <label for="profile_photo" class="form-label">Profile Photo (Optional)</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="form-control" />
            </div>

            <button type="submit" class="btn btn-custom w-100">Register</button>
        </form>

        <p class="text-center mt-4 form-text-link">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</main>

</body>
</html>