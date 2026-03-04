# ASPA Fitness - Payment Plan System

## Overview
This document describes the new comprehensive payment plan system implemented for the ASPA Fitness website. The system includes membership plans, subscriptions, payments, and analytics.

---

## 1. Database Schema

### Tables Used:
- **membership_plans** - Stores available membership plans
- **subscriptions** - User subscriptions and their status
- **payments** - Payment transaction records
- **users** - User information

### Key Fields:
```
membership_plans:
  - id: Plan identifier
  - plan_name: Name of the plan (Basic, Standard, Premium)
  - duration: Days the plan lasts
  - price: Cost in PKR (Pakistani Rupees)
  - description: Plan details

subscriptions:
  - id: Subscription ID
  - user_id: User who purchased
  - plan_id: Plan purchased
  - start_date: When subscription begins
  - end_date: When subscription expires
  - status: active, expired, or cancelled

payments:
  - id: Payment ID
  - user_id: User who paid
  - plan_id: Plan paid for
  - amount: Payment amount
  - payment_date: When payment was made
  - payment_status: pending, completed, or failed
  - transaction_id: Payment gateway transaction ID
```

---

## 2. Available Membership Plans

### Plan Options:
1. **Basic Plan** - Rs 2,999/month (30 days)
   - Gym equipment access
   - Workout programs
   - Nutrition guides

2. **Standard Plan** - Rs 7,999/quarter (90 days)
   - All Basic features
   - Group classes access
   - Progress tracking

3. **Premium Plan** - Rs 29,999/year (365 days)
   - All Standard features
   - Personal trainer consultation
   - Meal planning service

---

## 3. User-Side Features

### 3.1 View Plans Page (`/user/view_plans.php`)
**Path:** `/user/view_plans.php`

Features:
- Display all membership plans in attractive cards
- Show current subscription status
- Display plan pricing in different time periods
- List plan features/benefits
- "Choose Plan" button for new purchases

UI Elements:
- Plan pricing prominently displayed
- Plan duration information
- Feature list with checkmarks
- Current plan highlighted (if subscribed)
- Responsive grid layout

### 3.2 Checkout Page (`/user/checkout.php`)
**Path:** `/user/checkout.php?id=[plan_id]`

Features:
- Order review with plan details
- Pricing breakdown:
  - Base price
  - Tax calculation (8%)
  - Total amount
- Subscription date range display
- Billing information section
- Payment method selection (Credit Card, PayPal, Bank Transfer)
- User information display

### 3.3 Process Payment (`/user/process_payment.php`)
**Path:** `/user/process_payment.php`

Features:
- Validates plan and amount
- Creates payment record
- Creates subscription record
- Uses database transactions for consistency
- Redirects to subscription page on success

Payment Flow:
1. User submits checkout form
2. System validates plan exists and amount is correct
3. Creates payment record with "completed" status
4. Creates active subscription
5. Redirects to subscription page

---

## 4. User Subscription Management (`/user/subscription.php`)

**Path:** `/user/subscription.php`

Features:
- Display current active subscription
- Show subscription start and end dates
- Display time remaining in subscription
- Option to upgrade or renew plan
- Subscription history

---

## 5. Admin Features

### 5.1 Plan Management (`/admin/manage_plans.php`)
**Path:** `/admin/manage_plans.php`

CRUD Operations:
- **Create:** Add new membership plans
  - Plan name
  - Duration (in days)
  - Price (in PKR)
  - Description

- **Read:** View all existing plans
  - Sortable table
  - Complete plan details

- **Update:** Edit existing plans
  - Click "Edit" button
  - Modify any plan field
  - Changes take effect immediately

- **Delete:** Remove plans
  - Prevents deletion of plans with active subscriptions
  - Shows error message if deletion not allowed

Validation:
- Plan name required
- Duration must be > 0 days
- Price must be >= 0
- Proper error messaging

### 5.2 Payment Analytics (`/admin/payment_analytics.php`)
**Path:** `/admin/payment_analytics.php`

Statistics Dashboard:
- **Total Revenue:** Sum of all completed payments
- **Active Subscriptions:** Count of active subscriptions
- **Total Payments:** Count of all payment transactions
- **Failed Payments:** Count of failed transactions

Revenue Analysis:
- Revenue breakdown by plan
- Transaction count per plan
- Total generated per plan

Recent Payments:
- Last 10 payment transactions
- Display: Date, User, Plan, Amount, Status, Transaction ID
- Status indicators (badges): Completed (green), Pending (yellow), Failed (red)

