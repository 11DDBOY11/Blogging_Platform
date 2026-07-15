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
    $post_id = (int)$_GET['delete'];
    $query = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Post deleted successfully!</div>';
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    $post_id = (int)$_POST['post_id'];
    $status = sanitize($_POST['status']);
    $query = "UPDATE posts SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $post_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Status updated!</div>';
    }
}

$page_title = 'Manage Posts';
require_once '../includes/header.php';

$posts = getAllPosts();
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2>Manage Posts</h2>
    
    <?php echo $message; ?>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
        <thead>
            <tr style="background: #f9f9f9; border-bottom: 2px solid #ddd;">
                <th style="padding: 1rem; text-align: left;">Title</th>
                <th style="padding: 1rem; text-align: left;">Author</th>
                <th style="padding: 1rem; text-align: left;">Category</th>
                <th style="padding: 1rem; text-align: left;">Status</th>
                <th style="padding: 1rem; text-align: left;">Views</th>
                <th style="padding: 1rem; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($post = $posts->fetch_assoc()) {
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;"><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($post['username']); ?></td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($post['category_name']); ?></td>
                    <td style="padding: 1rem;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="padding: 0.25rem;">
                                <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                            </select>
                            <button type="submit" name="update_status" style="display: none;"></button>
                        </form>
                    </td>
                    <td style="padding: 1rem;"><?php echo $post['views_count']; ?></td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="<?php echo BASE_URL; ?>post.php?id=<?php echo $post['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;">View</a>
                        <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
