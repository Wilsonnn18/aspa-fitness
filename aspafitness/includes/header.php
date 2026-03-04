<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASPA Fitness</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">ASPA Fitness</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ml-auto align-items-center">
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/manage_plans.php">Plans</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/manage_workouts.php">Workouts</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/payment_analytics.php">Analytics</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/view_plans.php">Plans</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/workouts.php">Workouts</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/user/subscription.php">Membership</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['name'])): ?>
                    <li class="nav-item"><span class="nav-username"><?= htmlspecialchars($_SESSION['name']) ?></span></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/auth/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<div class="container mt-4">
