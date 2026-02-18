<?php $pageTitle = 'Site Settings - Admin'; ?>
<?php include __DIR__ . '/../header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/users" class="admin-nav-link">Users</a>
            <a href="/admin/projects" class="admin-nav-link">Projects</a>
            <a href="/admin/payments" class="admin-nav-link">Payments</a>
            <a href="/admin/settings" class="admin-nav-link active">Settings</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="page-header">
            <h1>Site Settings</h1>
        </div>

        <form method="POST" action="/admin/settings/update" class="settings-form">
            <div class="settings-section">
                <h2>General Settings</h2>

                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name"
                           value="<?php echo Helper::escape($settings['site_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="<?php echo Helper::escape($settings['contact_email'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="settings-section">
                <h2>Currency Settings</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="site_currency">Currency Code</label>
                        <input type="text" id="site_currency" name="site_currency"
                               value="<?php echo Helper::escape($settings['site_currency'] ?? 'USD'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="site_currency_symbol">Currency Symbol</label>
                        <input type="text" id="site_currency_symbol" name="site_currency_symbol"
                               value="<?php echo Helper::escape($settings['site_currency_symbol'] ?? '$'); ?>" required>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h2>Payment Settings</h2>

                <div class="form-group">
                    <label for="payment_gateway">Payment Gateway</label>
                    <select id="payment_gateway" name="payment_gateway">
                        <option value="stripe" <?php echo ($settings['payment_gateway'] ?? '') === 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                        <option value="paypal" <?php echo ($settings['payment_gateway'] ?? '') === 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                        <option value="manual" <?php echo ($settings['payment_gateway'] ?? '') === 'manual' ? 'selected' : ''; ?>>Manual</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="min_withdrawal">Minimum Withdrawal Amount</label>
                        <input type="number" id="min_withdrawal" name="min_withdrawal" step="0.01"
                               value="<?php echo Helper::escape($settings['min_withdrawal'] ?? '50'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="referral_bonus">Referral Bonus Amount</label>
                        <input type="number" id="referral_bonus" name="referral_bonus" step="0.01"
                               value="<?php echo Helper::escape($settings['referral_bonus'] ?? '300'); ?>" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-large">Save Settings</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
