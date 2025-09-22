# TechnoPazarim - Digital Product Sales Platform

TechnoPazarim is a modern e-commerce platform for selling digital products. It provides a comprehensive solution for games, software, and other digital content.

![Main](assets/images/main.png)
![Products](assets/images/products.png)
![Cart](assets/images/cart.png)
![Profile](assets/images/Profile.png)
![Contact](assets/images/contact.png)

![Admin Panel](assets/images/admin.png)
![Admin Users](assets/images/users.png)
![Admin Products](assets/images/products.png)
![Admin Analysis](assets/images/analysis.png)


## Features

### Customer Side
- **Modern and Responsive Design**: Smooth interface that works on all devices, mobile compatible
- **Product Management**: Categorized product listing, search and filtering
- **Product Detail Pages**: Image gallery, reviews and detailed description
- **Shopping Cart**: Add, remove products and update quantities
- **Secure Payment**: Secure payment processing with Shopier integration
- **Two-Factor Authentication (2FA)**: Account security with Google Authenticator support
- **Customer Panel**: Order history, download center and support system
- **Light/Dark Mode**: Theme switching based on user preference

### Admin Side
- **Product Management**: Adding, editing and deleting digital products
- **Digital Stock Tracking**: Inventory management for license keys and file-based products
- **Order and Payment Management**: Order statuses, payment tracking and reporting
- **Support Ticket System**: Management of customer support requests
- **Analytics and Statistics**: Sales reports, visitor analytics and performance metrics
- **User Management**: User accounts, roles and permissions
- **Security Logs**: Tracking login attempts and security events
- **Light/Dark Mode**: Theme support for admin panel

## Technical Features

### Technologies
- **Backend**: PHP (PDO with MySQL)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Database**: MySQL
- **Payment**: Shopier API integration
- **Security**: Two-Factor Authentication (2FA), Prepared Statements

### Security Features
- Two-Factor Authentication (2FA)
- Secure payment transactions
- XSS and SQL Injection protections
- Session management
- Recording of security events

### Responsive Design
- Interface optimized for mobile devices
- Compatible design for tablets and desktop computers
- Flexible grid system for proper display on all screen sizes

### Theme Support
- Light and dark theme options
- Automatic theme selection based on system preferences
- Storage of user preferences in localStorage

## Installation

1. Upload files to your web server
2. Import the `db.sql` file into your database
3. Configure database settings in `config.php`
4. Define your Shopier API keys in `config.php`

## Usage

### Admin Panel
- Login: `/admin`
- Default admin account:
  - Email: admin@gmail.com
  - Username: admin
  - Password: admin

### Customer Account
- New user registration: `/index.php?page=kayit`
- Login: `/index.php?page=login`

## File Structure

```
technopazarim/
├── admin/                 # Admin panel
│   ├── assets/            # CSS and JavaScript files
│   ├── pages/             # Admin pages
│   └── index.php          # Admin panel entry point
├── ajax/                  # AJAX operations
├── assets/                # General CSS and JavaScript files
├── includes/              # Common components
├── pages/                 # Customer pages
├── pay/                   # Payment related files
│   └── shopier/           # Shopier integration files
├── config.php             # Configuration file
├── db.sql                 # Database schema
├── index.php              # Main entry point
└── README.md              # This file
```

## Development

### Requirements
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Web server (Apache/Nginx)

### Contributing
We welcome contributions to improve TechnoPazarim! Here's how you can help:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/newfeature`)
3. Make your changes
4. Commit your changes (`git commit -am 'Add new feature'`)
5. Push to the branch (`git push origin feature/newfeature`)
6. Create a new Pull Request

If you find this project useful:
- ⭐ Give it a star!
- 📢 Share it with others
- 🐛 Report bugs
- 💡 Suggest new features
- 💻 Contribute code

### Important Notice for Commercial Use
Before using this project for commercial activities, please contact **bootkitt@protonmail.com** for permission.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For questions about the project, please create an issue or send an email.