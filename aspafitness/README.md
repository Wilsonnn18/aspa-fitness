# ASPA Fitness Gym Management System

This is a simple gym management project built with core PHP and MySQL. It is suitable for BCA final year assignments and works on XAMPP (or any LAMP/WAMP stack).

## Technology stack
- PHP (core)
- MySQL
- HTML5, CSS3, Bootstrap 4
- JavaScript (minimal)
- Apache (XAMPP compatible)

## Folder Structure
```
aspa-fitness/
│
├── index.php                # redirects to login
├── config/
│   └── db.php               # database connection
├── includes/
│   ├── header.php           # page header & nav
│   └── footer.php           # page footer
├── assets/
│   ├── css/style.css
│   └── js/ (empty)
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── user/                    # user-facing pages
│   ├── dashboard.php
│   ├── view_plans.php
│   ├── purchase_plan.php
│   ├── subscription.php
│   └── workouts.php
├── admin/                   # admin pages
│   ├── dashboard.php
│   ├── manage_users.php
│   ├── manage_plans.php
│   ├── manage_workouts.php
│   └── payments.php
└── database/
    └── aspa_fitness.sql     # SQL to create schema
```

## Database Setup
1. Start XAMPP and enable Apache & MySQL.
2. Open `http://localhost/phpmyadmin`.
3. Create a new database named `aspa_fitness` or run the SQL file.
   - Import `database/aspa_fitness.sql` or paste its contents.
4. (Optional) Create an admin user manually:
   ```sql
   INSERT INTO admin (name,email,password) VALUES (
       'Admin','admin@example.com','<hash>');
   ```
   - Use PHP's `password_hash()` helper to generate `<hash>`.
   - Admin accounts are stored in the `admin` table.

## Configuration
- Edit `config/db.php` if your MySQL credentials are different.

## Usage
1. Place the project folder inside `htdocs` (for XAMPP) or your web root. Avoid spaces in the folder name – e.g. use `aspa-fitness` rather than `aspa fitness`.
2. Visit `http://localhost/aspa-fitness/` (or adjust to match your folder name) to start.
3. Register as a new user or login with an admin account.
4. Navigate between user/admin dashboards to manage plans, workouts, memberships, and payments.

## Features
- Secure password hashing (`password_hash` / `password_verify`).
- Role-based access control via sessions.
- User registration, login/logout, dashboard.
- Membership plan browsing and simulated purchases.
- Membership status view.
- Workout plan listing.
- Admin CRUD for users, membership plans, workout plans, payment records.

## Notes
- Payment processing is simulated; no real gateway is integrated.
- All forms have basic validation; you can enhance with JavaScript or server checks.
- UI uses Bootstrap for a clean, responsive look.

Feel free to extend the system, add features like profile editing, real transactions, or reporting.

Good luck with your BCA project!
