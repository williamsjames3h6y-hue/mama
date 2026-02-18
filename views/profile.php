<?php $pageTitle = 'Profile - DataOptimize Pro'; ?>
<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="page-header">
        <h1>My Profile</h1>
    </div>

    <div class="profile-grid">
        <div class="profile-section">
            <h2>Account Information</h2>
            <div class="info-group">
                <label>Full Name</label>
                <p><?php echo Helper::escape($user['full_name']); ?></p>
            </div>
            <div class="info-group">
                <label>Email</label>
                <p><?php echo Helper::escape($user['email']); ?></p>
            </div>
            <div class="info-group">
                <label>Referral Code</label>
                <p><?php echo Helper::escape($user['referral_code']); ?></p>
            </div>
            <div class="info-group">
                <label>Member Since</label>
                <p><?php echo Helper::formatDate($user['created_at']); ?></p>
            </div>
        </div>

        <div class="profile-section">
            <h2>Balance & Earnings</h2>
            <div class="balance-card">
                <div class="balance-item">
                    <label>Current Balance</label>
                    <h3><?php echo Helper::formatMoney($user['balance']); ?></h3>
                </div>
                <div class="balance-item">
                    <label>Total Earned</label>
                    <h3><?php echo Helper::formatMoney($user['total_earned']); ?></h3>
                </div>
            </div>

            <div class="balance-actions">
                <button onclick="showDepositModal()" class="btn btn-success">Deposit Funds</button>
                <button onclick="showWithdrawModal()" class="btn btn-primary">Withdraw</button>
            </div>
        </div>
    </div>
</div>

<div id="depositModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('depositModal')">&times;</span>
        <h2>Deposit Funds</h2>
        <form method="POST" action="/profile/deposit">
            <div class="form-group">
                <label for="deposit_amount">Amount (<?php echo $settings['site_currency_symbol'] ?? '$'; ?>)</label>
                <input type="number" id="deposit_amount" name="amount" step="0.01" min="1" required>
            </div>
            <button type="submit" class="btn btn-success btn-block">Submit Deposit Request</button>
        </form>
    </div>
</div>

<div id="withdrawModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('withdrawModal')">&times;</span>
        <h2>Withdraw Funds</h2>
        <p class="modal-info">Minimum withdrawal: <?php echo Helper::formatMoney($settings['min_withdrawal'] ?? 50); ?></p>
        <form method="POST" action="/profile/withdraw">
            <div class="form-group">
                <label for="withdraw_amount">Amount (<?php echo $settings['site_currency_symbol'] ?? '$'; ?>)</label>
                <input type="number" id="withdraw_amount" name="amount" step="0.01"
                       min="<?php echo $settings['min_withdrawal'] ?? 50; ?>"
                       max="<?php echo $user['balance']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit Withdrawal Request</button>
        </form>
    </div>
</div>

<script>
function showDepositModal() {
    document.getElementById('depositModal').style.display = 'flex';
}

function showWithdrawModal() {
    document.getElementById('withdrawModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include 'footer.php'; ?>
