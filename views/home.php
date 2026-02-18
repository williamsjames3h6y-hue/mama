<?php $pageTitle = 'Dashboard - DataOptimize Pro'; ?>
<?php include 'header.php'; ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo Helper::escape($user['full_name']); ?></h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">
                Awaiting payout
                <span class="info-icon" title="Pending payments">ⓘ</span>
            </div>
            <div class="stat-value"><?php echo Helper::formatMoney($stats['awaiting_payout']); ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">
                Total paid
                <span class="info-icon" title="All completed payments">ⓘ</span>
            </div>
            <div class="stat-value"><?php echo Helper::formatMoney($stats['total_paid']); ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">
                Tasks this week
                <span class="info-icon" title="Active tasks">ⓘ</span>
            </div>
            <div class="stat-value"><?php echo $stats['tasks_week']; ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-label">
                Payable hours
                <span class="info-icon" title="Hours worked">ⓘ</span>
            </div>
            <div class="stat-value"><?php echo number_format($stats['payable_hours'], 2); ?></div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="/profile" class="action-card">
            <h3>Update your profile</h3>
            <span class="arrow">→</span>
        </a>
    </div>

    <div class="referral-section">
        <div class="referral-card">
            <div class="referral-icon">💰</div>
            <div class="referral-content">
                <h3>Refer and earn up to <?php echo Helper::formatMoney($settings['referral_bonus'] ?? 300); ?></h3>
                <p>Earn money by inviting others to the platform.</p>
            </div>
        </div>
        <div class="referral-code">
            <p>Your referral code: <strong><?php echo Helper::escape($user['referral_code']); ?></strong></p>
            <button onclick="copyReferralLink()" class="btn btn-secondary">Copy referral link</button>
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
