<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Find My Pet</title>
    <!-- Bootstrap CSS for basic layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font - Nunito -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* Shared animated background and typography */
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
            color: #333;
        }

        /* Reusable card container style */
        .card-container {
            width: 100%;
            max-width: 550px;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out;
            text-align: center;
        }

        h2 {
            font-weight: 700;
            color: #4353ff; /* Consistent color with the main theme */
        }
        
        .thank-you-emoji {
            font-size: 3rem;
            animation: bounce 1s infinite;
        }

        /* Custom button styling for consistency */
        .btn-custom {
            background-color: #4353ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #3545e0;
            color: #fff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 83, 255, 0.3);
        }

        .btn-outline-custom {
            color: #4353ff;
            border: 2px solid #4353ff;
            background: transparent;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: #4353ff;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(67, 83, 255, 0.3);
        }

        /* Keyframe animations */
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <span class="thank-you-emoji">🐾</span>
        <h2 class="mt-3">Thank You for Reporting!</h2>
        <p class="mt-3 text-muted">We’ve received your submission. You're helping a furry friend find its way home. ❤️</p>
        <div class="mt-5 d-grid gap-3 d-sm-flex justify-content-sm-center">
            <a href="index.php" class="btn btn-custom">🏠 Go to Home</a>
            <a href="dashboard.php" class="btn btn-outline-custom">📋 Go to Dashboard</a>
        </div>
    </div>
</body>
</html>
