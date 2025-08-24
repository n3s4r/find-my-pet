<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Find My Pet' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 900px;
            margin: 0 auto 3rem auto;
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

        .profile-pic:hover {
            transform: scale(1.05);
        }

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
            height: 100%;
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

        .action-card h4 {
            margin-bottom: 5px;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .action-card p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }

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
    <div class="container">
