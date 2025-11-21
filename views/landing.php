<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo APP_DESC; ?>">
    <meta name="author" content="">

    <title><?php echo $title ?? 'Welcome - UpholCare'; ?></title>

    <!-- Custom fonts -->
    <link href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Custom Styles for Landing Page */
        :root {
            --primary-color: #8B4513;
            --secondary-color: #A0522D;
            --accent-color: #654321;
            --text-dark: #2d3748;
            --text-light: #718096;
            --brown-dark: #654321;
            --brown-medium: #8B4513;
            --brown-light: #A0522D;
            --brown-tan: #CD853F;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--text-dark);
            background: #8B4513; /* Fallback brown color matching the image */
        }

        /* Navigation */
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .navbar-brand i {
            margin-right: 0.5rem;
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Hero Section with Background Image */
        .hero-section {
            background-image: url('<?php 
                $bgImage = (defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/') . 'assets/images/1.png';
                echo $bgImage;
            ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 120px 0;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            /* Fallback background color in case image doesn't load */
            background-color: #8B4513;
        }

        /* Dark overlay for better text readability - reduced opacity to show image */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.5) 0%, rgba(22, 33, 62, 0.45) 50%, rgba(15, 52, 96, 0.5) 100%);
            z-index: 0;
        }

        /* Additional overlay for better contrast - lighter */
        .hero-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.7), 0 0 20px rgba(0, 0, 0, 0.5);
            color: white;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.6;
        }

        .hero-buttons .btn {
            margin: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .btn-primary-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5);
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-outline-custom:hover {
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
            border-color: white;
        }

        .btn-outline-primary-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            transition: all 0.3s ease;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            border-radius: 50px;
            font-size: 0.9rem;
        }

        .btn-outline-primary-custom:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(134, 95, 28, 0.4);
        }

        .navbar .btn-primary-custom,
        .navbar .btn-outline-primary-custom {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-dark);
            text-align: center;
        }

        .feature-description {
            color: var(--text-light);
            text-align: center;
            line-height: 1.7;
        }

        /* Services Section with Background Image */
        .services-section {
            padding: 80px 0;
            background-image: url('<?php 
                $bgImage = (defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/') . 'assets/images/1.png';
                echo $bgImage;
            ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
            /* Fallback background color */
            background-color: #8B4513;
        }

        /* Overlay for services section */
        .services-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.6) 0%, rgba(22, 33, 62, 0.55) 50%, rgba(15, 52, 96, 0.6) 100%);
            z-index: 0;
        }

        .services-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.25);
            z-index: 0;
        }

        .services-section .container {
            position: relative;
            z-index: 1;
        }

        .services-section .section-title {
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .services-section .section-subtitle {
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
        }

        .service-item {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .service-item:hover {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            transform: translateX(10px) translateY(-5px);
        }

        .service-icon {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--brown-dark) 0%, var(--brown-medium) 50%, var(--brown-light) 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-title {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-link:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            margin-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Stats Section with Background Image */
        .stats-section {
            padding: 60px 0;
            background-image: url('<?php 
                $bgImage = (defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/') . 'assets/images/1.png';
                echo $bgImage;
            ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
            color: white;
            /* Fallback background color */
            background-color: #8B4513;
        }

        /* Overlay for stats section - slightly different for visual separation */
        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.7) 0%, rgba(118, 75, 162, 0.65) 50%, rgba(102, 126, 234, 0.7) 100%);
            z-index: 0;
        }

        .stats-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 0;
        }

        .stats-section .container {
            position: relative;
            z-index: 1;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            color: white;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.95;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
            color: rgba(255, 255, 255, 0.95);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                background-attachment: scroll;
                padding: 100px 0;
                min-height: auto;
            }

            .services-section {
                background-attachment: scroll;
            }

            .stats-section {
                background-attachment: scroll;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .hero-content .col-lg-6:last-child {
                margin-top: 2rem;
            }

            .hero-content .col-lg-6:last-child div {
                padding: 2rem !important;
            }

            .hero-content .col-lg-6:last-child i {
                font-size: 120px !important;
            }

            .service-item {
                margin-bottom: 1.5rem;
            }

            .navbar .btn-primary-custom,
            .navbar .btn-outline-primary-custom {
                width: 100%;
                margin: 0.25rem 0;
                display: block;
            }

            .navbar .nav-item.d-flex {
                flex-direction: column;
                width: 100%;
            }

            .navbar .nav-item.d-flex .btn {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
                            <a class="navbar-brand" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>">
                <i class="fas fa-couch"></i> UpholCare
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item d-flex align-items-center">
                        <a class="btn btn-outline-primary-custom mr-2" href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/register">
                            <i class="fas fa-user-plus"></i> Sign Up
                        </a>
                        <a class="btn btn-primary-custom" href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/login">
                            <i class="fas fa-sign-in-alt"></i> Log In
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Expert Repair & Restoration Services</h1>
                    <p class="hero-subtitle">
                        Transform your furniture, bedding, and vehicle covers with our professional upholstery services. 
                        Quality craftsmanship, affordable prices, exceptional results.
                    </p>
                    <div class="hero-buttons">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/register" class="btn btn-primary-custom">
                            <i class="fas fa-rocket"></i> Get Started Free
                        </a>
                        <a href="#services" class="btn btn-outline-custom">
                            <i class="fas fa-info-circle"></i> Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center hero-content">
                    <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); padding: 3rem; border-radius: 20px; border: 2px solid rgba(255, 255, 255, 0.2);">
                        <i class="fas fa-couch" style="font-size: 200px; opacity: 0.8; color: white; text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);"></i>
                        <p class="mt-3" style="color: white; font-size: 1.2rem; font-weight: 600; text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.5);">
                            Professional Upholstery Services
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
                <div class="col-md-3 stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="col-md-3 stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section" id="services">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Professional repair and restoration for all your upholstery needs</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="service-item">
                        <i class="fas fa-car service-icon"></i>
                        <h3 class="font-weight-bold">Vehicle Covers</h3>
                        <p class="text-muted mb-0">
                            Custom car seat repairs, truck covers, and automotive upholstery. 
                            Restore your vehicle's interior to like-new condition.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="service-item">
                        <i class="fas fa-bed service-icon"></i>
                        <h3 class="font-weight-bold">Bedding Services</h3>
                        <p class="text-muted mb-0">
                            Mattress covers, bed sheets, and bedroom furniture restoration. 
                            Quality materials and expert craftsmanship.
                        </p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="service-item">
                        <i class="fas fa-couch service-icon"></i>
                        <h3 class="font-weight-bold">Furniture Restoration</h3>
                        <p class="text-muted mb-0">
                            Sofas, chairs, ottomans, and more. Complete reupholstering services 
                            to bring your furniture back to life.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">Experience the difference of professional service</p>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3 class="feature-title">Expert Craftsmanship</h3>
                        <p class="feature-description">
                            Skilled professionals with years of experience in upholstery 
                            and restoration. Quality workmanship guaranteed.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Easy Booking</h3>
                        <p class="feature-description">
                            Book your service online in minutes. Select your preferred 
                            date and time at your convenience.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3 class="feature-title">Affordable Pricing</h3>
                        <p class="feature-description">
                            Competitive rates without compromising quality. Get premium 
                            service at fair prices.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3 class="feature-title">Fast Service</h3>
                        <p class="feature-description">
                            Quick turnaround times. Most projects completed within 
                            your specified timeframe.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Quality Materials</h3>
                        <p class="feature-description">
                            Premium fabrics and materials sourced from trusted suppliers. 
                            Built to last.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">24/7 Support</h3>
                        <p class="feature-description">
                            Dedicated customer support team ready to assist you 
                            anytime you need help.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="about">
        <div class="container">
            <h2 class="cta-title">Ready to Transform Your Furniture?</h2>
            <p class="cta-description">
                Join hundreds of satisfied customers who trust us with their repair and restoration needs.
            </p>
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/register" class="btn btn-outline-custom btn-lg">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
            <br>
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/roleSelection" class="btn btn-link text-white mt-3">
                Already have an account? Login here <i class="fas fa-arrow-right"></i>
            </a>
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
                    <p class="text-muted">
                        Professional repair and restoration services for furniture, 
                        bedding, and vehicle covers.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="footer-link">Home</a></li>
                        <li><a href="#services" class="footer-link">Services</a></li>
                        <li><a href="#features" class="footer-link">Features</a></li>
                        <li><a href="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>auth/roleSelection" class="footer-link">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-title">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="text-muted"><i class="fas fa-phone"></i> +63 XXX XXX XXXX</li>
                        <li class="text-muted"><i class="fas fa-envelope"></i> info@uphocare.com</li>
                        <li class="text-muted"><i class="fas fa-map-marker-alt"></i> Manila, Philippines</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> UpholCare. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo defined('BASE_URL') ? BASE_URL : 'http://localhost/UphoCare/'; ?>startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Smooth Scroll -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

</body>

</html>
