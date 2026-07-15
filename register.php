<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'reader')";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = 'Registration successful! <a href="login.php">Login here</a>';
            } else {
                if (strpos($stmt->error, 'Duplicate entry') !== false) {
                    $error = 'Username or email already exists!';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}

$page_title = 'Register';
require_once 'includes/header.php';
?>

<div style="max-width: 500px; margin: 3rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2>Register</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Register</button>
    </form>
    
    <p style="margin-top: 1rem; text-align: center;">
        Already have an account? <a href="login.php" style="color: #667eea; text-decoration: none;">Login here</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>
