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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $full_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password_input, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $full_name;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
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
    <title>Login | Find My Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
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
        
        .login-card {
            width: 100%;
            max-width: 500px;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out, pulse 2s infinite alternate;
            transition: transform 0.3s ease-in-out;
        }

        .login-card:hover {
            transform: scale(1.02);
        }

        .login-icon {
            display: inline-block;
            font-size: 3.5rem;
            line-height: 1;
            padding: 15px;
            background-color: #e8eaff;
            color: #4353ff;
            border-radius: 50%;
            margin-bottom: 1rem;
            animation: bounceIn 1s ease-out;
        }

        .login-card h3 {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 1.5rem;
            animation: slideInLeft 0.8s ease-out;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #4353ff;
            box-shadow: 0 0 0 4px rgba(67, 83, 255, 0.25);
            transform: scale(1.01);
        }

        .btn-custom {
            background-color: #4353ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #3545e0;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 83, 255, 0.3);
        }
        
        .form-text-link a {
            color: #4353ff;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-text-link a:hover {
            text-decoration: underline;
            color: #3545e0;
        }
        
        /* Keyframes for animations */
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

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes pulse {
            from {
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            }
            to {
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.1);
                opacity: 0;
            }
            60% {
                transform: scale(1.2);
                opacity: 1;
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-50px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <main class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-card text-center">
            <div class="login-icon"></div>
            <h3>Welcome Back!</h3>
            <p class="text-muted mb-4">Please enter your details to log in.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger animate__animated animate__shakeX"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus />
                </div>
                <div class="mb-4 text-start">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-custom w-100">Login</button>
            </form>

            <p class="text-center mt-4 form-text-link">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </div>
    </main>
</body>
</html>