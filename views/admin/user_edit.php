<?php $pageTitle = 'Edit User - Admin'; ?>
<?php include __DIR__ . '/../header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/users" class="admin-nav-link active">Users</a>
            <a href="/admin/projects" class="admin-nav-link">Projects</a>
            <a href="/admin/payments" class="admin-nav-link">Payments</a>
            <a href="/admin/settings" class="admin-nav-link">Settings</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="page-header">
            <h1>Edit User</h1>
            <a href="/admin/users" class="btn btn-secondary">Back to Users</a>
        </div>

        <?php if ($user): ?>
            <form method="POST" action="/admin/users/update" class="settings-form">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                <div class="settings-section">
                    <h2>User Information</h2>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                               value="<?php echo Helper::escape($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo Helper::escape($user['email']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role">
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vip_level">VIP Level</label>
                            <select id="vip_level" name="vip_level">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($user['vip_level'] ?? 1) == $i ? 'selected' : ''; ?>>
                                        VIP Level <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Financial Information</h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="balance">Balance ($)</label>
                            <input type="number" id="balance" name="balance" step="0.01"
                                   value="<?php echo Helper::escape($user['balance']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="total_earned">Total Earned ($)</label>
                            <input type="number" id="total_earned" name="total_earned" step="0.01"
                                   value="<?php echo Helper::escape($user['total_earned']); ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-large">Update User</button>
            </form>
        <?php else: ?>
            <p class="empty-state">User not found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
