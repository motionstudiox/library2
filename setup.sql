-- Library Management System Database Schema
-- Execute this SQL script to create the database

-- Create Database
CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- ============================================
-- Books Table
-- ============================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(200) NOT NULL,
    description LONGTEXT,
    publisher VARCHAR(150),
    publication_year INT,
    genre VARCHAR(100),
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_genre (genre)
);

-- ============================================
-- Lending Table
-- ============================================
CREATE TABLE IF NOT EXISTS lending (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATETIME NOT NULL,
    return_date DATETIME,
    is_returned BOOLEAN DEFAULT FALSE,
    fine_amount DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    INDEX idx_is_returned (is_returned)
);

-- ============================================
-- Reservation Table
-- ============================================
CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pickup_date DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    is_fulfilled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    INDEX idx_active (is_active)
);

-- ============================================
-- Review Table
-- ============================================
CREATE TABLE IF NOT EXISTS review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating DECIMAL(2, 1) NOT NULL,
    comment LONGTEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    UNIQUE KEY unique_user_book_review (user_id, book_id)
);

-- ============================================
-- Sample Data
-- ============================================

-- Insert sample users
INSERT INTO users (username, email, password_hash, full_name, phone) VALUES
('admin', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Library Admin', '123-456-7890'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '987-654-3210'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', '555-123-4567');

-- Insert sample books
INSERT INTO books (isbn, title, author, description, publisher, publication_year, genre, total_copies, available_copies) VALUES
('978-0-123456-78-9', 'The Great Gatsby', 'F. Scott Fitzgerald', 'A classic American novel about the Jazz Age.', 'Scribner', 1925, 'Fiction', 5, 5),
('978-0-987654-32-1', 'To Kill a Mockingbird', 'Harper Lee', 'A powerful story about racial injustice and childhood.', 'J.B. Lippincott & Co.', 1960, 'Fiction', 3, 3),
('978-0-111111-11-1', '1984', 'George Orwell', 'A dystopian novel about totalitarianism.', 'Secker & Warburg', 1949, 'Science Fiction', 4, 4),
('978-0-222222-22-2', 'Pride and Prejudice', 'Jane Austen', 'A romantic novel about manners and marriage.', 'T. Egerton', 1813, 'Romance', 2, 2),
('978-0-333333-33-3', 'The Catcher in the Rye', 'J.D. Salinger', 'A coming-of-age story about teenage rebellion.', 'Little, Brown and Company', 1951, 'Fiction', 3, 3);</content>
<parameter name="filePath">d:\PROJECT\library\setup.sql