### 5.3 Existing Payment Records (`/admin/payments.php`)
**Path:** `/admin/payments.php`

Features:
- View all payment records
- Display complete payment details
- Filter and search capabilities
- Transaction history

---

## 6. User Flow Diagrams

### Purchase Flow:
```
1. User logged in
2. Click "View Plans"
3. Browse membership plans
4. Select plan → Click "Choose Plan"
5. Review order at checkout
6. Select payment method
7. Click "Proceed to Payment"
8. Payment processed
9. Subscription created
10. Confirmation message
11. Redirect to subscription page
```

### Admin Plan Management Flow:
```
1. Admin logged in
2. Navigate to "Manage Plans"
3. Options:
   a) Create: Fill form → Submit → Plan added
   b) Update: Click Edit → Modify → Update
   c) Delete: Click Delete → Confirm → Plan removed (if no active subscriptions)
4. View all plans in table
```

---

## 7. Key Features

### Security:
- Input validation on all forms
- SQL prepared statements for all queries
- Login required for user pages
- Admin-only access to admin pages
- Transaction rollback on payment failure

### User Experience:
- Attractive pricing card layout
- Clear plan comparisons
- Current subscription indicator
- Easy checkout process
- Order review before payment
- Confirmation after purchase

### Admin Experience:
- Simple plan management interface
- Clear error messages
- Data validation feedback
- Real-time analytics
- Payment tracking

### Payment Features:
- Tax calculation
- Transaction ID generation
- Payment status tracking
- Multiple payment method support
- Subscription date auto-calculation

---

## 8. Configuration

### Tax Rate:
Currently set to 8% in checkout.php
To modify:
```php
$tax = $plan['price'] * 0.08;  // Change 0.08 to desired rate
```

### Default Plans:
Bootstrap plans are included in the database SQL file:
- Basic: 30 days @ Rs 2,999
- Standard: 90 days @ Rs 7,999
- Premium: 365 days @ Rs 29,999

---

## 9. Integration Notes

### Stripe Integration (Optional):
To add real payment gateway support:
1. Install Stripe PHP library
2. Add API keys to config
3. Update process_payment.php to use Stripe API
4. Payment status updated based on gateway response

### PayPal Integration (Optional):
To add PayPal support:
1. Install PayPal SDK
2. Configure PayPal credentials
3. Handle PayPal returns
4. Update subscription creation

---

## 10. Testing Checklist

- [ ] View all membership plans
- [ ] Current subscription displays correctly
- [ ] Checkout page shows correct pricing
- [ ] Tax calculation is accurate
- [ ] Payment creates subscription
- [ ] Subscription dates calculated correctly
- [ ] Admin can create new plan
- [ ] Admin can edit existing plan
- [ ] Admin can delete plan (no active subscriptions)
- [ ] Admin can view payment analytics
- [ ] Payment history displays
- [ ] Revenue calculation is correct

---

## 11. File Structure

```
/user/
  view_plans.php           - Display membership plans
  checkout.php             - Order review and checkout
  process_payment.php      - Payment processing
  subscription.php         - User subscription page
  purchase_plan.php        - Legacy (redirect to checkout)

/admin/
  manage_plans.php         - Create/Edit/Delete plans
  payment_analytics.php    - Payment reports
  payments.php             - Payment history

/database/
  aspa_fitness.sql         - Database schema with sample plans

/config/
  db.php                   - Database connection
  app.php                  - Application configuration
```

---

## 12. Future Enhancements

1. **Recurring Billing:**
   - Auto-renew subscriptions
   - Billing reminders

2. **Promotions:**
   - Discount codes
   - Seasonal sales
   - Bundle deals

3. **Payment Methods:**
   - Real Stripe integration
   - PayPal integration
   - Apple Pay / Google Pay

4. **Advanced Analytics:**
   - Churn rate calculation
   - Customer lifetime value
   - Revenue forecasting

5. **User Features:**
   - Plan comparison tool
   - Auto-upgrade on expiration
   - Cancellation requests
   - Refund processing

---

## 13. Support & Maintenance

### Common Issues:

**Q: Plan won't delete**
A: This plan has active subscriptions. Deactivate subscriptions first or manually change their status.

**Q: Payment not processing**
A: Check database connection, ensure plan exists, verify amount calculation.

**Q: Subscription dates incorrect**
A: Verify database server timezone matches application timezone.

### Contact:
For issues or enhancements, contact the development team.

---

*Last Updated: February 2026*
