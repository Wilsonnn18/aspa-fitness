<?php
require_once __DIR__ . '/config/app.php';
?>

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="landing-page">
    <section class="hero-section">
        <div class="hero-content">
            <p class="hero-kicker">Train Better Every Day</p>
            <h1>Build strength, confidence, and consistency at ASPA Fitness</h1>
            <p class="hero-text">
                A simple and modern gym experience with curated workout plans, expert guidance, and a motivating environment.
            </p>
            <div class="hero-actions">
                <a class="btn btn-dark btn-lg" href="<?= BASE_URL ?>/auth/login.php">Login</a>
                <a class="btn btn-outline-dark btn-lg" href="<?= BASE_URL ?>/auth/register.php">Register</a>
            </div>
        </div>
        <div class="hero-image-wrap">
            <img
                src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1200&q=80"
                alt="Gym training session"
                class="hero-image"
            >
        </div>
    </section>

    <section class="gallery-section">
        <h2>Inside Our Gym</h2>
        <div class="gallery-grid">
            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1000&q=80" alt="Gym equipment">
            <img src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1000&q=80" alt="Weight lifting workout">
            <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=1000&q=80" alt="Cardio training">
        </div>
    </section>

    <section class="plans-section">
        <h2>Workout Plans</h2>
        <p class="plans-sub">Choose a plan and stay consistent with your fitness journey.</p>
        <div class="plan-grid">
            <article class="plan-card">
                <h3>Starter Plan</h3>
                <p>3 days/week full-body program focused on technique and consistency.</p>
                <span class="plan-tag">Beginner</span>
            </article>
            <article class="plan-card">
                <h3>Strength Builder</h3>
                <p>4 days/week split with progressive overload for faster strength gains.</p>
                <span class="plan-tag">Intermediate</span>
            </article>
            <article class="plan-card">
                <h3>Lean Athlete</h3>
                <p>5 days/week hybrid routine combining weights, HIIT, and mobility work.</p>
                <span class="plan-tag">Advanced</span>
            </article>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
