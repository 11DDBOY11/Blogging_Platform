<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('author')) {
    header('Location: ' . BASE_URL);
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$post_id = (int)$_GET['id'];
$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header('Location: dashboard.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = $_POST['content'];
    $category_id = (int)$_POST['category_id'];
    $status = sanitize($_POST['status']);
    $slug = createSlug($title);
    
    // Handle image upload
    $featured_image = $post['featured_image'];
    if ($_FILES['featured_image']['size'] > 0) {
        // Delete old image if exists
        if ($featured_image && file_exists('../' . $featured_image)) {
            unlink('../' . $featured_image);
        }
        
        $upload_dir = '../' . UPLOAD_DIR;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['featured_image']['name'];
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $file_path)) {
            $featured_image = UPLOAD_DIR . $file_name;
        }
    }
    
    $query = "UPDATE posts SET title = ?, slug = ?, content = ?, category_id = ?, featured_image = ?, status = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssissii", $title, $slug, $content, $category_id, $featured_image, $status, $post_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Post updated successfully!</div>';
        // Refresh post data
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
    } else {
        $message = '<div class="alert alert-danger">Error updating post!</div>';
    }
}

$page_title = 'Edit Post';
require_once '../includes/header.php';

$categories = getCategories();
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2>Edit Post</h2>
    
    <?php echo $message; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Post Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    while ($cat = $categories->fetch_assoc()) {
                        $selected = $cat['id'] == $post['category_id'] ? 'selected' : '';
                        echo "<option value='" . $cat['id'] . "' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <?php if ($post['featured_image']): ?>
                <div style="margin-bottom: 1rem;">
                    <img src="<?php echo BASE_URL . $post['featured_image']; ?>" alt="Featured" style="max-width: 200px; border-radius: 5px;">
                </div>
            <?php endif; ?>
            <input type="file" id="featured_image" name="featured_image" accept="image/*">
            <p style="font-size: 0.9rem; color: #999; margin-top: 0.5rem;">Leave empty to keep current image</p>
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Update Post</button>
            <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
