<?php
// user/checkout.php - Checkout page with order review
require_once __DIR__ . '/../config/app.php';
require_login('user');

$planId = (int)($_GET['id'] ?? 0);
if ($planId <= 0) {
    redirect('/user/view_plans.php');
}

$stmt = $conn->prepare('SELECT id, plan_name, duration, price, description FROM membership_plans WHERE id = ?');
$stmt->bind_param('i', $planId);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();

if (!$plan) {
    redirect('/user/view_plans.php');
}

// Get current user info
$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT name, email, phone FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Calculate dates
$startDate = date('Y-m-d');
$endDate   = date('Y-m-d', strtotime('+' . $plan['duration'] . ' days'));

// Tax (8%)
$tax   = $plan['price'] * 0.08;
$total = $plan['price'] + $tax;
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <div class="page-header">
        <div>
            <h2>Checkout</h2>
            <p class="page-header-sub">Review your order before proceeding</p>
        </div>
        <a href="<?= BASE_URL ?>/user/view_plans.php" class="btn btn-secondary btn-sm">&larr; Back to Plans</a>
    </div>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-6 mb-4">
            <h5 class="mb-3" style="color:#fff;">Order Summary</h5>

            <div class="checkout-order-card card mb-3">
                <div class="card-header"><?= htmlspecialchars($plan['plan_name']) ?> Plan</div>
                <div class="card-body">
                    <p style="color:var(--muted);font-size:0.9rem;margin-bottom:14px;"><?= htmlspecialchars($plan['description']) ?></p>

                    <div class="checkout-row">
                        <span>Plan Price</span>
                        <span class="checkout-val">Rs <?= number_format($plan['price'], 0) ?></span>
                    </div>
                    <div class="checkout-row">
                        <span>Duration</span>
                        <span class="checkout-val"><?= $plan['duration'] ?> days</span>
                    </div>
                    <div class="checkout-row">
                        <span>Tax (8%)</span>
                        <span class="checkout-val">Rs <?= number_format($tax, 0) ?></span>
                    </div>
                    <div class="checkout-total-row">
                        <span>Total</span>
                        <span class="checkout-total-val">Rs <?= number_format($total, 0) ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Access Dates</div>
                <div class="card-body">
                    <div class="checkout-row">
                        <span>Start Date</span>
                        <span class="checkout-val"><?= date('M d, Y', strtotime($startDate)) ?></span>
                    </div>
                    <div class="checkout-row" style="border:none;">
                        <span>End Date</span>
                        <span class="checkout-val"><?= date('M d, Y', strtotime($endDate)) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing -->
        <div class="col-md-6 mb-4">
            <h5 class="mb-3" style="color:#fff;">Billing Information</h5>

            <div class="card mb-3">
                <div class="card-header">Contact Details</div>
                <div class="card-body">
                    <div class="checkout-row">
                        <span>Name</span>
                        <span class="checkout-val"><?= htmlspecialchars($user['name']) ?></span>
                    </div>
                    <div class="checkout-row">
                        <span>Email</span>
                        <span class="checkout-val"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="checkout-row" style="border:none;">
                        <span>Phone</span>
                        <span class="checkout-val"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></span>
                    </div>
                </div>
            </div>

            <div class="payment-method-card card mb-4">
                <div class="card-header">Payment Method</div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="credit" value="credit" checked>
                        <label class="form-check-label" for="credit">Credit / Debit Card</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal">
                        <label class="form-check-label" for="paypal">PayPal</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="bank" value="bank">
                        <label class="form-check-label" for="bank">Bank Transfer</label>
                    </div>
                </div>
            </div>

            <form action="<?= BASE_URL ?>/user/process_payment.php" method="POST">
                <input type="hidden" name="plan_id" value="<?= $planId ?>">
                <input type="hidden" name="amount"  value="<?= $total ?>">
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Confirm &amp; Pay — Rs <?= number_format($total, 0) ?>
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
