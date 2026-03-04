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
                src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80"
                alt="Male bodybuilder training"
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

    <section class="landing-description">
        <h2><br>Your journey starts from here</h2>
        <p>
            At ASPA Fitness, we believe fitness is more than just lifting weights. It is about building confidence,
            discipline, and a healthier lifestyle. Join us today and take the first step toward becoming a stronger
            version of yourself.

        </p>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>





