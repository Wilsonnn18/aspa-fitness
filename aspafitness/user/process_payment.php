<?php
// user/process_payment.php - Process payment
require_once __DIR__ . '/../config/app.php';
require_login('user');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/user/view_plans.php');
}

$planId = (int)($_POST['plan_id'] ?? 0);
$amount = (float)($_POST['amount'] ?? 0);
$userId = (int)$_SESSION['user_id'];

// Validate plan exists
$stmt = $conn->prepare('SELECT id, duration FROM membership_plans WHERE id = ?');
$stmt->bind_param('i', $planId);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();

if (!$plan || $planId <= 0 || $amount <= 0) {
    $_SESSION['error'] = 'Invalid purchase request.';
    redirect('/user/view_plans.php');
}

// In a real system, you would process payment with a payment gateway here (Stripe, PayPal, etc.)
// For now, we'll simulate a successful payment

try {
    // Start transaction
    $conn->begin_transaction();

    // Create payment record
    $paymentStatus = 'completed'; // In real system, this would be 'pending' until confirmed by gateway
    $transactionId = 'txn_' . uniqid() . '_' . time();

    $stmt = $conn->prepare('INSERT INTO payments (user_id, plan_id, amount, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('iidss', $userId, $planId, $amount, $paymentStatus, $transactionId);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    $_SESSION['success'] = 'Payment successful!';
    redirect('/user/subscription.php');

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Payment processing failed: ' . $e->getMessage();
    redirect('/user/view_plans.php');
}
?>
