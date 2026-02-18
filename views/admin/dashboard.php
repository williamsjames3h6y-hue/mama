<?php $pageTitle = 'Admin Dashboard - DataOptimize Pro'; ?>
<?php include __DIR__ . '/../header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav class="admin-nav">
            <a href="/admin" class="admin-nav-link active">Dashboard</a>
            <a href="/admin/users" class="admin-nav-link">Users</a>
            <a href="/admin/projects" class="admin-nav-link">Projects</a>
            <a href="/admin/payments" class="admin-nav-link">Payments</a>
            <a href="/admin/settings" class="admin-nav-link">Settings</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="page-header">
            <h1>Dashboard Overview</h1>
        </div>

        <div class="admin-stats">
            <div class="admin-stat-card">
                <h3>Total Users</h3>
                <p class="stat-number"><?php echo $stats['total_users']; ?></p>
            </div>
            <div class="admin-stat-card">
                <h3>Active Projects</h3>
                <p class="stat-number"><?php echo $stats['active_projects']; ?></p>
            </div>
            <div class="admin-stat-card">
                <h3>Pending Payments</h3>
                <p class="stat-number"><?php echo $stats['pending_payments']; ?></p>
            </div>
            <div class="admin-stat-card">
                <h3>Total Paid Out</h3>
                <p class="stat-number"><?php echo Helper::formatMoney($stats['total_paid']); ?></p>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="activity-item">
                        <p><?php echo Helper::escape($activity['message']); ?></p>
                        <span class="activity-time"><?php echo Helper::formatDateTime($activity['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
