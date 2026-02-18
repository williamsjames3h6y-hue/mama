<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Helper::escape($settings['site_name'] ?? 'EarningsLLC'); ?> - Earn Money Online</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .landing-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 50%, #34495e 100%);
            position: relative;
        }

        .nav-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(26, 42, 58, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: #3498db;
            letter-spacing: -0.5px;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 25px rgba(52, 152, 219, 0.4);
            transform: translateY(-2px);
        }

        .hero-section {
            padding: 180px 5% 100px;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 1s ease-out;
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

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }

        .hero-buttons .btn {
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
        }

        .features-section {
            padding: 100px 5%;
            background: white;
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.25rem;
            color: #7f8c8d;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            padding: 2.5rem;
            border-radius: 16px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #3498db;
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .feature-description {
            color: #7f8c8d;
            line-height: 1.8;
        }

        .vip-section {
            padding: 100px 5%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .vip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .vip-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }

        .vip-card:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .vip-level {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .vip-rate {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #ffd700;
        }

        .vip-description {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .cta-section {
            padding: 100px 5%;
            background: #2c3e50;
            text-align: center;
            color: white;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 2rem;
        }

        .footer {
            background: #1a2a3a;
            color: white;
            padding: 60px 5% 30px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: #3498db;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.75rem;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
        }

        .image-showcase {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 4rem auto 0;
        }

        .showcase-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .showcase-image:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.125rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .image-showcase {
                grid-template-columns: 1fr;
            }

            .nav-buttons {
                gap: 0.5rem;
            }

            .btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>
<body>
    <div class="landing-page">
        <nav class="nav-header">
            <div class="logo"><?php echo Helper::escape($settings['site_name'] ?? 'EarningsLLC'); ?></div>
            <div class="nav-buttons">
                <a href="/login" class="btn btn-outline">Login</a>
                <a href="/register" class="btn btn-primary">Sign Up</a>
            </div>
        </nav>

        <section class="hero-section">
            <h1 class="hero-title">Earn Money From Home</h1>
            <p class="hero-subtitle">Join thousands of professionals earning extra income through flexible remote work opportunities</p>
            <div class="hero-buttons">
                <a href="/register" class="btn btn-primary">Get Started Now</a>
                <a href="/login" class="btn btn-outline">Login to Account</a>
            </div>

            <div class="image-showcase">
                <img src="/assets/images/1.jpg" alt="Work from home" class="showcase-image floating-animation" style="animation-delay: 0s;">
                <img src="/assets/images/2.jpg" alt="Flexible schedule" class="showcase-image floating-animation" style="animation-delay: 0.5s;">
                <img src="/assets/images/3.jpg" alt="Earn money" class="showcase-image floating-animation" style="animation-delay: 1s;">
            </div>
        </section>

        <section class="features-section">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">Everything you need to start earning money from home</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">💼</div>
                    <h3 class="feature-title">Flexible Work</h3>
                    <p class="feature-description">Work on your own schedule from anywhere in the world. Choose projects that match your skills and availability.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3 class="feature-title">Competitive Pay</h3>
                    <p class="feature-description">Earn competitive rates based on your VIP level. The more you work, the higher your earning potential.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Fast Payments</h3>
                    <p class="feature-description">Get paid quickly and securely. Request withdrawals anytime and receive your earnings promptly.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Quality Projects</h3>
                    <p class="feature-description">Access a variety of high-quality projects across different industries and skill levels.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3 class="feature-title">Career Growth</h3>
                    <p class="feature-description">Advance through VIP levels to unlock higher-paying projects and exclusive opportunities.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3 class="feature-title">Secure Platform</h3>
                    <p class="feature-description">Your data and earnings are protected with industry-leading security measures.</p>
                </div>
            </div>
        </section>

        <section class="vip-section">
            <h2 class="section-title" style="color: white;">VIP Membership Levels</h2>
            <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9);">Start at VIP 1 & 2 and earn your way up to higher levels</p>

            <div class="vip-grid">
                <div class="vip-card">
                    <div class="vip-level">VIP 1</div>
                    <div class="vip-rate">$10</div>
                    <p class="vip-description">Entry level projects<br>Available immediately</p>
                </div>

                <div class="vip-card">
                    <div class="vip-level">VIP 2</div>
                    <div class="vip-rate">$20</div>
                    <p class="vip-description">Intermediate projects<br>Available immediately</p>
                </div>

                <div class="vip-card">
                    <div class="vip-level">VIP 3</div>
                    <div class="vip-rate">$50</div>
                    <p class="vip-description">Advanced projects<br>Unlock with experience</p>
                </div>

                <div class="vip-card">
                    <div class="vip-level">VIP 4</div>
                    <div class="vip-rate">$100</div>
                    <p class="vip-description">Expert level projects<br>Unlock with experience</p>
                </div>

                <div class="vip-card">
                    <div class="vip-level">VIP 5</div>
                    <div class="vip-rate">$200</div>
                    <p class="vip-description">Premium projects<br>Unlock with experience</p>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <h2 class="cta-title">Ready to Start Earning?</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">Join now and get instant access to VIP 1 & VIP 2 projects</p>
            <a href="/register" class="btn btn-primary" style="padding: 1.25rem 3rem; font-size: 1.25rem;">Create Your Free Account</a>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <p><?php echo Helper::escape($settings['site_name'] ?? 'EarningsLLC'); ?> is a leading platform connecting skilled professionals with flexible remote work opportunities.</p>
                </div>

                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="/register">Sign Up</a>
                    <a href="/login">Login</a>
                    <a href="#features">Features</a>
                    <a href="#vip">VIP Levels</a>
                </div>

                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><strong>Email:</strong><br><?php echo Helper::escape($settings['contact_email'] ?? 'admin@earningsllc.com'); ?></p>
                    <p><strong>Phone:</strong><br><?php echo Helper::escape($settings['contact_phone'] ?? '+1 (555) 123-4567'); ?></p>
                </div>

                <div class="footer-section">
                    <h3>Address</h3>
                    <p><?php echo nl2br(Helper::escape($settings['contact_address'] ?? '123 Business Street\nSuite 100\nNew York, NY 10001\nUnited States')); ?></p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo Helper::escape($settings['site_name'] ?? 'EarningsLLC'); ?>. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>
