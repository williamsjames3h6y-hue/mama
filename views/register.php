<?php $pageTitle = 'Register - EarningsLLC'; ?>
<?php include 'header.php'; ?>

<div class="register-page">
    <div class="register-images">
        <div class="register-image-item" style="animation-delay: 0s;">
            <img src="/assets/images/1.jpg" alt="Professional workspace">
            <div class="image-overlay">
                <h3>Professional Work</h3>
                <p>Join thousands of professionals earning online</p>
            </div>
        </div>
        <div class="register-image-item" style="animation-delay: 0.2s;">
            <img src="/assets/images/2.jpg" alt="Data analytics">
            <div class="image-overlay">
                <h3>Data Excellence</h3>
                <p>Work with cutting-edge projects and technologies</p>
            </div>
        </div>
        <div class="register-image-item" style="animation-delay: 0.4s;">
            <img src="/assets/images/3.jpg" alt="Business growth">
            <div class="image-overlay">
                <h3>Grow Your Career</h3>
                <p>Unlimited opportunities for skilled professionals</p>
            </div>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-box register-form-box">
            <h1>Create Account</h1>
            <p class="auth-subtitle">Join our professional earnings platform</p>

            <form method="POST" action="/register" class="auth-form">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <p class="auth-footer">
                Already have an account? <a href="/login">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
