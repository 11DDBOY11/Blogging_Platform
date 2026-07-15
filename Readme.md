# VTUBLOG 🎓

A full-featured **student blogging platform** built for the VTU community, where students can share knowledge, publish articles, explore campus-related content, and collaborate through a modern role-based blogging system.

VTUBLOG combines secure authentication, post management, comments, profile customization, password recovery, and role-based dashboards into one complete PHP and MySQL web application.

## Overview

VTUBLOG was designed as a practical full-stack project focused on real-world academic community needs. It supports three major user roles — **Admin**, **Author**, and **Reader** — each with a dedicated experience and permissions model.

The platform allows students to:
- Publish and manage blog posts.
- Explore VTU-specific categories.
- Interact through comments.
- Request author access.
- Personalize accounts with profile photos.
- Recover passwords securely through email-based reset links.

## Features

### Core Platform
- User registration and login.
- Secure password hashing.
- Role-based access control.
- Create, edit, delete, and publish blog posts.
- Post categorization.
- Comment system with moderation-ready structure.
- Featured image upload for posts.
- Responsive UI for desktop and mobile.

### User Management
- Admin, Author, and Reader roles.
- Author request workflow.
- Profile settings page.
- Profile photo upload.
- Email update and password change support.

### Security
- Prepared statements for database queries.
- Password hashing using PHP password APIs.
- Session-based authentication.
- Forgot password flow with secure token reset.
- Token expiration support.
- Basic protection against SQL injection and XSS through safe output handling.

### Branding and UX
- VTU-themed blogging experience.
- Clean dashboard-driven structure.
- Navigation with user profile display.
- Default avatar support.
- Organized upload directories for posts and profile photos.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Email | PHPMailer with Gmail SMTP |
| Local Environment | XAMPP |
| Hosting | InfinityFree |

## User Roles

### Admin
- Manage users.
- Access admin dashboard.
- Manage categories.
- Oversee posts and platform activity.
- Review author-related actions.

### Author
- Access author dashboard.
- Create and manage own posts.
- Upload featured images.
- Publish content in selected categories.

### Reader
- Register and log in.
- Read posts.
- Comment on articles.
- Request author access.
- Manage profile settings.

## Project Structure

```text
blogging_system/
├── admin/
├── author/
├── css/
├── includes/
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── js/
├── src/
├── uploads/
│   ├── posts/
│   └── profiles/
├── database/
├── index.php
├── login.php
├── register.php
├── forgot_password.php
├── reset_password.php
├── profile_settings.php
└── ...
```

## Database Highlights

The project database includes support for:
- `users`
- `posts`
- `categories`
- `comments`
- `password_resets`
- `author_requests`

This structure enables authentication, blogging workflows, account recovery, and role expansion in a scalable way.

## Installation

### 1. Clone or Download
Place the project folder inside your XAMPP `htdocs` directory.

```bash
C:\xampp\htdocs\blogging_system
```

### 2. Start Services
Start these modules in XAMPP:
- Apache
- MySQL

### 3. Create Database
Create a MySQL database and import the SQL file.

Example database name:

```sql
if0_40763004_epiz_vtublog
```

### 4. Configure Database Connection
Update `includes/config.php` with your database credentials.

```php
define('DB_HOST', 'your_host');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database_name');
```

### 5. Configure Email
For password reset functionality, set up Gmail SMTP in `includes/config.php`.

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'VTUBLOG');
```

### 6. Run the Project
Open the project in your browser:

```text
http://localhost/blogging_system/
```

## Hosting

VTUBLOG can be deployed on free PHP hosting platforms such as InfinityFree. For production-style deployment:
- Upload files to `htdocs`.
- Import the project database using phpMyAdmin.
- Update `config.php` with hosted database credentials.
- Enable SSL after hosting activation.

## Sample Login

Use the seeded admin account from the final database:

- **Username:** `admin`
- **Password:** `Admin@123`

## Screens and Modules

Key modules included in the project:
- Homepage
- Login and registration
- Admin dashboard
- Author dashboard
- Post creation and management
- Category management
- Comments system
- Forgot password and reset password
- Profile settings with avatar upload

## What Makes This Project Strong

VTUBLOG is more than a CRUD app. It demonstrates:
- Full-stack web development.
- Real authentication and authorization design.
- Email integration.
- File upload handling.
- User experience thinking.
- Role-based architecture.
- Deployment workflow awareness.

This makes it a strong academic, portfolio, and interview-ready project.

## Future Enhancements

Potential improvements for the next version:
- Search functionality.
- Rich text editor.
- Post likes and reactions.
- Email verification during registration.
- Notification system.
- Analytics dashboard.
- Tag-based filtering.
- Social sharing.
- Bookmarking and saved posts.

## Author Note

VTUBLOG was created as a complete VTU-focused student blogging system intended to solve a real campus-community use case while showcasing practical PHP and MySQL development skills.

## License

This project is intended for educational, academic, and portfolio use.