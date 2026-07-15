<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('author')) {
    header('Location: ' . BASE_URL);
    exit();
}

$page_title = 'Author Dashboard';
require_once '../includes/header.php';

$posts = getUserPosts($_SESSION['user_id']);
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>My Posts</h2>
        <a href="create_post.php" class="btn">+ Create New Post</a>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f9f9f9; border-bottom: 2px solid #ddd;">
                <th style="padding: 1rem; text-align: left;">Title</th>
                <th style="padding: 1rem; text-align: left;">Category</th>
                <th style="padding: 1rem; text-align: left;">Status</th>
                <th style="padding: 1rem; text-align: left;">Views</th>
                <th style="padding: 1rem; text-align: left;">Created</th>
                <th style="padding: 1rem; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($post = $posts->fetch_assoc()) {
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;"><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($post['category_name']); ?></td>
                    <td style="padding: 1rem;">
                        <span style="background: <?php echo $post['status'] === 'published' ? '#d4edda' : '#fff3cd'; ?>; padding: 0.25rem 0.75rem; border-radius: 3px;">
                            <?php echo ucfirst($post['status']); ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;"><?php echo $post['views_count']; ?></td>
                    <td style="padding: 1rem;"><?php echo formatDate($post['created_at']); ?></td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
