# User Management System

A simple PHP web application built with MVC architecture for managing user registrations. The application provides a clean interface to add, edit, delete, and view user information with email domain validation.

## Features

- **User Registration**: Add new users with Name, Email, Gender, and Country
- **User Management**: Edit and delete existing user records
- **Email Validation**: Enforces `@webmail.npru.ac.th` domain requirement
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **Data Security**: SQL injection and XSS protection
- **SQLite Database**: Self-contained database with automatic schema creation

## Technology Stack

- **Backend**: PHP 8.3+ with MVC architecture
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Frontend**: HTML5, Tailwind CSS (CDN)
- **Security**: PDO prepared statements, input sanitization

## Installation

### Prerequisites

- PHP 8.3 or higher
- MySQL 5.7+ or MariaDB 10.2+
- PDO MySQL extension (usually included with PHP)
- Web server (Apache, Nginx) or local development environment (XAMPP, WAMP)

### Setup

1. **Clone or download** the project files to your web server directory
2. **Ensure MySQL server is running** (XAMPP includes MySQL)
3. **Navigate** to the project directory in your web browser
4. **Database and tables will be created automatically** on first access

### Local Development

#### Using PHP Built-in Server
```bash
cd /path/to/webclaude
php -S localhost:8000
```
Then visit: `http://localhost:8000`

#### Using XAMPP/WAMP
1. Place project files in `htdocs` directory
2. Visit: `http://localhost/webclaude/`

## Project Structure

```
webclaude/
├── config/
│   └── database.php          # Database configuration and connection
├── controllers/
│   └── UserController.php    # Business logic and request handling
├── models/
│   └── User.php             # Data layer and database operations
├── views/
│   ├── layout.php           # Main HTML layout template
│   ├── user_form.php        # User registration/edit form
│   └── user_list.php        # Users table display
├── index.php                # Application entry point
├── CLAUDE.md                # Development documentation
└── README.md                # This file

Note: MySQL database 'senpru' is auto-created on MySQL server
```

## Usage

### Adding Users
1. Fill in the user form with required information
2. Email must end with `@webmail.npru.ac.th`
3. Click "Add User" to save

### Editing Users
1. Click "Edit" next to any user in the list
2. Modify the information in the form
3. Click "Update User" to save changes

### Deleting Users
1. Click "Delete" next to any user in the list
2. Confirm the deletion when prompted

## Database Schema

The application uses a single `users` table in MySQL database `senpru`:

**Database Configuration:**
- Host: localhost
- Database: senpru
- User: root
- Password: (empty)

**Table Schema:**
```sql
CREATE TABLE users (
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

## Security Features

- **SQL Injection Prevention**: All database queries use PDO prepared statements
- **XSS Protection**: All user input is sanitized with `htmlspecialchars()`
- **Email Domain Validation**: Restricts registration to `@webmail.npru.ac.th` domain
- **Input Validation**: Server-side validation for required fields

## Architecture

The application follows the **MVC (Model-View-Controller)** pattern:

- **Model** (`models/User.php`): Handles all database operations and data validation
- **View** (`views/*.php`): Manages the presentation layer and HTML templates
- **Controller** (`controllers/UserController.php`): Processes user requests and coordinates between Model and View

## Development

### Code Style
- Object-oriented PHP programming
- PSR-4 autoloading compatible structure
- Modern PHP 8+ features
- Separation of concerns

### Adding New Features
1. **Database changes**: Modify `config/database.php`
2. **Data operations**: Add methods to `models/User.php`
3. **Business logic**: Update `controllers/UserController.php`
4. **UI changes**: Modify appropriate view files in `views/`

## Troubleshooting

### Common Issues

**MySQL connection issues**
- Ensure MySQL server is running (check XAMPP control panel)
- Verify MySQL credentials (root user with empty password)
- Check if port 3306 is available

**PHP extensions**
- Verify that `pdo_mysql` extension is enabled in PHP

**Email validation errors**
- Check that email addresses end with exactly `@webmail.npru.ac.th`

### Error Messages
The application provides clear error messages for:
- Invalid email domains
- Duplicate email addresses
- Missing required fields
- Database connection issues

## License

This project is open source and available under the [MIT License](LICENSE).

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For issues or questions, please create an issue in the project repository.