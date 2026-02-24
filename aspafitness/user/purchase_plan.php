<?php
// user/purchase_plan.php
require_once __DIR__ . '/../config/app.php';
require_login('user');

$planId = (int)($_GET['id'] ?? 0);
if ($planId <= 0) {
    redirect('/user/view_plans.php');
}

$stmt = $conn->prepare('SELECT id, duration, price FROM membership_plans WHERE id = ?');
$stmt->bind_param('i', $planId);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();

if (!$plan) {
    redirect('/user/view_plans.php');
}

$userId = (int)$_SESSION['user_id'];
$amount = (float)$plan['price'];
$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+' . (int)$plan['duration'] . ' days'));
$subStatus = 'active';
$paymentStatus = 'completed';
$txn = uniqid('txn_');

$stmt = $conn->prepare('INSERT INTO subscriptions (user_id, plan_id, start_date, end_date, status) VALUES (?,?,?,?,?)');
$stmt->bind_param('iisss', $userId, $planId, $startDate, $endDate, $subStatus);
$stmt->execute();

$stmt = $conn->prepare('INSERT INTO payments (user_id, plan_id, amount, payment_status, transaction_id) VALUES (?,?,?,?,?)');
$stmt->bind_param('iidss', $userId, $planId, $amount, $paymentStatus, $txn);
$stmt->execute();

redirect('/user/subscription.php');
