<?php
$form_data = $_SESSION['registration_form_data'] ?? [];
$field_errors = $_SESSION['registration_field_errors'] ?? [];
// Note: We don't unset here because the modal might be rendered before some other checks, 
// though typically unsetting at the end of the file or after modal render is safer.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESC ?? 'Streamline Your Upholstery Business'; ?>">
    <meta name="author" content="">

    <title><?php echo $title ?? 'Welcome - UpholCare'; ?></title>

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Modern Clean Design - Matching the Provided Image */
        :root {
            --primary-navy: #2C3E50;
            --primary-blue: #3498DB;
            --accent-orange: #E67E22;
            --text-dark: #2C3E50;
            --text-muted: #7F8C8D;
            --bg-light: #F8F9FA;
            --white: #FFFFFF;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-dark);
            background: var(--white);
            overflow-x: hidden;
            line-height: 1.6;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Navigation - Clean & Professional */
        .navbar {
            padding: 1.2rem 0;
            background: var(--white) !important;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar.scrolled {
            box-shadow: var(--shadow-md);
            padding: 0.8rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-navy) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0;
        }

        .navbar-brand img {
            height: 70px;
            width: auto;
            max-width: 250px;
            object-fit: contain;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            margin: 0 0.75rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            color: var(--primary-blue) !important;
        }

        .btn-get-started {
            background: var(--primary-navy);
            color: white;
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            margin-left: 1rem;
        }

        .btn-get-started:hover {
            background: var(--primary-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-learn-more {
            background: transparent;
            color: var(--accent-orange);
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            border: 2px solid var(--accent-orange);
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 0.75rem;
        }

        .btn-learn-more:hover {
            background: var(--accent-orange);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Hero Section - Clean & Modern with Background Image */
        .hero-section {
            padding: 180px 0 120px;
            background-image: url('<?php echo defined("BASE_URL") ? BASE_URL : ""; ?>assets/images/furniture.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, 
                rgba(255, 255, 255, 0.95) 0%, 
                rgba(255, 255, 255, 0.85) 40%,
                rgba(255, 255, 255, 0.4) 70%,
                transparent 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary-navy);
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        /* Key Features Section */
        .features-section {
            padding: 80px 0;
            background: var(--white);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-navy);
            text-align: center;
            margin-bottom: 3rem;
        }

        .feature-card {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: var(--white);
            border-radius: 12px;
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #E8E8E8;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-blue);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .feature-icon.blue {
            background: linear-gradient(135deg, #EBF5FF 0%, #D6EAFF 100%);
            color: #3498DB;
        }

        .feature-icon.green {
            background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
            color: #27AE60;
        }

        .feature-icon.orange {
            background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
            color: #E67E22;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-navy);
            margin-bottom: 0.75rem;
        }

        .feature-description {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 80px 0;
            background: var(--bg-light);
        }

        .testimonial-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #E8E8E8;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .quote-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            opacity: 0.2;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            font-size: 1rem;
            color: var(--text-dark);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
        }

        .avatar-1 {
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        }

        .avatar-2 {
            background: linear-gradient(135deg, #F093FB 0%, #F5576C 100%);
        }

        .testimonial-info h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-navy);
            margin-bottom: 0.25rem;
        }

        .testimonial-info p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* Stats Section */
        .stats-section {
            padding: 60px 0;
            background: var(--primary-navy);
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-navy) 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }

        .btn-cta {
            background: white;
            color: var(--primary-navy);
            padding: 1rem 2.5rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            background: var(--bg-light);
        }

        /* Footer */
        .footer {
            padding: 50px 0 30px;
            background: var(--primary-navy);
            color: white;
        }

        .footer-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: white;
        }

        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-link:hover {
            color: white;
            padding-left: 5px;
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: rgba(255,255,255,0.6);
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            z-index: 999;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-navy);
            transform: translateY(-5px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                padding: 140px 0 100px;
                background-position: center right;
            }
            
            .hero-section::before {
                background: linear-gradient(to bottom, 
                    rgba(255, 255, 255, 0.95) 0%, 
                    rgba(255, 255, 255, 0.9) 60%,
                    rgba(255, 255, 255, 0.7) 100%);
            }
            
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .navbar-collapse {
                background: white;
                padding: 1rem;
                margin-top: 1rem;
                border-radius: 8px;
                box-shadow: var(--shadow-sm);
            }
        }

        /* Animation Classes */
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Updated Registration Modal Styles */
        #registrationModal .modal-content {
            border-radius: 12px;
            overflow: hidden;
            border: none;
            box-shadow: var(--shadow-lg);
        }

        #registrationModal .modal-header {
            background: var(--primary-navy);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }

        #registrationModal .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
        }

        #registrationModal .close {
            color: white;
            opacity: 0.8;
        }

        #registrationModal .nav-tabs {
            border-bottom: 2px solid #EEE;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        #registrationModal .nav-link {
            border: none;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            padding: 1rem 1.5rem;
            position: relative;
        }

        #registrationModal .nav-link.active {
            color: var(--primary-navy);
            background: transparent;
        }

        #registrationModal .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-navy);
        }

        #registrationModal .form-control {
            border-radius: 8px;
            border: 1px solid #DDD;
            padding: 0.75rem 1rem;
            height: auto;
            font-size: 0.95rem;
        }

        #registrationModal label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
        }

        #registrationModal .btn-register {
            background: var(--primary-navy);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            width: 100%;
            margin-top: 1rem;
            border: none;
            transition: all 0.3s ease;
        }

        #registrationModal .btn-register:hover {
            background: var(--primary-blue);
            transform: translateY(-2px);
        }

        /* Form Grid Layout */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            display: none; /* Hidden by default, shown when password has input */
        }

        .password-toggle:hover {
            color: var(--primary-navy);
        }

        .password-toggle:focus {
            outline: none;
        }

        .password-wrapper input {
            padding-right: 45px !important;
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        /* ID Upload Section Styles */
        .id-upload-zone {
            border: 2px dashed #cdd3da;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            background: #fff;
            transition: all 0.25s ease;
            cursor: pointer;
            position: relative;
        }
        .id-upload-zone:hover, .id-upload-zone.drag-over {
            border-color: var(--primary-blue);
            background: #f0f7ff;
        }
        .id-upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .id-upload-zone .upload-placeholder {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            pointer-events: none;
        }
        .id-upload-zone .upload-placeholder .upload-icon {
            font-size: 2rem;
            color: #adb5bd;
        }
        .id-preview-box {
            display: none;
            margin-top: 0.75rem;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #28a745;
            position: relative;
            max-height: 130px;
        }
        .id-preview-box img {
            width: 100%;
            max-height: 130px;
            object-fit: cover;
            display: block;
        }
        .id-preview-box .id-preview-badge {
            position: absolute;
            top: 6px; right: 6px;
            background: rgba(40,167,69,0.9);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .section-verification {
            background: linear-gradient(135deg, #fff9f0 0%, #fff3e0 100%);
            border-left: 4px solid #E67E22 !important;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>assets/images/logo2.png" 
                     alt="UpholCare Logo"
                     style="height: 70px; width: auto; max-width: 200px;">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">LogIn</a>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="btn btn-get-started" data-toggle="modal" data-target="#registrationModal">
                            Sign Up
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-8 hero-content">
                    <h1 class="hero-title fade-in-up">
                        Streamline Your<br>Upholstery Business
                    </h1>
                    <p class="hero-subtitle fade-in-up">
                        Manage booking and customer relationships effortlessly
                    </p>
                    <div class="fade-in-up">
                        <button type="button" class="btn btn-get-started btn-lg ml-0" data-toggle="modal" data-target="#registrationModal">
                            GET STARTED NOW
                        </button>
                        <a href="#features" class="btn btn-learn-more btn-lg">
                            LEARN MORE
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title scroll-reveal">Key Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4 scroll-reveal">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="feature-title">EASY SCHEDULING</h3>
                        <p class="feature-description">
                            Enable booking slots seamlessly
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 scroll-reveal">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3 class="feature-title">INVENTORY TRACKING</h3>
                        <p class="feature-description">
                            Streamline inventory for 0 wastelines
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 scroll-reveal">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">CLIENT MANAGEMENT</h3>
                        <p class="feature-description">
                            Tracks my way the food will anchor media honor
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 stat-item scroll-reveal">
                    <div class="stat-number">40+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="col-md-4 stat-item scroll-reveal">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
                <div class="col-md-4 stat-item scroll-reveal">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <h2 class="section-title scroll-reveal">Testimonials</h2>
            <div class="row">
                <div class="col-md-6 mb-4 scroll-reveal">
                    <div class="testimonial-card">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonial-text">
                            "Reupholstery Effort served Home Hoedot love Invest"
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar avatar-1">
                                <img src="https://i.pravatar.cc/150?img=1" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            </div>
                            <div class="testimonial-info">
                                <h5>Hor Wand Studer</h5>
                                <p>Reupholstery Effort served Home Hoedot love Invest</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 scroll-reveal">
                    <div class="testimonial-card">
                        <div class="quote-icon">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonial-text">
                            "Victoria deca el causa poutine Het mine original invest"
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar avatar-2">
                                <img src="https://i.pravatar.cc/150?img=5" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            </div>
                            <div class="testimonial-info">
                                <h5>Daning Cane Hour</h5>
                                <p>Victoria deca el causa poutine Het mine original invest</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="about">
        <div class="container">
            <h2 class="cta-title">Ready to Transform Your Business?</h2>
            <p class="cta-description">
                Join hundreds of satisfied customers who trust us with their upholstery needs.
            </p>
            <button type="button" class="btn btn-cta" data-toggle="modal" data-target="#registrationModal">
                <i class="fas fa-user-plus"></i> Get Started Today
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="footer-title">
                        <i class="fas fa-couch"></i> UpholCare
                    </h5>
                    <p style="color: rgba(255,255,255,0.7);">
                        Professional upholstery management system for modern businesses. Streamline your operations and grow your customer base.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="footer-link">Home</a></li>
                        <li><a href="#features" class="footer-link">Features</a></li>
                        <li><a href="#testimonials" class="footer-link">Testimonials</a></li>
                        <li><a href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UpholCare/'; ?>auth/roleSelection" class="footer-link">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li style="color: rgba(255,255,255,0.7); margin-bottom: 0.5rem;">
                            <i class="fas fa-phone"></i> +63 XXX XXX XXXX
                        </li>
                        <li style="color: rgba(255,255,255,0.7); margin-bottom: 0.5rem;">
                            <i class="fas fa-envelope"></i> info@upholcare.com
                        </li>
                        <li style="color: rgba(255,255,255,0.7);">
                            <i class="fas fa-map-marker-alt"></i> Cebu City, Philippines
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> UpholCare. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <div class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Unified Registration Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sign up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <ul class="nav nav-tabs" id="registrationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="provider-tab" data-toggle="tab" href="#provider" role="tab" aria-selected="true">ADMIN</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="customer-tab" data-toggle="tab" href="#customer" role="tab" aria-selected="false">CUSTOMER</a>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="registrationTabsContent">
                        <!-- Admin Tab -->
                        <div class="tab-pane fade show active" id="provider" role="tabpanel">
                            <form action="<?php echo BASE_URL; ?>auth/processRegisterAdmin" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="role" value="admin">
                                
                                <!-- Section 1: Contact Information -->
                                <div class="p-3 mb-3 bg-light rounded shadow-sm border-left-primary">
                                    <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-user-circle mr-2"></i>Contact Information</h6>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">FULL NAME *</label>
                                        <input type="text" class="form-control <?php echo (!empty($field_errors) && empty($form_data['full_name']) ? 'is-invalid' : ''); ?>" 
                                            id="full_name" name="full_name" 
                                            placeholder="John Doe" 
                                            value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>" 
                                            required>
                                        <?php if (!empty($field_errors) && empty($form_data['full_name'])): ?>
                                            <div class="invalid-feedback">Full name is required</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">EMAIL ADDRESS *</label>
                                            <input type="email" class="form-control <?php echo (!empty($field_errors) && (empty($form_data['email']) || !filter_var($form_data['email'] ?? '', FILTER_VALIDATE_EMAIL) || (isset($field_errors) && in_array('Email already exists', $field_errors))) ? 'is-invalid' : ''); ?>" 
                                                id="email" name="email" 
                                                placeholder="john@example.com" 
                                                value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                                                required>
                                            <?php if (!empty($field_errors)): ?>
                                                <?php if (empty($form_data['email'])): ?>
                                                    <div class="invalid-feedback">Email is required</div>
                                                <?php elseif (!filter_var($form_data['email'] ?? '', FILTER_VALIDATE_EMAIL)): ?>
                                                    <div class="invalid-feedback">Invalid email</div>
                                                <?php elseif (in_array('Email already exists', $field_errors)): ?>
                                                    <div class="invalid-feedback">Email already exists</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">PHONE NUMBER *</label>
                                            <input type="tel" class="form-control" 
                                                id="phone" name="phone" 
                                                placeholder="09123456789" 
                                                pattern="[0-9]{11}"
                                                minlength="11"
                                                maxlength="11"
                                                title="11 digits only"
                                                value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Section 2: Business Information -->
                                <div class="p-3 mb-3 bg-light rounded shadow-sm border-left-success">
                                    <h6 class="text-success font-weight-bold mb-3"><i class="fas fa-store mr-2"></i>Business Details</h6>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">BUSINESS/STORE NAME *</label>
                                        <input type="text" class="form-control <?php echo (!empty($field_errors) && empty($form_data['business_name']) ? 'is-invalid' : ''); ?>" 
                                            id="business_name" name="business_name" 
                                            placeholder="Your Shop Name" 
                                            value="<?php echo htmlspecialchars($form_data['business_name'] ?? ''); ?>" 
                                            required>
                                        <?php if (!empty($field_errors) && empty($form_data['business_name'])): ?>
                                            <div class="invalid-feedback">Business name is required</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">COMPLETE ADDRESS *</label>
                                        <textarea class="form-control <?php echo (!empty($field_errors) && empty($form_data['business_address']) ? 'is-invalid' : ''); ?>" 
                                            id="business_address" name="business_address" 
                                            rows="2" placeholder="Street, Barangay, City..." 
                                            required><?php echo htmlspecialchars($form_data['business_address'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="small font-weight-bold text-muted mb-1">CITY *</label>
                                            <input type="text" class="form-control" id="business_city" name="business_city" 
                                                value="<?php echo htmlspecialchars($form_data['business_city'] ?? 'Bohol'); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small font-weight-bold text-muted mb-1">PROVINCE *</label>
                                            <input type="text" class="form-control" id="business_province" name="business_province" 
                                                value="<?php echo htmlspecialchars($form_data['business_province'] ?? 'Bohol'); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold text-muted mb-2"><i class="fas fa-file-pdf text-danger mr-1"></i>BUSINESS PERMIT (PDF) *</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="business_permit" name="business_permit" accept=".pdf" required>
                                            <label class="custom-file-label" for="business_permit">Choose PDF file...</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Admin Valid ID Verification -->
                                <div class="p-3 mb-3 rounded shadow-sm section-verification">
                                    <h6 class="text-warning font-weight-bold mb-1">
                                        <i class="fas fa-id-card mr-2"></i>Identity Verification
                                    </h6>
                                    <p class="small text-muted mb-3">Upload a clear photo of your valid government-issued ID. The Super Admin will review this before approving your account.</p>
                                    <label class="small font-weight-bold text-muted mb-2">VALID ID (IMAGE) *</label>
                                    <div class="id-upload-zone" id="adminIdUploadZone">
                                        <input type="file" id="admin_valid_id" name="valid_id" accept="image/jpeg,image/png,image/webp,image/gif" required>
                                        <div class="upload-placeholder">
                                            <div class="upload-icon"><i class="fas fa-id-card"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size:0.9rem;">Click or drag to upload your ID</div>
                                                <div class="text-muted" style="font-size:0.78rem;">UMID, PhilSys, Driver's License, Passport &bull; JPG / PNG / WEBP &bull; Max 5MB</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="id-preview-box" id="adminIdPreviewBox">
                                        <img id="adminIdPreviewImg" src="#" alt="ID Preview">
                                        <span class="id-preview-badge"><i class="fas fa-check mr-1"></i>ID Uploaded</span>
                                    </div>
                                    <div id="adminIdFileName" class="small text-success mt-1" style="display:none;"></div>
                                </div>
                                
                                <!-- Section 3: Account Security -->
                                <div class="p-3 mb-3 bg-light rounded shadow-sm border-left-info">
                                    <h6 class="text-info font-weight-bold mb-3"><i class="fas fa-lock mr-2"></i>Account Security</h6>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-md-0">
                                            <label class="small font-weight-bold text-muted mb-1">PASSWORD *</label>
                                            <div class="password-wrapper position-relative">
                                                <input type="password" class="form-control" id="password_reg" name="password" placeholder="******" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('password_reg')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon_reg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">REPEAT PASSWORD *</label>
                                            <div class="password-wrapper position-relative">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="******" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group px-2">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="agree_terms" name="agree_terms" required>
                                        <label class="custom-control-label" for="agree_terms">
                                            I agree to the terms & conditions and understand my account is pending verification.
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" id="btn-register-admin" class="btn btn-primary btn-block btn-lg shadow" style="background: var(--primary-navy); border: none; font-weight: 700;" disabled>
                                    <i class="fas fa-user-shield mr-2"></i>REGISTER AS ADMIN
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small font-weight-bold text-primary" href="#" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">ALREADY HAVE AN ACCOUNT? LOGIN!</a>
                            </div>
                        </div>

                        <!-- Customer Tab -->
                        <div class="tab-pane fade" id="customer" role="tabpanel">
                            <form action="<?php echo BASE_URL; ?>auth/processRegisterCustomer" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="role" value="customer">
                                
                                <!-- Section 1: Personal Information -->
                                <div class="p-3 mb-3 bg-light rounded shadow-sm border-left-primary">
                                    <h6 class="text-primary font-weight-bold mb-3"><i class="fas fa-user mr-2"></i>Personal Information</h6>
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">FULL NAME *</label>
                                        <input type="text" class="form-control" name="full_name" placeholder="John Doe" required>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">EMAIL ADDRESS *</label>
                                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">PHONE NUMBER *</label>
                                            <input type="tel" name="phone" class="form-control" placeholder="09123456789" pattern="[0-9]{11}" minlength="11" maxlength="11" title="11 digits only" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Customer Valid ID Verification -->
                                <div class="p-3 mb-3 rounded shadow-sm section-verification">
                                    <h6 class="text-warning font-weight-bold mb-1">
                                        <i class="fas fa-id-card mr-2"></i>Identity Verification
                                    </h6>
                                    <p class="small text-muted mb-3">Upload a clear photo of your valid government-issued ID for account verification.</p>
                                    <label class="small font-weight-bold text-muted mb-2">VALID ID (IMAGE) *</label>
                                    <div class="id-upload-zone" id="custIdUploadZone">
                                        <input type="file" id="customer_valid_id" name="customer_id_image" accept="image/jpeg,image/png,image/webp,image/gif" required>
                                        <div class="upload-placeholder">
                                            <div class="upload-icon"><i class="fas fa-id-card"></i></div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size:0.9rem;">Click or drag to upload your ID</div>
                                                <div class="text-muted" style="font-size:0.78rem;">UMID, PhilSys, Driver's License, Passport &bull; JPG / PNG / WEBP &bull; Max 5MB</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="id-preview-box" id="custIdPreviewBox">
                                        <img id="custIdPreviewImg" src="#" alt="ID Preview">
                                        <span class="id-preview-badge"><i class="fas fa-check mr-1"></i>ID Uploaded</span>
                                    </div>
                                    <div id="custIdFileName" class="small text-success mt-1" style="display:none;"></div>
                                </div>

                                <!-- Section 3: Account Security -->
                                <div class="p-3 mb-3 bg-light rounded shadow-sm border-left-info">
                                    <h6 class="text-info font-weight-bold mb-3"><i class="fas fa-lock mr-2"></i>Account Security</h6>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-md-0">
                                            <label class="small font-weight-bold text-muted mb-1">PASSWORD *</label>
                                            <div class="password-wrapper position-relative">
                                                <input type="password" id="password_cust" name="password" class="form-control" placeholder="******" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('password_cust')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon_cust"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 mb-0">
                                            <label class="small font-weight-bold text-muted mb-1">REPEAT PASSWORD *</label>
                                            <div class="password-wrapper position-relative">
                                                <input type="password" id="confirm_password_cust" name="confirm_password" class="form-control" placeholder="******" required>
                                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password_cust')">
                                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon_cust"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group px-2">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="agree_terms_customer" name="agree_terms" required>
                                        <label class="custom-control-label" for="agree_terms_customer">
                                            I agree to the terms & conditions and understand how my data will be used.
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" id="btn-register-customer" class="btn btn-primary btn-block btn-lg shadow" style="background: var(--primary-navy); border: none; font-weight: 700;" disabled>
                                    <i class="fas fa-user-plus mr-2"></i>REGISTER AS CUSTOMER
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small font-weight-bold text-primary" href="#" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">ALREADY HAVE AN ACCOUNT? LOGIN!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unified Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
                <div class="modal-header" style="background: var(--primary-navy); color: white; padding: 1.5rem; border-bottom: none;">
                    <h5 class="modal-title" style="font-weight: 700; font-size: 1.5rem;">Log in</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo BASE_URL; ?>auth/processLogin" method="POST" autocomplete="off">
                        <div class="form-group">
                            <label class="small fw-bold" style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block;">Email Address</label>
                            <input type="email" name="email" id="loginEmail" class="form-control" placeholder="Enter your email" required autocomplete="new-password" style="border-radius: 8px; border: 1px solid #DDD; padding: 0.75rem 1rem; height: auto; font-size: 0.95rem;">
                        </div>
                        <div class="form-group">
                            <label class="small fw-bold" style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block;">Password</label>
                            <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" required autocomplete="new-password" style="border-radius: 8px; border: 1px solid #DDD; padding: 0.75rem 1rem; height: auto; font-size: 0.95rem;">
                        </div>
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="rememberMe">
                                <label class="custom-control-label" for="rememberMe">Remember Me</label>
                            </div>
                        </div>
                        <button type="submit" id="btn-login" class="btn btn-register w-100" disabled style="background: var(--primary-navy); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; transition: all 0.3s ease; width: 100%; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.5;">Log In</button>
                        <div class="text-center mt-3">
                            <a href="#" class="small text-muted" data-dismiss="modal" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p class="small mb-0">Don't have an account? <a href="#" class="font-weight-bold" data-dismiss="modal" data-toggle="modal" data-target="#registrationModal">Sign up</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: var(--shadow-lg);">
                <div class="modal-header" style="background: var(--primary-navy); color: white; padding: 1.5rem; border-bottom: none;">
                    <h5 class="modal-title" style="font-weight: 700; font-size: 1.5rem;">Forgot Password?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-4 small text-muted">No worries! Enter your email address below and we'll send you a link to reset your password.</p>
                    <form action="<?php echo BASE_URL; ?>auth/processForgotPassword" method="POST">
                        <div class="form-group mb-4">
                            <label class="small fw-bold" style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block;">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required style="border-radius: 8px; border: 1px solid #DDD; padding: 0.75rem 1rem; height: auto; font-size: 0.95rem;">
                        </div>
                        <button type="submit" class="btn btn-register w-100" style="background: var(--primary-navy); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; transition: all 0.3s ease; width: 100%; text-transform: uppercase; letter-spacing: 0.5px;">Reset Password</button>
                        <div class="text-center mt-3">
                            <a href="#" class="small font-weight-bold" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const backToTop = document.getElementById('backToTop');
            
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Show/hide back to top button
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Back to top functionality
        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Scroll reveal animation
        const revealElements = document.querySelectorAll('.scroll-reveal');
        
        function checkReveal() {
            revealElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 100;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', checkReveal);
        checkReveal(); // Check on page load

        // Update custom file input label (business permit)
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // ── Valid ID upload handlers ──────────────────────────────────────────
        function setupIdUpload(inputId, previewBoxId, previewImgId, fileNameId, zoneId) {
            const input = document.getElementById(inputId);
            const previewBox = document.getElementById(previewBoxId);
            const previewImg = document.getElementById(previewImgId);
            const fileNameEl = document.getElementById(fileNameId);
            const zone = document.getElementById(zoneId);

            if (!input) return;

            input.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) { previewBox.style.display = 'none'; fileNameEl.style.display = 'none'; return; }

                // Validate type
                if (!['image/jpeg','image/png','image/webp','image/gif'].includes(file.type)) {
                    alert('Only image files are allowed (JPG, PNG, WEBP, GIF).');
                    this.value = ''; previewBox.style.display = 'none'; fileNameEl.style.display = 'none';
                    return;
                }
                // Validate size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must not exceed 5MB.');
                    this.value = ''; previewBox.style.display = 'none'; fileNameEl.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewBox.style.display = 'block';
                    fileNameEl.textContent = '\u2714 ' + file.name;
                    fileNameEl.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });

            // Drag & drop visual feedback
            zone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('drag-over'); });
            zone.addEventListener('dragleave', function() { this.classList.remove('drag-over'); });
            zone.addEventListener('drop', function(e) {
                e.preventDefault(); this.classList.remove('drag-over');
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }

        setupIdUpload('admin_valid_id', 'adminIdPreviewBox', 'adminIdPreviewImg', 'adminIdFileName', 'adminIdUploadZone');
        setupIdUpload('customer_valid_id', 'custIdPreviewBox',  'custIdPreviewImg',  'custIdFileName',  'custIdUploadZone');

        // Toggle registration buttons based on terms agreement
        $('#agree_terms').on('change', function() {
            $('#btn-register-admin').prop('disabled', !$(this).is(':checked'));
        });

        $('#agree_terms_customer').on('change', function() {
            $('#btn-register-customer').prop('disabled', !$(this).is(':checked'));
        });

        // Initialize button states on page load (in case session holds checked value)
        $(document).ready(function() {
            if ($('#agree_terms').is(':checked')) {
                $('#btn-register-admin').prop('disabled', false);
            }
            if ($('#agree_terms_customer').is(':checked')) {
                $('#btn-register-customer').prop('disabled', false);
            }
        });

        // Close mobile menu on link click
        $('.navbar-nav>li>a').on('click', function(){
            $('.navbar-collapse').collapse('hide');
        });

        // Open login or registration modal based on hash or session errors
        $(document).ready(function() {
            if (window.location.hash === '#login' || <?php echo isset($_SESSION['error']) && !isset($_SESSION['registration_field_errors']) ? 'true' : 'false'; ?>) {
                $('#loginModal').modal('show');
            } else if (window.location.hash === '#signup' || <?php echo isset($_SESSION['registration_field_errors']) ? 'true' : 'false'; ?>) {
                $('#registrationModal').modal('show');
            }
        });

        // Clear login form fields when modal opens to prevent autofill
        $('#loginModal').on('show.bs.modal', function () {
            setTimeout(function() {
                $('#loginEmail').val('');
                $('#loginPassword').val('');
            }, 50);
        });

        // Enable/disable login button based on Remember Me checkbox
        $('#rememberMe').on('change', function() {
            const loginBtn = $('#btn-login');
            if ($(this).is(':checked')) {
                loginBtn.prop('disabled', false).css('opacity', '1');
            } else {
                loginBtn.prop('disabled', true).css('opacity', '0.5');
            }
        });

        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            let iconId;
            if (fieldId === 'loginPassword') iconId = 'toggleLoginPasswordIcon';
            else if (fieldId === 'password_reg') iconId = 'togglePasswordIcon_reg';
            else if (fieldId === 'confirm_password') iconId = 'toggleConfirmPasswordIcon';
            else if (fieldId === 'password_cust') iconId = 'togglePasswordIcon_cust';
            else if (fieldId === 'confirm_password_cust') iconId = 'toggleConfirmPasswordIcon_cust';
            
            const icon = document.getElementById(iconId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>