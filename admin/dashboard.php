<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('admin')) {
    header('Location: ' . BASE_URL);
    exit();
}

$page_title = 'Admin Dashboard';
require_once '../includes/header.php';

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Total Users</h3>
        <p style="font-size: 2.5rem; color: #667eea; margin: 1rem 0;"><?php echo $total_users; ?></p>
        <a href="manage_users.php" class="btn">Manage Users</a>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Total Posts</h3>
        <p style="font-size: 2.5rem; color: #667eea; margin: 1rem 0;"><?php echo $total_posts; ?></p>
        <a href="manage_posts.php" class="btn">Manage Posts</a>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Total Comments</h3>
        <p style="font-size: 2.5rem; color: #667eea; margin: 1rem 0;"><?php echo $total_comments; ?></p>
        <a href="manage_comments.php" class="btn">Manage Comments</a>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Total Categories</h3>
        <p style="font-size: 2.5rem; color: #667eea; margin: 1rem 0;"><?php echo $total_categories; ?></p>
        <a href="manage_categories.php" class="btn">Manage Categories</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
