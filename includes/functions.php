<?php

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

// Create URL-friendly slug
function createSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Get all categories
function getCategories() {
    global $conn;
    $query = "SELECT * FROM categories";
    return $conn->query($query);
}

// Get user by ID
function getUserById($id) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get post by ID
function getPostById($id) {
    global $conn;
    $query = "SELECT p.*, u.username, u.bio, c.name as category_name 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get all published posts with pagination
function getPublishedPosts($limit = 10, $offset = 0) {
    global $conn;
    $query = "SELECT p.*, u.username, c.name as category_name 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 'published' 
              ORDER BY p.published_at DESC 
              LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result();
}

// Get total published posts count
function getTotalPublishedPosts() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM posts WHERE status = 'published'";
    return $conn->query($query)->fetch_assoc()['total'];
}

// Get comments for a post
function getPostComments($post_id) {
    global $conn;
    $query = "SELECT c.*, u.username FROM comments c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.post_id = ? AND c.status = 'approved' 
              ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Add comment
function addComment($post_id, $user_id, $comment_text) {
    global $conn;
    $comment_text = sanitize($comment_text);
    $query = "INSERT INTO comments (post_id, user_id, comment_text, status) 
              VALUES (?, ?, ?, 'approved')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
    return $stmt->execute();
}

// Increment post views
function incrementViews($post_id) {
    global $conn;
    $query = "UPDATE posts SET views_count = views_count + 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $post_id);
    return $stmt->execute();
}

// Get user posts
function getUserPosts($user_id) {
    global $conn;
    $query = "SELECT p.*, c.name as category_name 
              FROM posts p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.user_id = ? 
              ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Delete post
function deletePost($post_id, $user_id) {
    global $conn;
    $query = "DELETE FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $post_id, $user_id);
    return $stmt->execute();
}

// Search posts
function searchPosts($search_term) {
    global $conn;
    $search_term = '%' . sanitize($search_term) . '%';
    $query = "SELECT p.*, u.username, c.name as category_name 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 'published' AND 
              (p.title LIKE ? OR p.content LIKE ?) 
              ORDER BY p.published_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    return $stmt->get_result();
}

// Get all users (admin only)
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM users ORDER BY created_at DESC";
    return $conn->query($query);
}

// Get all posts (admin)
function getAllPosts() {
    global $conn;
    $query = "SELECT p.*, u.username, c.name as category_name 
              FROM posts p 
              JOIN users u ON p.user_id = u.id 
              JOIN categories c ON p.category_id = c.id 
              ORDER BY p.created_at DESC";
    return $conn->query($query);
}

// Format date
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}
