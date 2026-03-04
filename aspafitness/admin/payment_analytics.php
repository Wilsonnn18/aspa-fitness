<?php
// admin/payment_analytics.php - Payment analytics and reports
require_once __DIR__ . '/../config/app.php';
require_login('admin');

// Get payment statistics
$stats = [];

$result = $conn->query('SELECT SUM(amount) as total FROM payments WHERE payment_status = "completed"');
$stats['total_revenue'] = $result->fetch_assoc()['total'] ?? 0;

$result = $conn->query('SELECT COUNT(*) as count FROM payments');
$stats['total_payments'] = $result->fetch_assoc()['count'] ?? 0;

$result = $conn->query('SELECT COUNT(*) as count FROM payments WHERE payment_status = "pending"');
$stats['pending_payments'] = $result->fetch_assoc()['count'] ?? 0;

$result = $conn->query('SELECT COUNT(*) as count FROM payments WHERE payment_status = "failed"');
$stats['failed_payments'] = $result->fetch_assoc()['count'] ?? 0;

$result = $conn->query('SELECT COUNT(DISTINCT user_id) as count FROM payments WHERE payment_status = "completed"');
$stats['active_members'] = $result->fetch_assoc()['count'] ?? 0;

// Revenue by plan
$planRevenue = [];
$res = $conn->query('SELECT m.plan_name, COUNT(p.id) as count, SUM(p.amount) as total FROM payments p JOIN membership_plans m ON p.plan_id = m.id WHERE p.payment_status = "completed" GROUP BY m.id, m.plan_name ORDER BY total DESC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $planRevenue[] = $row;
    }
}

// Recent payments
$recentPayments = [];
$res = $conn->query('SELECT p.*, u.name as user_name, m.plan_name, DATE_ADD(p.payment_date, INTERVAL m.duration DAY) AS plan_end_date FROM payments p JOIN users u ON p.user_id = u.id JOIN membership_plans m ON p.plan_id = m.id ORDER BY p.payment_date DESC LIMIT 10');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recentPayments[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>Payment Analytics</h2>
            <p class="page-header-sub">Revenue and transaction history</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-revenue">
                <div class="stat-card-label">Total Revenue</div>
                <div class="stat-card-value">Rs <?= number_format($stats['total_revenue'], 0) ?></div>
                <span class="stat-card-icon"><i class="fa-solid fa-coins"></i></span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-active">
                <div class="stat-card-label">Active Members</div>
                <div class="stat-card-value"><?= $stats['active_members'] ?></div>
                <span class="stat-card-icon"><i class="fa-solid fa-circle-check"></i></span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-payments">
                <div class="stat-card-label">Total Payments</div>
                <div class="stat-card-value"><?= $stats['total_payments'] ?></div>
                <span class="stat-card-icon"><i class="fa-solid fa-file-lines"></i></span>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-failed">
                <div class="stat-card-label">Failed Payments</div>
                <div class="stat-card-value"><?= $stats['failed_payments'] ?></div>
                <span class="stat-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
        </div>
    </div>

    <!-- Revenue by Plan -->
    <?php if (!empty($planRevenue)): ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Revenue by Plan</div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Transactions</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planRevenue as $record): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($record['plan_name']) ?></strong></td>
                                        <td><?= $record['count'] ?></td>
                                        <td>Rs <?= number_format($record['total'], 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header">Recent Payments</div>
        <div class="card-body p-0">
            <?php if (empty($recentPayments)): ?>
                <p class="text-muted p-3 mb-0">No payments on record yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Plan End</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td><?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></td>
                                    <td><strong><?= htmlspecialchars($payment['user_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($payment['plan_name']) ?></td>
                                    <td><?= date('M d, Y', strtotime($payment['plan_end_date'])) ?></td>
                                    <td>Rs <?= number_format($payment['amount'], 0) ?></td>
                                    <td>
                                        <?php
                                            $statusClass = match($payment['payment_status']) {
                                                'completed' => 'active',
                                                'pending'   => 'pending',
                                                default     => 'expired',
                                            };
                                        ?>
                                        <span class="status-pill <?= $statusClass ?>">
                                            <?= ucfirst($payment['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td><code style="color:var(--muted);font-size:0.8rem;"><?= htmlspecialchars($payment['transaction_id']) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
