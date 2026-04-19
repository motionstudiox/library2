# Web Library Management System

A complete web-based library management system built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- **User Authentication**: Register and login functionality
- **Book Management**: Add, search, and view books
- **Borrowing System**: Borrow and return books with due dates
- **User Dashboard**: View borrowed books and due dates
- **Responsive Design**: Works on desktop and mobile devices

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7+
- **Database**: MySQL
- **Styling**: Custom CSS with modern design

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database named `library_db`
2. Import the `setup.sql` file:
   ```sql
   mysql -u root -p library_db < setup.sql
   ```

### 2. Configuration

Edit `config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'library_db');
```

### 3. Web Server Setup

Place the `library` folder in your web server's document root:

- **XAMPP**: `C:\xampp\htdocs\library`
- **WAMP**: `C:\wamp\www\library`
- **LAMP**: `/var/www/html/library`

### 4. Access the Application

Open your browser and go to:
```
http://localhost/library/index.html
```

## Default Login Credentials

- **Username**: admin
- **Password**: password

## API Endpoints

The system uses RESTful API endpoints:

- `api/users.php` - User authentication
- `api/books.php` - Book management
- `api/lending.php` - Borrowing operations

## File Structure

```
library/
├── index.html          # Main application
├── config.php          # Database configuration
├── database.php        # Database connection class
├── setup.sql           # Database schema
├── api/
│   ├── books.php       # Books API
│   ├── users.php       # Users API
│   └── lending.php     # Lending API
└── README.md           # This file
```

## Usage

1. **Register**: Create a new account or login with existing credentials
2. **Browse Books**: Search and view available books
3. **Borrow Books**: Click "Borrow" on available books
4. **Return Books**: View your borrowed books and return them
5. **Add Books**: Librarians can add new books to the catalog

## Security Features

- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
- SQL injection prevention with prepared statements

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running
- Check database credentials in `config.php`
- Ensure the database `library_db` exists

### API Errors
- Check PHP error logs
- Verify file permissions
- Ensure CORS headers are set correctly

### JavaScript Errors
- Check browser console for errors
- Ensure all API endpoints are accessible
- Verify network connectivity

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.