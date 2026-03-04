<?php
// user/dashboard.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$quickLinks = [
    [
        'title' => 'Membership Plans',
        'description' => 'Review available plans and pricing before you subscribe.',
        'meta' => 'Choose the best plan for your goals',
        'href' => BASE_URL . '/user/view_plans.php',
        'cta' => 'View Plans',
        'tone' => 'tone-plans',
    ],
    [
        'title' => 'My Membership',
        'description' => 'Track your latest plan purchase, access period, and status.',
        'meta' => 'Monitor your membership details',
        'href' => BASE_URL . '/user/subscription.php',
        'cta' => 'Check Membership',
        'tone' => 'tone-subscription',
    ],
    [
        'title' => 'Workout Plans',
        'description' => 'Browse workout routines based on your fitness level.',
        'meta' => 'Build your weekly workout flow',
        'href' => BASE_URL . '/user/workouts.php',
        'cta' => 'Explore Workouts',
        'tone' => 'tone-workouts',
    ],
];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<section class="user-dashboard">
    <div class="dashboard-hero">
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?></h2>
        <p class="dashboard-subtext">Manage your fitness journey from one place with quick actions and clear next steps.</p>
    </div>

    <div class="dashboard-grid">
        <?php foreach ($quickLinks as $link): ?>
            <article class="dashboard-card <?= htmlspecialchars($link['tone']) ?>">
                <span class="dashboard-card-meta"><?= htmlspecialchars($link['meta']) ?></span>
                <h3><?= htmlspecialchars($link['title']) ?></h3>
                <p><?= htmlspecialchars($link['description']) ?></p>
                <a class="btn btn-primary" href="<?= htmlspecialchars($link['href']) ?>"><?= htmlspecialchars($link['cta']) ?></a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
