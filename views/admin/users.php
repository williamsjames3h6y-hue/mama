<?php $pageTitle = 'Manage Users - Admin'; ?>
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
            <h1>Manage Users</h1>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>VIP Level</th>
                        <th>Balance</th>
                        <th>Total Earned</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo Helper::escape($user['full_name']); ?></td>
                            <td><?php echo Helper::escape($user['email']); ?></td>
                            <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                            <td><span class="badge badge-vip">VIP <?php echo $user['vip_level'] ?? 1; ?></span></td>
                            <td><?php echo Helper::formatMoney($user['balance']); ?></td>
                            <td><?php echo Helper::formatMoney($user['total_earned']); ?></td>
                            <td><?php echo Helper::formatDate($user['created_at']); ?></td>
                            <td>
                                <a href="/admin/users/edit/<?php echo $user['id']; ?>" class="btn-small btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
