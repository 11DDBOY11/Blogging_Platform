<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('admin')) {
    header('Location: ' . BASE_URL);
    exit();
}

$message = '';

// Handle approve
if (isset($_GET['approve'])) {
    $comment_id = (int)$_GET['approve'];
    $query = "UPDATE comments SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Comment approved!</div>';
    }
}

// Handle reject
if (isset($_GET['reject'])) {
    $comment_id = (int)$_GET['reject'];
    $query = "UPDATE comments SET status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Comment rejected!</div>';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $comment_id = (int)$_GET['delete'];
    $query = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Comment deleted!</div>';
    }
}

$page_title = 'Manage Comments';
require_once '../includes/header.php';

// Get all comments
$query = "SELECT c.*, u.username, p.title as post_title 
          FROM comments c 
          JOIN users u ON c.user_id = u.id 
          JOIN posts p ON c.post_id = p.id 
          ORDER BY c.created_at DESC";
$comments = $conn->query($query);
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 1.5rem;">Manage Comments</h2>
    
    <?php echo $message; ?>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
        <thead>
            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <th style="padding: 1rem; text-align: left;">Comment</th>
                <th style="padding: 1rem; text-align: left;">Author</th>
                <th style="padding: 1rem; text-align: left;">Post</th>
                <th style="padding: 1rem; text-align: left;">Status</th>
                <th style="padding: 1rem; text-align: left;">Date</th>
                <th style="padding: 1rem; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($comment = $comments->fetch_assoc()) {
                $status_class = $comment['status'] === 'approved' ? 'background: #d4edda; color: #155724;' : 'background: #fff3cd; color: #856404;';
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">
                        <p style="margin: 0; color: #666; font-size: 0.9rem;">
                            <?php echo htmlspecialchars(substr($comment['comment_text'], 0, 60)) . '...'; ?>
                        </p>
                    </td>
                    <td style="padding: 1rem;"><?php echo htmlspecialchars($comment['username']); ?></td>
                    <td style="padding: 1rem;">
                        <a href="<?php echo BASE_URL; ?>post.php?id=<?php echo $comment['post_id']; ?>" style="color: #667eea; text-decoration: none;">
                            <?php echo htmlspecialchars(substr($comment['post_title'], 0, 30)); ?>
                        </a>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="<?php echo $status_class; ?> padding: 0.25rem 0.75rem; border-radius: 3px; font-size: 0.85rem;">
                            <?php echo ucfirst($comment['status']); ?>
                        </span>
                    </td>
                    <td style="padding: 1rem;"><?php echo formatDate($comment['created_at']); ?></td>
                    <td style="padding: 1rem; text-align: center;">
                        <?php if ($comment['status'] !== 'approved'): ?>
                            <a href="?approve=<?php echo $comment['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem; background-color: #28a745;">Approve</a>
                        <?php else: ?>
                            <a href="?reject=<?php echo $comment['id']; ?>" class="btn" style="padding: 0.25rem 0.75rem; font-size: 0.9rem; background-color: #ffc107; color: #333;">Reject</a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $comment['id']; ?>" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.9rem;" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
