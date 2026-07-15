<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('admin')) {
    header('Location: ' . BASE_URL);
    exit();
}

$message = '';

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    if ($user_id !== 1) { // Prevent deleting admin user
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">User deleted successfully!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Cannot delete the primary admin user!</div>';
    }
}

// Handle role update
if (isset($_POST['update_role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = sanitize($_POST['role']);
    
    $query = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $role, $user_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">User role updated!</div>';
    }
}

$page_title = 'Manage Users';
require_once '../includes/header.php';

$users = getAllUsers();
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 1.5rem;">Manage Users</h2>
    
    <?php echo $message; ?>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
        <thead>
            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <th style="padding: 1rem; text-align: left;">Username</th>
                <th style="padding: 1rem; text-align: left;">Email</th>
                <th style="padding: 1rem; text-align: left;">Role</th>
                <th style="padding: 1rem; text-align: left;">Joined</th>
                <th style="padding: 1rem; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($user = $users->fetch_assoc()) {
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td style="padding: 1rem;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="role" onchange="this.form.submit()" style="padding: 0.4rem; border-radius: 4px; border: 1px solid #ddd;">
                                <option value="reader" <?php echo $user['role'] === 'reader' ? 'selected' : ''; ?>>Reader</option>
                                <option value="author" <?php echo $user['role'] === 'author' ? 'selected' : ''; ?>>Author</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit" name="update_role" style="display: none;"></button>
                        </form>
                    </td>
                    <td style="padding: 1rem;"><?php echo formatDate($user['created_at']); ?></td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="<?php echo BASE_URL; ?>post.php" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;">View</a>
                        <?php if ($user['id'] !== 1): ?>
                            <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;" onclick="return confirm('Are you sure?');">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
