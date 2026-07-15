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

// Delete the post
$query = "DELETE FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header('Location: dashboard.php?success=Post deleted successfully');
} else {
    header('Location: dashboard.php?error=Error deleting post');
}
exit();
?>
