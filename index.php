<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';
$search = '';
$posts = null;
$total_posts = 0;

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = sanitize($_POST['search']);
    $posts = searchPosts($search);
} else {
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 6;
    $offset = ($page - 1) * $limit;
    
    $posts = getPublishedPosts($limit, $offset);
    $total_posts = getTotalPublishedPosts();
    $total_pages = ceil($total_posts / $limit);
}

require_once 'includes/header.php';
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h1>Latest Blog Posts</h1>
    
    <form method="POST" style="margin: 2rem 0;">
        <div style="display: flex; gap: 1rem;">
            <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn">Search</button>
        </div>
    </form>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0;">
        <?php
        if ($posts && $posts->num_rows > 0) {
            while ($post = $posts->fetch_assoc()) {
                ?>
                <div style="background: white; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; transition: transform 0.3s; cursor: pointer;"
                     onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    
                    <?php if ($post['featured_image']): ?>
                        <img src="<?php echo BASE_URL . $post['featured_image']; ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>" 
                             style="width: 100%; height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div style="padding: 1.5rem;">
                        <h3 style="margin-bottom: 0.5rem;">
                            <a href="post.php?id=<?php echo $post['id']; ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
                            <span><?php echo htmlspecialchars($post['username']); ?></span> | 
                            <span><?php echo formatDate($post['published_at']); ?></span> | 
                            <span><?php echo htmlspecialchars($post['category_name']); ?></span>
                        </div>
                        
                        <p style="color: #666; margin-bottom: 1rem;">
                            <?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 150)) . '...'; ?>
                        </p>
                        
                        <div style="display: flex; justify-content: space-between;">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn">Read More</a>
                            <span style="color: #999; font-size: 0.9rem;">👁️ <?php echo $post['views_count']; ?></span>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No posts found.</p>";
        }
        ?>
    </div>

    <?php if (!$search && $total_pages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" 
                   class="btn" 
                   style="<?php echo $page === $i ? 'background-color: #764ba2;' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
