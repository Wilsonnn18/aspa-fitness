<?php
// auth/login.php
require_once __DIR__ . '/../config/app.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        // Admin login uses the dedicated admin table
        $adminStmt = $conn->prepare('SELECT admin_id, name, email, password FROM admin WHERE email = ? LIMIT 1');
        $adminStmt->bind_param('s', $email);
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result();

        if ($adminResult && $adminResult->num_rows === 1) {
            $admin = $adminResult->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = (int)$admin['admin_id'];
                $_SESSION['name'] = $admin['name'];
                $_SESSION['email'] = $admin['email'];
                $_SESSION['role'] = 'admin';
                $_SESSION['loggedin'] = true;
                redirect('/admin/dashboard.php');
            }
            $error = 'Incorrect password.';
        } else {
            // User login uses users table only
            $userStmt = $conn->prepare('SELECT id,name,email,password FROM users WHERE email = ? LIMIT 1');
            $userStmt->bind_param('s', $email);
            $userStmt->execute();
            $userResult = $userStmt->get_result();

            if ($userResult && $userResult->num_rows === 1) {
                $user = $userResult->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = 'user';
                    $_SESSION['loggedin'] = true;
                    redirect('/user/dashboard.php');
                }
            }

            $error = 'Invalid email or password.';
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<section class="auth-layout">
    <div class="auth-panel">
        <h2 class="auth-title">Welcome Back</h2>
        <p class="auth-subtitle">Login to your ASPA Fitness account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3">Login</button>
        </form>

        <p class="auth-footer-text">Don't have an account? <a href="<?= BASE_URL ?>/auth/register.php">Register</a></p>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
