<?php
// auth/register.php
require_once __DIR__ . '/../config/app.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $password2 === '') {
        $error = 'All fields are required.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $stmt = $conn->prepare('INSERT INTO users (name,email,phone,password,role) VALUES (?,?,?,?,?)');
            $stmt->bind_param('sssss', $name, $email, $phone, $hash, $role);
            if ($stmt->execute()) {
                redirect('/auth/login.php');
            }

            $error = 'Registration failed, please try again.';
        }
    }
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Register</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="password2" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
