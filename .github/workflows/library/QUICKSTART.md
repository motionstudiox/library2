# Quick Start Guide - Web Library System

## 🚀 5-Minute Setup

### Prerequisites
- Web server with PHP 7+ and MySQL (XAMPP, WAMP, or similar)
- Modern web browser

### Step 1: Install Web Server
Download and install **XAMPP** (recommended for Windows):
1. Go to https://www.apachefriends.org/
2. Download XAMPP for Windows
3. Install with default settings
4. Start Apache and MySQL from the XAMPP Control Panel

### Step 2: Setup Project
1. Copy the `library` folder to `C:\xampp\htdocs\library`
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Create database: `library_db`
4. Import `setup.sql` from the project folder

### Step 3: Configure Database
Edit `C:\xampp\htdocs\library\config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Default XAMPP user
define('DB_PASS', '');          // Default XAMPP password (empty)
define('DB_NAME', 'library_db');
```

### Step 4: Access the System
Open browser and go to:
```
http://localhost/library/index.html
```

## 🔑 Features Overview

### Public Access (No Login Required)
- **Browse Books**: Search and view all available books
- **Book Details**: Click "View Details" to see book information
- **Book Availability**: See which books are available vs. checked out

### Authenticated Features (Login Required)
- **Borrow Books**: Click "Borrow" to check out available books
- **Return Books**: View and return your borrowed books
- **Due Dates**: Track when books are due (14 days from borrow date)
- **Overdue Alerts**: See overdue books highlighted in red
- **Add Books**: Librarians can add new books to the catalog
- **User Profile**: View your account information

## 🔑 Demo Credentials

**Admin Account:**
- Username: `admin`
- Password: `password`

**Sample Users:**
- Username: `john_doe` / Password: `password`
- Username: `jane_smith` / Password: `password`

## 📱 How to Use

1. **Browse Books**: The catalog is visible immediately - no login required
2. **Login/Register**: Click the login tab to access borrowing features
3. **Borrow Books**: After logging in, click "Borrow" on available books
4. **Manage Books**: Use "My Borrowed Books" to return items
5. **Add Books**: Use the "Add New Book" form (admin access)

## 🛠 Troubleshooting

### "Database connection failed"
- Ensure MySQL is running in XAMPP
- Check database credentials in `config.php`
- Verify database `library_db` exists

### "API endpoints not working"
- Check if Apache is running
- Verify file paths are correct
- Check PHP error logs in XAMPP

### "Page not loading"
- Ensure files are in `htdocs/library/`
- Check browser console for JavaScript errors
- Verify all files were copied correctly

### "Borrow button not showing"
- Make sure you're logged in
- Check if the book has available copies
- Refresh the page if needed

## 📂 File Structure
```
library/
├── index.html          # Main application (merged interface)
├── config.php          # Database config
├── database.php        # DB connection
├── setup.sql           # Database schema
├── README.md           # Full documentation
├── index_old.html      # Original simple version (backup)
└── api/
    ├── books.php       # Book operations
    ├── users.php       # User auth
    └── lending.php     # Borrow/return
```

## 🎯 Key Improvements in Merged Version

- **Public Catalog**: Books visible to everyone without login
- **Progressive Access**: Basic features free, advanced features require login
- **Better UX**: Clear separation between public and private features
- **Flexible Usage**: Can be used as both public library browser and member portal

## 💡 Tips

- Use the admin account to add more books
- Test borrowing/returning with different users
- Check browser developer tools for debugging
- All passwords in demo data are `password`

Happy coding! 📚