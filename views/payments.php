<?php $pageTitle = 'Payments - EarningsLLC'; ?>
<?php include 'header.php'; ?>

<div class="payments-container">
    <div class="page-header">
        <h1>Payment History</h1>
    </div>

    <div class="payments-table">
        <?php if (empty($payments)): ?>
            <p class="empty-state">No payment history available.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo Helper::formatDateTime($payment['created_at']); ?></td>
                            <td><span class="badge badge-<?php echo $payment['type']; ?>"><?php echo ucfirst($payment['type']); ?></span></td>
                            <td><?php echo Helper::escape($payment['description'] ?? 'N/A'); ?></td>
                            <td><?php echo Helper::formatMoney($payment['amount']); ?></td>
                            <td><span class="status-badge status-<?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
