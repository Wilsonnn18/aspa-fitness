<?php
// admin/manage_plans.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['plan_name'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');

    if ($name !== '' && $duration > 0) {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE membership_plans SET plan_name = ?, duration = ?, price = ?, description = ? WHERE id = ?');
            $stmt->bind_param('sidsi', $name, $duration, $price, $desc, $id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare('INSERT INTO membership_plans (plan_name,duration,price,description) VALUES (?,?,?,?)');
            $stmt->bind_param('sids', $name, $duration, $price, $desc);
            $stmt->execute();
        }
    }
}

$deleteId = (int)($_GET['delete'] ?? 0);
if ($deleteId > 0) {
    $stmt = $conn->prepare('DELETE FROM membership_plans WHERE id = ?');
    $stmt->bind_param('i', $deleteId);
    $stmt->execute();
}

$plans = [];
$res = $conn->query('SELECT * FROM membership_plans');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<h2>Manage Membership Plans</h2>
<h4>Add / Edit plan</h4>
<form method="post" class="mb-4">
    <input type="hidden" name="id" id="plan-id">
    <div class="form-row">
        <div class="col"><input class="form-control" name="plan_name" id="plan-name" placeholder="Name" required></div>
        <div class="col"><input type="number" class="form-control" name="duration" id="plan-duration" placeholder="Duration (days)" required></div>
        <div class="col"><input type="number" step="0.01" class="form-control" name="price" id="plan-price" placeholder="Price" required></div>
    </div>
    <div class="form-group mt-2">
        <textarea class="form-control" name="description" id="plan-desc" placeholder="Description"></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
</form>
<table class="table table-bordered" id="plans-table">
    <thead><tr><th>ID</th><th>Name</th><th>Duration</th><th>Price</th><th>Description</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($plans as $p): ?>
        <tr data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['plan_name'], ENT_QUOTES) ?>" data-duration="<?= $p['duration'] ?>" data-price="<?= $p['price'] ?>" data-desc="<?= htmlspecialchars($p['description'], ENT_QUOTES) ?>">
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['plan_name']) ?></td>
            <td><?= $p['duration'] ?></td>
            <td><?= number_format($p['price'], 2) ?></td>
            <td><?= htmlspecialchars($p['description']) ?></td>
            <td><a class="btn btn-sm btn-secondary edit-plan" href="#">Edit</a> <a class="btn btn-sm btn-danger" href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete?');">Delete</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
document.querySelectorAll('.edit-plan').forEach(function (el) {
    el.addEventListener('click', function (e) {
        e.preventDefault();
        var tr = this.closest('tr');
        document.getElementById('plan-id').value = tr.dataset.id;
        document.getElementById('plan-name').value = tr.dataset.name;
        document.getElementById('plan-duration').value = tr.dataset.duration;
        document.getElementById('plan-price').value = tr.dataset.price;
        document.getElementById('plan-desc').value = tr.dataset.desc;
    });
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
