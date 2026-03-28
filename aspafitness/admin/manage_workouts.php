<?php
// admin/manage_workouts.php
require_once __DIR__ . '/../config/app.php';
require_login('admin');

$goalColumn = $conn->query("SHOW COLUMNS FROM workout_plans LIKE 'goal'");
if ($goalColumn && $goalColumn->num_rows === 0) {
    $conn->query("ALTER TABLE workout_plans ADD COLUMN goal ENUM('weight_loss','weight_gain','lean_muscle') DEFAULT 'weight_loss'");
}

$descriptionColumn = $conn->query("SHOW COLUMNS FROM workout_plans LIKE 'description'");
if ($descriptionColumn && $descriptionColumn->num_rows === 0) {
    $conn->query("ALTER TABLE workout_plans ADD COLUMN description TEXT NULL");
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $level = $_POST['level'] ?? 'beginner';
    $goal = $_POST['goal'] ?? 'weight_loss';
    $description = trim($_POST['description'] ?? '');

    $allowedLevels = ['beginner', 'intermediate', 'advanced'];
    $allowedGoals = ['weight_loss', 'weight_gain', 'lean_muscle'];

    if (!in_array($level, $allowedLevels, true)) $level = 'beginner';
    if (!in_array($goal, $allowedGoals, true)) $goal = 'weight_loss';

    if ($title !== '') {
        if ($id > 0) {
            $stmt = $conn->prepare('UPDATE workout_plans SET title = ?, level = ?, goal = ?, description = ? WHERE id = ?');
            $stmt->bind_param('ssssi', $title, $level, $goal, $description, $id);
            $stmt->execute();
            $success = 'Workout plan updated.';
        } else {
            $stmt = $conn->prepare('INSERT INTO workout_plans (title,level,goal,description) VALUES (?,?,?,?)');
            $stmt->bind_param('ssss', $title, $level, $goal, $description);
            $stmt->execute();
            $success = 'Workout plan created.';
        }
    }
}

$deleteId = (int)($_GET['delete'] ?? 0);
if ($deleteId > 0) {
    $stmt = $conn->prepare('DELETE FROM workout_plans WHERE id = ?');
    $stmt->bind_param('i', $deleteId);
    $stmt->execute();
}

$plans = [];
$res = $conn->query('SELECT * FROM workout_plans ORDER BY id DESC');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if (!array_key_exists('goal', $row)) {
            $row['goal'] = 'weight_loss';
        }
        if (!array_key_exists('level', $row) || $row['level'] === null || $row['level'] === '') {
            $row['level'] = 'beginner';
        }
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>Manage Workout Plans</h2>
            <p class="page-header-sub">Create and edit fitness programs</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Create / Edit Form -->
    <div class="card mb-4">
        <div class="card-header" id="workout-form-heading">Add New Workout Plan</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="id" id="wp-id">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title" id="wp-title" placeholder="Plan Title" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Level</label>
                        <select name="level" id="wp-level" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Goal</label>
                        <select name="goal" id="wp-goal" class="form-control">
                            <option value="weight_loss">Weight Loss</option>
                            <option value="weight_gain">Weight Gain</option>
                            <option value="lean_muscle">Lean Muscle</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="wp-description" rows="4" placeholder="Workout plan details"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Save Plan</button>
                    <button class="btn btn-secondary" type="reset" id="wp-reset-btn">Clear Form</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Plans Table -->
    <div class="card">
        <div class="card-header">Existing Workout Plans (<?= count($plans) ?>)</div>
        <div class="card-body p-0">
            <?php if (empty($plans)): ?>
                <p class="text-muted p-3 mb-0">No workout plans yet. Create one above.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Level</th>
                                <th>Goal</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($plans as $p): ?>
                            <tr data-id="<?= $p['id'] ?>"
                                data-title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>"
                                data-level="<?= htmlspecialchars($p['level']) ?>"
                                data-goal="<?= htmlspecialchars($p['goal']) ?>"
                                data-description="<?= htmlspecialchars($p['description'] ?? '', ENT_QUOTES) ?>">
                                <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                                <td>
                                    <span class="status-pill inactive"><?= ucfirst(htmlspecialchars($p['level'])) ?></span>
                                </td>
                                <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $p['goal']))) ?></td>
                                <td><?= nl2br(htmlspecialchars(substr($p['description'] ?? '', 0, 120))) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-secondary edit-wp" href="#">Edit</a>
                                    <a class="btn btn-sm btn-danger" href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this plan?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.edit-wp').forEach(function(el) {
    el.addEventListener('click', function(e) {
        e.preventDefault();
        var tr = this.closest('tr');
        document.getElementById('wp-id').value = tr.dataset.id;
        document.getElementById('wp-title').value = tr.dataset.title;
        document.getElementById('wp-level').value = tr.dataset.level;
        document.getElementById('wp-goal').value = tr.dataset.goal;
        document.getElementById('wp-description').value = tr.dataset.description || '';
        document.getElementById('workout-form-heading').textContent = 'Edit Workout Plan';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

document.getElementById('wp-reset-btn').addEventListener('click', function() {
    document.getElementById('wp-id').value = '';
    document.getElementById('wp-description').value = '';
    document.getElementById('workout-form-heading').textContent = 'Add New Workout Plan';
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
