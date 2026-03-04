<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="admin-dashboard page-section">
    <div class="page-header">
        <div>
            <h2>Admin Dashboard</h2>
            <p class="page-header-sub">Manage your gym from one place</p>
        </div>
    </div>

    <div class="admin-nav-grid">
        <a href="<?= BASE_URL ?>/admin/manage_users.php" class="admin-nav-card">
            <span class="admin-nav-card-icon"><i class="fa-solid fa-users"></i></span>
            <h5>Manage Users</h5>
            <p>View, add, and remove member accounts</p>
        </a>
        <a href="<?= BASE_URL ?>/admin/manage_plans.php" class="admin-nav-card">
            <span class="admin-nav-card-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <h5>Membership Plans</h5>
            <p>Create and update gym membership packages</p>
        </a>
        <a href="<?= BASE_URL ?>/admin/manage_workouts.php" class="admin-nav-card">
            <span class="admin-nav-card-icon"><i class="fa-solid fa-dumbbell"></i></span>
            <h5>Workout Plans</h5>
            <p>Manage fitness programs in the database</p>
        </a>
        <a href="<?= BASE_URL ?>/admin/payment_analytics.php" class="admin-nav-card">
            <span class="admin-nav-card-icon"><i class="fa-solid fa-chart-bar"></i></span>
            <h5>Payment Analytics</h5>
            <p>Track revenue, member activity, and transactions</p>
        </a>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
