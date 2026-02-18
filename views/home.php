<?php $pageTitle = 'Dashboard - EarningsLLC'; ?>
<?php include 'header.php'; ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo Helper::escape($user['full_name']); ?></h1>
        <p>Here's what's happening with your account today | <span class="badge badge-vip" style="font-size: 1rem; padding: 0.5rem 1rem;">VIP Level <?php echo $user['vip_level'] ?? 1; ?></span></p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Awaiting payout</div>
            <div class="stat-value"><?php echo Helper::formatMoney($stats['awaiting_payout']); ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total paid</div>
            <div class="stat-value success"><?php echo Helper::formatMoney($stats['total_paid']); ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Tasks this week</div>
            <div class="stat-value"><?php echo $stats['tasks_week']; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Payable hours</div>
            <div class="stat-value primary"><?php echo number_format($stats['payable_hours'], 2); ?></div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="/profile" class="action-card">
            <div>
                <h3>Manage your balance</h3>
                <p>View your earnings and request payouts</p>
            </div>
            <span class="arrow">→</span>
        </a>

        <a href="/projects" class="action-card">
            <div>
                <h3>Browse available projects</h3>
                <p>Find new opportunities that match your skills</p>
            </div>
            <span class="arrow">→</span>
        </a>

        <a href="/payments" class="action-card">
            <div>
                <h3>Payment history</h3>
                <p>Track all your transactions and earnings</p>
            </div>
            <span class="arrow">→</span>
        </a>
    </div>

    <div class="referral-section">
        <div class="referral-card">
            <div class="referral-icon">🎁</div>
            <div class="referral-content">
                <h3>Refer and earn up to <?php echo Helper::formatMoney($settings['referral_bonus'] ?? 300); ?></h3>
                <p>Share your unique referral link and earn bonuses when others join the platform</p>
            </div>
        </div>
        <div class="referral-code">
            <div>
                <label style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem; display: block;">Your referral code</label>
                <p style="font-size: 1.125rem; font-weight: 600; margin: 0;"><strong><?php echo Helper::escape($user['referral_code']); ?></strong></p>
            </div>
            <button onclick="copyReferralLink()" class="btn btn-primary">Copy link</button>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const link = window.location.origin + '/register?ref=<?php echo $user['referral_code']; ?>';
    navigator.clipboard.writeText(link);
    alert('Referral link copied!');
}
</script>

<?php include 'footer.php'; ?>
