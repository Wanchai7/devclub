# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# Project Overview

This project is a PHP 8.3 web application following MVC architecture. It provides a user registration form and displays the list of registered users. The application uses MySQL database named `senpru` with automatic database and table creation. The frontend is styled with Tailwind CSS loaded via a CDN.

## Architecture

MVC (Model-View-Controller) web application using object-oriented PHP programming. The application is structured with separate components for data handling (Model), business logic (Controller), and presentation (View).


## Key Features:

*   **User Registration:** A form to add new users with fields for Name, Email, Gender, and Country.
*   **User List:** Displays all registered users in a responsive table.
*   **User Deletion:** Allows for the deletion of users.
*   **Email Validation:** Specifically validates that the user's email domain is `@webmail.npru.ac.th`.
*   **User Management:** Edit User functionality 

# Development Commands

## Running the Application
```bash
# Start local development server (if using PHP built-in server)
php -S localhost:8000

# Or access via XAMPP/WAMP
# Navigate to: http://localhost/webclaude/
```

## Prerequisites
- Web server (Apache/Nginx) or XAMPP/WAMP for local development
- PHP 8.3+ with `pdo_mysql` extension enabled
- MySQL 5.7+ or MariaDB 10.2+
- No build process required - direct PHP execution

## Database Management
The MySQL database (`senpru`) and tables are created automatically on first run.
- Database: `senpru`
- User: `root`
- Password: (empty)
- Host: `localhost`

# Development Conventions

## File Structure
```
/
├── config/
│   └── database.php      # Database configuration and connection
├── controllers/
│   └── UserController.php # Business logic and request handling
├── models/
│   └── User.php          # Data layer and database operations
├── views/
│   ├── layout.php        # Main HTML layout template
│   ├── user_form.php     # User registration/edit form
│   └── user_list.php     # Users table display
├── index.php             # Application entry point
└── CLAUDE.md             # Documentation

Note: MySQL database `senpru` is auto-created on MySQL server
```

## Code Style
- Object-oriented PHP programming with MVC pattern
- Modern PHP 8+ features (e.g., `str_ends_with()`)
- Separation of concerns (Model-View-Controller)
- Database operations using PDO with prepared statements
- Template-based views with PHP includes

## Security Practices
- PDO prepared statements for SQL injection prevention
- `htmlspecialchars()` for XSS protection
- Email domain validation (`@webmail.npru.ac.th` only)

# Database Schema

The application uses a single table named `users` in MySQL database `senpru`. The schema is defined as follows:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    gender ENUM('Male', 'Female', 'Other') NULL,
    country VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```