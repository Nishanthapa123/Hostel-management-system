# Hostel Management System

A complete college project for managing hostel operations using PHP 8, MySQL, HTML5, CSS3, JavaScript, and Bootstrap 5.

## Project Structure

```text
HostelManagementSystem/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── includes/
│   ├── navbar.php
│   └── sidebar.php
├── uploads/
│   └── students/
├── config.php
├── database.sql
├── dashboard.php
├── index.php
├── logout.php
├── README.md
├── room_edit.php
├── rooms.php
├── staff.php
├── student_edit.php
├── student_view.php
├── students.php
├── visitors.php
├── complaints.php
├── payments.php
├── allocations.php
├── reports.php
└── ...
```

## Features

- Admin authentication with session management
- Student management with photo upload
- Room management and allocation tracking
- Fee collection and payment record dashboard
- Visitor tracking and complaint handling
- Staff management
- Reports and analytics
- Responsive Bootstrap UI in blue and white theme

## Database Setup

1. Open MySQL or phpMyAdmin.
2. Create a database named `hostel_management`.
3. Import the file `database.sql`.

The SQL file creates the database tables and a starter admin account.

## Configuration

Update database credentials in `config.php` if needed. For a hosted deployment, environment variables can be used instead:

```php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'hostel_management';
```

Supported environment variables are `HOSTEL_DB_HOST`, `HOSTEL_DB_USER`, `HOSTEL_DB_PASS`, `HOSTEL_DB_NAME`, and `HOSTEL_BASE_URL`.

## Run Locally

1. Place the project inside `htdocs` (XAMPP) or your local PHP server root.
2. Start Apache and MySQL.
3. Visit:

```text
http://localhost/HostelManagementSystem/
```

## Login Credentials

- Username: `admin`
- Password: `admin123`

## Resident Portal

Residents use the separate portal at:

```text
http://localhost/HostelManagementSystem/student_login.php
```

They sign in with their Student ID and registered phone number. From the portal they can view their room, payments, and send a message to the admin through Support requests.

## Notes

This project is designed for deployment on PHP 8+, MySQL/MariaDB, and a web server such as Apache. Keep the `uploads/students/` directory writable so student photos can be uploaded.
