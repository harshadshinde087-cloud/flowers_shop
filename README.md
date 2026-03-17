# Online Flower & Greeting Delivery System

A fully functional e-commerce website for selling flowers and greeting cards, built with PHP, MySQL, and Bootstrap.

## 🌸 Features

### Admin Panel
- Secure admin login system
- Dashboard with statistics
- Product management (Add, Edit, Delete)
- Category management
- Order management with status updates
- Customer management
- Real-time stock tracking

### User Side
- User registration and login
- Product browsing with categories
- Advanced search functionality
- Shopping cart with quantity management
- Secure checkout process
- Order history and tracking
- Responsive design for all devices

### Technical Features
- Clean, modular, well-commented code
- Database-driven dynamic content
- Secure password hashing
- Session management
- File upload for product images
- Real-time stock management
- Amazon/Flipkart inspired UI/UX

## 🛠 Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 8.x
- **Database**: MySQL
- **Server**: XAMPP (localhost)
- **Additional**: Font Awesome Icons

## 📋 Requirements

- XAMPP Server (Apache + MySQL + PHP)
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Modern web browser

## 🚀 Installation Guide

### Step 1: Setup XAMPP
1. Download and install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `flower_shop`
3. Import the provided `flower_shop.sql` file:
   - Click on the `flower_shop` database
   - Click "Import" tab
   - Choose the `flower_shop.sql` file from the project
   - Click "Go"

### Step 3: Project Setup
1. Copy the entire project folder to `C:/xampp/htdocs/p3/`
2. Ensure the folder structure is maintained

### Step 4: Permissions
1. Make sure the `uploads` folder is writable:
   - Right-click on `uploads` folder → Properties → Security
   - Give full permissions to IIS_IUSRS and your user account

### Step 5: Access the Website
- **Main Website**: http://localhost/p3/
- **Admin Panel**: http://localhost/p3/admin/

## 🔑 Login Credentials

### Admin Panel
- **URL**: http://localhost/p3/admin/
- **Username**: admin
- **Password**: admin123

### Test User Account
After registration, you can use any registered account to test the user features.

## 📁 Project Structure

```
p3/
├── admin/                  # Admin panel files
│   ├── login.php          # Admin login
│   ├── dashboard.php      # Admin dashboard
│   ├── products.php       # Product management
│   ├── categories.php     # Category management
│   ├── orders.php         # Order management
│   ├── customers.php      # Customer management
│   └── logout.php         # Admin logout
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   │   └── style.css     # Main styles
│   ├── js/               # JavaScript files
│   └── images/           # Image files
├── includes/             # PHP includes
│   ├── config.php        # Configuration
│   ├── database.php      # Database connection
│   └── functions.php     # Helper functions
├── uploads/              # Product image uploads
├── index.php             # Home page
├── products.php          # Product listing
├── product.php           # Product details
├── category.php          # Category products
├── cart.php              # Shopping cart
├── checkout.php          # Checkout process
├── order_confirmation.php # Order confirmation
├── orders.php            # User orders
├── login.php             # User login
├── register.php          # User registration
├── profile.php           # User profile
├── search.php            # Search results
├── logout.php            # User logout
├── flower_shop.sql       # Database file
└── README.md             # This file
```

## 🎯 How to Use

### For Admin
1. Login to admin panel with provided credentials
2. Add categories and products
3. Manage inventory and stock
4. View and update orders
5. Monitor customer activity

### For Customers
1. Register a new account or login
2. Browse products by category
3. Add items to cart
4. Proceed to checkout
5. Track order status

## 🔧 Configuration

### Database Configuration
Edit `includes/config.php` to modify database settings:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flower_shop');
```

### Site Configuration
Modify site settings in `includes/config.php`:
```php
define('SITE_URL', 'http://localhost/p3/');
define('SITE_NAME', 'Online Flower & Greeting Delivery System');
```

## 🌟 Key Features Explained

### Real-time Product Management
- When admin adds a product, it automatically appears on the shop page
- No manual coding required
- Stock updates in real-time

### Secure Authentication
- Password hashing using PHP's password_hash()
- Session-based authentication
- Separate admin and user authentication

### Shopping Cart System
- Session-based cart for logged-in users
- Real-time stock checking
- Automatic cart clearing after order

### Order Management
- Complete order lifecycle tracking
- Admin can update order status
- Customer can view order history

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL service is running in XAMPP
   - Check database credentials in `includes/config.php`

2. **Image Upload Not Working**
   - Check `uploads` folder permissions
   - Ensure PHP file uploads are enabled

3. **Session Issues**
   - Check PHP session settings
   - Ensure browser accepts cookies

4. **404 Errors**
   - Verify XAMPP is running on port 80
   - Check file permissions

### Error Reporting
For debugging, enable error display in PHP:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📱 Mobile Responsiveness

The website is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Features

- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- CSRF protection in forms
- Secure password hashing
- Session management
- File upload security

## 🎨 Customization

### Changing Colors
Edit `assets/css/style.css` and modify CSS variables:
```css
:root {
    --primary-color: #ff6b9d;
    --secondary-color: #c44569;
    --accent-color: #f8b500;
}
```

### Adding New Pages
1. Create new PHP file in root directory
2. Include `includes/config.php` at the top
3. Use existing header/footer structure

## 📞 Support

For any issues or questions:
1. Check the troubleshooting section
2. Verify all installation steps
3. Ensure proper file permissions
4. Check XAMPP services are running

## 📄 License

This project is for educational purposes. Feel free to modify and use for learning.

---

**Project Title**: Online Flower & Greeting Delivery System  
**Version**: 1.0  
**Last Updated**: <?php echo date('Y-m-d'); ?>
