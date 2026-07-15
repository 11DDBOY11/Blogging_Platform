<?php
if (!isset($conn)) {
    require_once 'config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>My Blog</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 1rem;
            transition: opacity 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            gap: 1rem;
        }

        main {
            min-height: calc(100vh - 200px);
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #764ba2;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 300px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <header>
        <nav class="container">
            <div class="nav-brand">
                <a href="<?php echo BASE_URL; ?>" style="color: white; text-decoration: none;">📝 MyBlog</a>
            </div>
            <div class="nav-menu">
                <a href="<?php echo BASE_URL; ?>index.php">Home</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole('admin')): ?>
                        <a href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Panel</a>
                    <?php elseif (hasRole('author')): ?>
                        <a href="<?php echo BASE_URL; ?>author/dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php">Login</a>
                    <a href="<?php echo BASE_URL; ?>register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container">
