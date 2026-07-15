<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

requireLogin();
if (!hasRole('admin')) {
    header('Location: ' . BASE_URL);
    exit();
}

$message = '';

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    if (!empty($name)) {
        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $name, $description);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Category added successfully!</div>';
        } else {
            if (strpos($stmt->error, 'Duplicate') !== false) {
                $message = '<div class="alert alert-danger">Category name already exists!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding category!</div>';
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Check if category has posts
    $check = $conn->query("SELECT COUNT(*) as count FROM posts WHERE category_id = $category_id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        $message = '<div class="alert alert-danger">Cannot delete category with existing posts!</div>';
    } else {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Category deleted successfully!</div>';
        }
    }
}

$page_title = 'Manage Categories';
require_once '../includes/header.php';

$categories = getCategories();
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Add New Category</h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" required placeholder="e.g., Technology">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Category description..." style="min-height: 100px;"></textarea>
            </div>
            
            <button type="submit" name="add_category" class="btn" style="width: 100%;">Add Category</button>
        </form>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">All Categories</h2>
        
        <?php echo $message; ?>
        
        <div style="max-height: 500px; overflow-y: auto;">
            <?php
            if ($categories->num_rows > 0) {
                while ($cat = $categories->fetch_assoc()) {
                    $post_count = $conn->query("SELECT COUNT(*) as count FROM posts WHERE category_id = " . $cat['id'])->fetch_assoc()['count'];
                    ?>
                    <div style="background: #f9f9f9; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; border-left: 4px solid #667eea; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($cat['name']); ?></h4>
                            <p style="margin: 0; color: #999; font-size: 0.9rem;">Posts: <?php echo $post_count; ?></p>
                        </div>
                        <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="return confirm('Are you sure?');">Delete</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No categories found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
