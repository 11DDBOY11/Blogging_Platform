<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = (int)$_GET['id'];
$post = getPostById($post_id);

if (!$post || $post['status'] !== 'published') {
    header('Location: index.php');
    exit();
}

// Increment views
incrementViews($post_id);

// Handle comment submission
$comment_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!isLoggedIn()) {
        $comment_message = '<div class="alert alert-danger">Please login to comment.</div>';
    } else {
        $comment_text = $_POST['comment_text'];
        if (!empty($comment_text)) {
            if (addComment($post_id, $_SESSION['user_id'], $comment_text)) {
                $comment_message = '<div class="alert alert-success">Comment added successfully!</div>';
            }
        }
    }
}

$page_title = htmlspecialchars($post['title']);
require_once 'includes/header.php';
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    
    <?php if ($post['featured_image']): ?>
        <img src="<?php echo BASE_URL . $post['featured_image']; ?>" 
             alt="<?php echo htmlspecialchars($post['title']); ?>" 
             style="width: 100%; height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 2rem;">
    <?php endif; ?>
    
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    
    <div style="color: #666; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
        <span>By <strong><?php echo htmlspecialchars($post['username']); ?></strong></span> | 
        <span><?php echo formatDate($post['published_at']); ?></span> | 
        <span>Category: <strong><?php echo htmlspecialchars($post['category_name']); ?></strong></span> | 
        <span>👁️ <?php echo $post['views_count']; ?> views</span>
    </div>
    
    <div style="margin-bottom: 2rem; line-height: 1.8;">
        <?php echo nl2br($post['content']); ?>
    </div>
    
    <hr>
    
    <h3 style="margin: 2rem 0;">Comments</h3>
    
    <?php echo $comment_message; ?>
    
    <?php if (isLoggedIn()): ?>
        <div style="background: #f9f9f9; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <h4>Add a Comment</h4>
            <form method="POST">
                <div class="form-group">
                    <textarea name="comment_text" placeholder="Write your comment..." required></textarea>
                </div>
                <button type="submit" name="submit_comment" class="btn">Post Comment</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <a href="login.php">Login</a> to post a comment.
        </div>
    <?php endif; ?>
    
    <div>
        <?php
        $comments = getPostComments($post_id);
        if ($comments->num_rows > 0) {
            while ($comment = $comments->fetch_assoc()) {
                ?>
                <div style="background: #f9f9f9; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea;">
                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                    <div style="font-size: 0.85rem; color: #999;">
                        <?php echo formatDate($comment['created_at']); ?>
                    </div>
                    <p style="margin-top: 0.5rem;">
                        <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                    </p>
                </div>
                <?php
            }
        } else {
            echo "<p style='color: #999;'>No comments yet. Be the first to comment!</p>";
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
