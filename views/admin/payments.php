<?php $pageTitle = 'Manage Payments - Admin'; ?>
<?php include __DIR__ . '/../header.php'; ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav class="admin-nav">
            <a href="/admin" class="admin-nav-link">Dashboard</a>
            <a href="/admin/users" class="admin-nav-link">Users</a>
            <a href="/admin/projects" class="admin-nav-link">Projects</a>
            <a href="/admin/payments" class="admin-nav-link active">Payments</a>
            <a href="/admin/settings" class="admin-nav-link">Settings</a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="page-header">
            <h1>Manage Payments</h1>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo Helper::escape($payment['user_email'] ?? 'N/A'); ?></td>
                            <td><span class="badge badge-<?php echo $payment['type']; ?>"><?php echo ucfirst($payment['type']); ?></span></td>
                            <td><?php echo Helper::formatMoney($payment['amount']); ?></td>
                            <td><span class="status-badge status-<?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                            <td><?php echo Helper::escape($payment['description'] ?? 'N/A'); ?></td>
                            <td><?php echo Helper::formatDateTime($payment['created_at']); ?></td>
                            <td>
                                <?php if ($payment['status'] === 'pending'): ?>
                                    <form method="POST" action="/admin/payments/approve" class="inline-form">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <button type="submit" class="btn-small btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="/admin/payments/reject" class="inline-form">
                                        <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                        <button type="submit" class="btn-small btn-danger">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
