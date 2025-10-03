# Narshimha Tattoo - PHP/MySQL Backend

A complete PHP/MySQL backend for the Narshimha Tattoo Studio website with booking system, cart functionality, and service management.

## 🚀 Quick Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Database Setup**
   ```bash
   # Create MySQL database
   mysql -u root -p
   CREATE DATABASE narshimha_tattoo;
   exit
   ```

2. **Configure Database**
   - Edit `config/database.php`
   - Update database credentials:
     ```php
     private $host = 'localhost';
     private $db_name = 'narshimha_tattoo';
     private $username = 'your_username';
     private $password = 'your_password';
     ```

3. **Run Setup**
   - Open `http://localhost/your-project/setup.php` in browser
   - This will create all tables and insert sample data

4. **Start Development Server**
   ```bash
   # If using PHP built-in server
   php -S localhost:8000
   
   # Then visit: http://localhost:8000
   ```

## 📁 Backend Structure

```
backend/
├── api/
│   ├── bookings.php      # Booking management API
│   ├── cart.php          # Shopping cart API
│   └── services.php      # Services and categories API
├── config/
│   └── database.php      # Database configuration
├── database/
│   └── schema.sql        # Database schema and sample data
├── includes/
│   └── functions.php     # Common utility functions
├── setup.php            # Database setup script
└── README_BACKEND.md    # This file
```

## 🔗 API Endpoints

### Bookings API (`/api/bookings.php`)

#### Create Booking
```http
POST /api/bookings.php
Content-Type: application/json

{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com",
  "phone": "(555) 123-4567",
  "artist": "marcus",
  "style": "blackwork",
  "description": "I want a geometric mandala tattoo",
  "serviceId": 1
}
```

#### Get Bookings
```http
GET /api/bookings.php              # Get all bookings
GET /api/bookings.php?id=123       # Get specific booking
```

#### Update Booking
```http
PUT /api/bookings.php?id=123
Content-Type: application/json

{
  "status": "confirmed",
  "notes": "Confirmed for next Tuesday"
}
```

### Services API (`/api/services.php`)

#### Get All Services
```http
GET /api/services.php
```

#### Get Services by Category
```http
GET /api/services.php/category/blackwork
```

#### Get Service Categories
```http
GET /api/services.php/categories
```

### Cart API (`/api/cart.php`)

#### Get Cart
```http
GET /api/cart.php?session_id=cart_12345
```

#### Add to Cart
```http
POST /api/cart.php
Content-Type: application/json

{
  "session_id": "cart_12345",
  "service_id": 1,
  "quantity": 1
}
```

#### Update Cart Item
```http
PUT /api/cart.php
Content-Type: application/json

{
  "session_id": "cart_12345",
  "service_id": 1,
  "quantity": 2
}
```

#### Remove from Cart
```http
DELETE /api/cart.php?session_id=cart_12345&service_id=1
```

## 🗄️ Database Schema

### Main Tables

- **artists** - Tattoo artists information
- **service_categories** - Service categories (Blackwork, Realism, etc.)
- **services** - Individual tattoo designs and services
- **bookings** - Customer booking requests
- **cart_sessions** - Temporary cart storage
- **contact_messages** - Contact form submissions
- **booking_status_history** - Booking status change log

### Key Features

- **Automatic booking references** (NT2024XXXX format)
- **Cart session management** (24-hour expiry)
- **Status tracking** for bookings
- **Email notifications** for confirmations
- **Soft deletes** for data integrity

## ⚙️ Configuration

### Email Settings
Edit `config/database.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
```

### Business Settings
```php
define('BUSINESS_START', 12);     # 12 PM
define('BUSINESS_END', 20);       # 8 PM
define('BUSINESS_DAYS', [2,3,4,5,6]); # Tue-Sat
```

### Booking Rules
```php
define('MIN_ADVANCE_HOURS', 24);  # 24 hours minimum
define('MAX_ADVANCE_DAYS', 90);   # 90 days maximum
```

## 🔒 Security Features

- **Input sanitization** for all user data
- **SQL injection protection** with prepared statements
- **XSS prevention** with HTML entity encoding
- **CORS headers** for API access control
- **Rate limiting** structure (implement as needed)

## 📧 Email Notifications

The system sends automatic email confirmations for:
- New booking requests
- Booking status changes
- Contact form submissions

Email templates are in HTML format with studio branding.

## 🛠️ Development

### Adding New Endpoints
1. Create new PHP file in `/api/` directory
2. Include database connection and functions
3. Implement CRUD operations
4. Add proper error handling and validation

### Database Migrations
- Modify `database/schema.sql` for structure changes
- Run setup.php again to apply changes
- Or execute SQL manually for production

### Testing APIs
Use tools like Postman or curl:
```bash
# Test booking creation
curl -X POST http://localhost:8000/api/bookings.php \
  -H "Content-Type: application/json" \
  -d '{"firstName":"Test","lastName":"User","email":"test@example.com","phone":"555-1234","description":"Test booking"}'
```

## 🚀 Production Deployment

1. **Update database credentials** in `config/database.php`
2. **Configure email settings** for notifications
3. **Set up SSL certificate** for HTTPS
4. **Enable error logging** and disable debug output
5. **Set up regular backups** for database
6. **Configure web server** (Apache/Nginx) properly

## 📞 Support

For backend-specific issues:
- Check PHP error logs
- Verify database connections
- Test API endpoints individually
- Review CORS settings for frontend integration

---

**Built with PHP 8+ and MySQL for optimal performance** 🔥

