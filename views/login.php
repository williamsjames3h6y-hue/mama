<?php $pageTitle = 'Login - DataOptimize Pro'; ?>
<?php include 'header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h1>Welcome Back</h1>
        <p class="auth-subtitle">Login to your account</p>

        <form method="POST" action="/login" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="auth-footer">
            Don't have an account? <a href="/register">Register here</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
