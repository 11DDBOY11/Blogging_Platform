<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($user['role'] === 'author') {
                header('Location: author/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'Invalid password!';
        }
    } else {
        $error = 'User not found!';
    }
}

$page_title = 'Login';
require_once 'includes/header.php';
?>

<div style="max-width: 500px; margin: 3rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2>Login</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>
    
    <p style="margin-top: 1rem; text-align: center;">
        Don't have an account? <a href="register.php" style="color: #667eea; text-decoration: none;">Register here</a>
    </p>
    
    <p style="margin-top: 1rem; background: #f0f0f0; padding: 1rem; border-radius: 5px; font-size: 0.9rem;">
        <strong>Demo Credentials:</strong><br>
        Username: admin<br>
        Password: password123<br>
        <br>
        Username: john_author<br>
        Password: password123
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>
