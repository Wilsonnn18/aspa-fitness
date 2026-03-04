<?php
// admin/manage_plans.php - Manage membership plans
require_once __DIR__ . '/../config/app.php';
require_login('admin');

$success = $error = '';

// Create new plan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $planName = trim($_POST['plan_name'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if (empty($planName) || $duration <= 0 || $price < 0) {
            $error = 'Please fill in all required fields correctly.';
        } else {
            $stmt = $conn->prepare('INSERT INTO membership_plans (plan_name, duration, price, description) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('sids', $planName, $duration, $price, $description);
            if ($stmt->execute()) {
                $success = 'Plan created successfully!';
            } else {
                $error = 'Error creating plan: ' . $conn->error;
            }
        }
    }

    // Update plan
    elseif ($_POST['action'] === 'update') {
        $planId = (int)($_POST['plan_id'] ?? 0);
        $planName = trim($_POST['plan_name'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if ($planId <= 0 || empty($planName) || $duration <= 0 || $price < 0) {
            $error = 'Please fill in all required fields correctly.';
        } else {
            $stmt = $conn->prepare('UPDATE membership_plans SET plan_name = ?, duration = ?, price = ?, description = ? WHERE id = ?');
            $stmt->bind_param('sidsi', $planName, $duration, $price, $description, $planId);
            if ($stmt->execute()) {
                $success = 'Plan updated successfully!';
            } else {
                $error = 'Error updating plan: ' . $conn->error;
            }
        }
    }

    // Delete plan
    elseif ($_POST['action'] === 'delete') {
        $planId = (int)($_POST['plan_id'] ?? 0);
        if ($planId > 0) {
            // Prevent deleting plans that already have payment history
            $stmt = $conn->prepare('SELECT COUNT(*) as cnt FROM payments WHERE plan_id = ?');
            $stmt->bind_param('i', $planId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result['cnt'] > 0) {
                $error = 'Cannot delete plan with payment history.';
            } else {
                $stmt = $conn->prepare('DELETE FROM membership_plans WHERE id = ?');
                $stmt->bind_param('i', $planId);
                if ($stmt->execute()) {
                    $success = 'Plan deleted successfully!';
                } else {
                    $error = 'Error deleting plan: ' . $conn->error;
                }
            }
        }
    }
}

// Get plan edit data if editing
$editPlan = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT id, plan_name, duration, price, description FROM membership_plans WHERE id = ?');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $editPlan = $result->fetch_assoc();
    }
}

// Get all plans
$plans = [];
$sql = 'SELECT id, plan_name, duration, price, description FROM membership_plans ORDER BY duration ASC';
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $plans[] = $row;
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Membership Plans</h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Form to create/edit plan -->
    <div class="card mb-4">
        <div class="card-header">
            <?= $editPlan ? 'Edit Plan' : 'Create New Plan' ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="<?= $editPlan ? 'update' : 'create' ?>">
                <?php if ($editPlan): ?>
                    <input type="hidden" name="plan_id" value="<?= $editPlan['id'] ?>">
                <?php endif; ?>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="plan_name" class="form-label">Plan Name *</label>
                        <input type="text" class="form-control" id="plan_name" name="plan_name" 
                               value="<?= $editPlan ? htmlspecialchars($editPlan['plan_name']) : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="duration" class="form-label">Duration (days) *</label>
                        <input type="number" class="form-control" id="duration" name="duration" 
                               value="<?= $editPlan ? $editPlan['duration'] : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="price" class="form-label">Price ($) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                               value="<?= $editPlan ? $editPlan['price'] : '' ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= $editPlan ? htmlspecialchars($editPlan['description']) : '' ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $editPlan ? 'Update Plan' : 'Create Plan' ?>
                    </button>
                    <?php if ($editPlan): ?>
                        <a href="<?= BASE_URL ?>/admin/manage_plans.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- List of existing plans -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Existing Plans (<?= count($plans) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($plans)): ?>
                <p class="text-muted">No plans found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Plan Name</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $plan): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($plan['plan_name']) ?></strong></td>
                                    <td><?= $plan['duration'] ?> days</td>
                                    <td>Rs <?= number_format($plan['price'], 0) ?></td>
                                    <td><?= htmlspecialchars(substr($plan['description'], 0, 50)) ?>...</td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/admin/manage_plans.php?edit=<?= $plan['id'] ?>" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
