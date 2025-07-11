<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | SCM Project</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: url('/assets/img/cocoa-beans.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-card {
            background: linear-gradient(135deg, #fff 70%, #e3eafe 100%);
            border-radius: 2.5rem 1.5rem 2.5rem 1.5rem / 1.5rem 2.5rem 1.5rem 2.5rem;
            box-shadow: 0 8px 40px 0 rgba(26,35,126,0.18), 0 1.5px 8px 0 rgba(67,90,255,0.08) inset;
            border: 2.5px solid #e3eafe;
            padding: 4rem 3rem 3rem 3rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
            min-height: 480px;
            position: relative;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .welcome-card:hover {
            box-shadow: 0 16px 64px 0 rgba(26,35,126,0.22), 0 2px 12px 0 rgba(67,90,255,0.10) inset;
            transform: translateY(-4px) scale(1.015);
        }
        .welcome-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 2rem;
        }
        .display-5 {
            font-size: 2.8rem;
            font-weight: 700;
            letter-spacing: -1px;
            color: #1a237e;
            font-family: 'Poppins', Arial, sans-serif;
        }
        .lead {
            font-size: 1.25rem;
            font-weight: 400;
            color: #333;
            font-family: 'Poppins', Arial, sans-serif;
            margin-bottom: 2.2rem;
        }
        .btn-primary {
            background: #1a237e;
            border: none;
            font-size: 1.15rem;
            font-family: 'Poppins', Arial, sans-serif;
            padding: 0.85rem 3rem;
            border-radius: 2rem;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(26,35,126,0.13);
        }
        .btn-primary:hover {
            background: #3949ab;
            box-shadow: 0 4px 24px rgba(26,35,126,0.18);
        }
        .footer {
            margin-top: 2.5rem;
            color: #fff;
            font-size: 1.05rem;
            opacity: 0.90;
            font-family: 'Poppins', Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="welcome-card mx-auto">
        <img src="/assets/img/favicon.png" alt="SCM Logo" class="welcome-logo">
        <h1 class="display-5 mb-3">Welcome to SCM Project</h1>
        <p class="lead mb-4">Your Supply Chain Management solution starts here.</p>
        <a href="/dashboard" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
        </a>
        <div class="footer mt-4">
            &copy; 2024 SCM Project. All rights reserved.
        </div>
    </div>