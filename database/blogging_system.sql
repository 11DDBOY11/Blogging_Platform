-- Create Database
CREATE DATABASE IF NOT EXISTS blogging_system;
USE blogging_system;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'author', 'reader') DEFAULT 'reader',
    bio TEXT,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts Table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published') DEFAULT 'draft',
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Comments Table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    status ENUM('approved', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Data
INSERT INTO users (username, email, password, role, bio) VALUES
('admin', 'admin@blog.com', '$2y$10$YIjlrPNoS0G6Bs.Y9dVTiOnyx5NdHKVqWXaVxJglFvHWkVQUVHw5m', 'admin', 'Administrator'),
('john_author', 'john@blog.com', '$2y$10$YIjlrPNoS0G6Bs.Y9dVTiOnyx5NdHKVqWXaVxJglFvHWkVQUVHw5m', 'author', 'Tech Writer'),
('reader', 'reader@blog.com', '$2y$10$YIjlrPNoS0G6Bs.Y9dVTiOnyx5NdHKVqWXaVxJglFvHWkVQUVHw5m', 'reader', 'Blog Reader');

INSERT INTO categories (name, description) VALUES
('Technology', 'Tech related posts'),
('Web Development', 'Web development tutorials'),
('Database', 'Database management posts'),
('Programming', 'General programming topics');

-- Password for all: password123
