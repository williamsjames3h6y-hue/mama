<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'EarningsLLC'; ?></title>
    <meta name="description" content="Professional data optimization and earnings platform">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php if (Session::isLoggedIn()): ?>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/home" class="nav-brand">EarningsLLC</a>
            <div class="nav-menu">
                <a href="/home" class="nav-link">Home</a>
                <a href="/projects" class="nav-link">Projects</a>
                <a href="/payments" class="nav-link">Payments</a>
                <div class="nav-user">
                    <span><?php echo Helper::escape(Session::get('user_name')); ?></span>
                    <?php if (Session::isAdmin()): ?>
                    <a href="/admin" class="nav-link admin-link">Admin</a>
                    <?php endif; ?>
                    <a href="/logout" class="nav-link">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <div class="main-content">
        <?php
        $successMsg = Session::getFlash('success');
        $errorMsg = Session::getFlash('error');
        if ($successMsg): ?>
            <div class="alert alert-success"><?php echo Helper::escape($successMsg); ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><?php echo Helper::escape($errorMsg); ?></div>
        <?php endif; ?>
