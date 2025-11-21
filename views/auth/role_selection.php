<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESC; ?>">
    <meta name="author" content="">

    <title><?php echo $title ?? 'Select Role - ' . APP_NAME; ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo BASE_URL; ?>startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 1000px;
        }

        .role-selection-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .role-selection-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
        }

        /* Header Styles */
        .header {
            margin-bottom: 40px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #666;
            font-size: 1.2rem;
            font-weight: 300;
        }

        /* Role Cards */
        .role-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .role-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .role-card:hover::before {
            transform: scaleX(1);
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: #8B4513;
        }

        .admin-card:hover {
            border-color: #e74c3c;
        }

        .admin-card:hover::before {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .staff-card:hover {
            border-color: #27ae60;
        }

        .staff-card:hover::before {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }

        .role-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .admin-card .role-icon {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .staff-card .role-icon {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }

        .role-card:hover .role-icon {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .role-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .role-card p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .role-card ul {
            list-style: none;
            text-align: left;
            margin-bottom: 30px;
        }

        .role-card li {
            padding: 8px 0;
            color: #555;
            font-size: 0.95rem;
            position: relative;
            padding-left: 25px;
        }

        .role-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .role-button {
            background: linear-gradient(135deg, #654321 0%, #8B4513 50%, #A0522D 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.4);
            text-decoration: none;
            border: none;
            width: 100%;
            cursor: pointer;
        }

        .admin-card .role-button {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        .staff-card .role-button {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }

        .role-card:hover .role-button {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.5);
        }

        .admin-card:hover .role-button {
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }

        .staff-card:hover .role-button {
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4);
        }

        /* Back Button */
        .back-section {
            margin-top: 30px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #8B4513;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 12px 25px;
            border: 2px solid #8B4513;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: white;
        }

        .back-button:hover {
            background: #8B4513;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.4);
        }

        /* Loading Spinner */
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .loading-spinner i {
            font-size: 2rem;
            color: #8B4513;
            margin-bottom: 15px;
        }

        .loading-spinner p {
            color: #666;
            font-size: 1rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-selection-container {
            animation: fadeIn 0.6s ease;
        }

        .role-card {
            animation: fadeIn 0.6s ease;
        }

        .role-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .role-selection-container {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .role-cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .role-card {
                padding: 30px 20px;
            }
            
            .role-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
            }
            
            .role-card h2 {
                font-size: 1.5rem;
            }
            
            .role-card p {
                font-size: 1rem;
            }
            
            .role-button {
                padding: 12px 25px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            
            .role-selection-container {
                padding: 25px 15px;
            }
            
            .logo-section {
                flex-direction: column;
                gap: 10px;
            }
            
            .logo {
                width: 50px;
                height: 50px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .role-card {
                padding: 25px 15px;
            }
            
            .role-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            
            .role-card h2 {
                font-size: 1.3rem;
            }
            
            .role-card p {
                font-size: 0.95rem;
            }
            
            .role-card li {
                font-size: 0.9rem;
            }
            
            .role-button {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
            
            .back-button {
                padding: 10px 20px;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="role-selection-container">
            <div class="header">
                <div class="logo-section">
                    <i class="fas fa-couch fa-3x" style="color: #8B4513;"></i>
                </div>
                <h1><?php echo APP_NAME; ?></h1>
                <p class="subtitle">Select your role to continue</p>
            </div>

            <div class="role-cards">
                <!-- Admin Card -->
                <div class="role-card admin-card" onclick="window.location.href='<?php echo BASE_URL; ?>auth/registerAdmin'">
                    <div class="role-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2>Admin</h2>
                    <p>Manage bookings, services, and system settings</p>
                    <ul>
                        <li>Manage all bookings</li>
                        <li>View customer information</li>
                        <li>Control system settings</li>
                        <li>Generate reports</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>auth/registerAdmin" class="role-button">
                        <i class="fas fa-user-plus"></i> Register as Admin
                    </a>
                </div>

                <!-- Customer Card -->
                <div class="role-card staff-card" onclick="window.location.href='<?php echo BASE_URL; ?>auth/registerCustomer'">
                    <div class="role-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2>Customer</h2>
                    <p>Book services and manage your repair requests</p>
                    <ul>
                        <li>Book repair services</li>
                        <li>Track your bookings</li>
                        <li>View service history</li>
                        <li>Manage your profile</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>auth/registerCustomer" class="role-button">
                        <i class="fas fa-user-plus"></i> Register as Customer
                    </a>
                </div>
            </div>

            <div class="back-section">
                <a href="<?php echo BASE_URL; ?>" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</body>

</html>

