<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('author')) {
    header('Location: ' . BASE_URL);
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
    $featured_image = '';
    if ($_FILES['featured_image']['size'] > 0) {
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
    
    $query = "INSERT INTO posts (user_id, category_id, title, slug, content, featured_image, status, published_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, " . ($status === 'published' ? 'NOW()' : 'NULL') . ")";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iissss", $_SESSION['user_id'], $category_id, $title, $slug, $content, $featured_image);
    
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Post created successfully!</div>';
        header('Refresh: 2; url=dashboard.php');
    } else {
        $message = '<div class="alert alert-danger">Error creating post!</div>';
    }
}

$page_title = 'Create Post';
require_once '../includes/header.php';

$categories = getCategories();
?>

<div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2>Create New Post</h2>
    
    <?php echo $message; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Post Title</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php
                    while ($cat = $categories->fetch_assoc()) {
                        echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <input type="file" id="featured_image" name="featured_image" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" required></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Create Post</button>
            <a href="dashboard.php" class="btn" style="background-color: #6c757d;">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
