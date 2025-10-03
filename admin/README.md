# Narshimha Tattoo - Admin Panel

A comprehensive admin panel for managing the Narshimha Tattoo Studio website with full CRUD operations for bookings, services, and artists.

## 🚀 Quick Access

**Admin Login URL:** `http://localhost/your-project/admin/login.php`

**Default Credentials:**
- Username: `admin`
- Password: `narshimha2024`

⚠️ **IMPORTANT:** Change these credentials in production!

## 📱 Admin Panel Features

### 🔐 Authentication System
- **Secure Login/Logout** with session management
- **Session Timeout** (4 hours automatic logout)
- **Access Control** - all pages require authentication
- **Responsive Design** - works on desktop and mobile

### 📊 Dashboard Overview
- **Real-time Statistics**
  - Total bookings count
  - Pending bookings (requiring attention)
  - Confirmed bookings
  - Active services count
- **Recent Bookings** - last 10 bookings with status
- **Status Distribution** - visual breakdown of booking statuses
- **Quick Actions** - direct links to common tasks

### 📅 Booking Management
- **Complete Booking List** with pagination
- **Advanced Filtering**
  - Filter by status (pending, confirmed, completed, etc.)
  - Search by name, email, or booking reference
- **Status Updates** with notes and history tracking
- **Booking Details** - full customer information
- **Responsive Table** - works on all screen sizes

### 🎨 Service Management
- **Visual Service Grid** - see all services with images
- **Add/Edit Services** with full details:
  - Category assignment
  - Pricing and duration
  - Size information
  - Image URLs
  - Active/inactive status
- **Category Organization** - services grouped by type
- **Price Management** - easy pricing updates

### 👨‍🎨 Artist Management
- **Artist Profiles** with photos and bios
- **Specialties Tracking** - what each artist excels at
- **Experience Levels** - years of experience
- **Contact Information** - email and phone
- **Booking Statistics** - how many bookings per artist
- **Active/Inactive Status** - control visibility

## 🛡️ Security Features

### Authentication & Sessions
- **Secure Session Management** - PHP sessions with timeout
- **CSRF Protection** - forms protected against cross-site attacks
- **Input Sanitization** - all user input cleaned and validated
- **SQL Injection Prevention** - prepared statements throughout

### Access Control
- **Admin-Only Access** - all admin pages require login
- **Automatic Redirects** - unauthorized users sent to login
- **Session Validation** - continuous session checking
- **Secure Logout** - complete session destruction

## 📁 File Structure

```
admin/
├── auth.php          # Authentication system
├── login.php         # Login page
├── dashboard.php     # Main dashboard
├── bookings.php      # Booking management
├── services.php      # Service management
├── artists.php       # Artist management
└── README.md         # This documentation
```

## 🎨 Design Features

### Modern Dark Theme
- **Consistent Branding** - matches main website
- **Neon Red Accents** - signature color throughout
- **Professional Layout** - clean and organized
- **Responsive Design** - works on all devices

### User Experience
- **Intuitive Navigation** - clear menu structure
- **Visual Feedback** - success/error messages
- **Loading States** - smooth interactions
- **Modal Dialogs** - for editing without page refresh

## 📊 Dashboard Statistics

### Key Metrics Tracked
- **Total Bookings** - lifetime booking count
- **Pending Bookings** - requiring immediate attention
- **Confirmed Bookings** - scheduled appointments
- **Active Services** - currently available services

### Recent Activity
- **Latest Bookings** - last 10 booking requests
- **Status Distribution** - breakdown by booking status
- **Quick Actions** - shortcuts to common tasks

## 🔧 Management Features

### Booking Operations
- ✅ **View All Bookings** - paginated list with search
- ✅ **Update Status** - change booking status with notes
- ✅ **Filter & Search** - find specific bookings quickly
- ✅ **Status History** - track all status changes
- ✅ **Customer Details** - full contact information

### Service Operations
- ✅ **Add New Services** - complete service creation
- ✅ **Edit Existing** - update prices, descriptions, images
- ✅ **Category Management** - organize by tattoo style
- ✅ **Active/Inactive** - control service visibility
- ✅ **Visual Grid** - see all services at a glance

### Artist Operations
- ✅ **Artist Profiles** - complete bio and contact info
- ✅ **Specialties** - track what each artist does best
- ✅ **Experience Tracking** - years in the business
- ✅ **Booking Statistics** - performance metrics
- ✅ **Profile Images** - professional photos

## 🚀 Getting Started

### 1. Setup Database
Make sure you've run the main setup first:
```bash
# Visit in browser
http://localhost/your-project/setup.php
```

### 2. Access Admin Panel
```bash
# Login page
http://localhost/your-project/admin/login.php

# Use default credentials
Username: admin
Password: narshimha2024
```

### 3. Change Default Password
⚠️ **CRITICAL FOR PRODUCTION:**

Edit `admin/auth.php`:
```php
define('ADMIN_USERNAME', 'your_username');
define('ADMIN_PASSWORD', 'your_secure_password');
```

## 📱 Mobile Responsive

The admin panel is fully responsive and works great on:
- **Desktop** - full featured experience
- **Tablet** - optimized layout with touch-friendly controls
- **Mobile** - condensed view with essential features

## 🔒 Production Security

### Essential Security Steps

1. **Change Default Credentials**
   ```php
   // In admin/auth.php
   define('ADMIN_USERNAME', 'your_secure_username');
   define('ADMIN_PASSWORD', 'your_very_secure_password');
   ```

2. **Enable HTTPS**
   - Use SSL certificate
   - Force HTTPS redirects
   - Secure cookie settings

3. **Database Security**
   - Use strong database passwords
   - Limit database user permissions
   - Regular security updates

4. **File Permissions**
   - Restrict admin folder access
   - Set proper file permissions
   - Hide sensitive files

## 🛠️ Customization

### Adding New Admin Pages
1. Create new PHP file in `/admin/` directory
2. Include `require_once 'auth.php';` at top
3. Call `AdminAuth::requireAuth();` to protect page
4. Use consistent navigation and styling

### Modifying Permissions
Edit `auth.php` to add role-based permissions:
```php
// Add role checking
public static function hasRole($role) {
    return $_SESSION['admin_role'] === $role;
}
```

### Custom Styling
The admin panel uses Tailwind CSS with custom configuration. Modify the `tailwind.config` in each file to customize colors and styling.

## 📞 Support & Troubleshooting

### Common Issues

**Can't Login:**
- Check database connection
- Verify credentials in `auth.php`
- Clear browser cookies/session

**Pages Not Loading:**
- Ensure PHP is running
- Check file permissions
- Verify database setup

**Session Timeout:**
- Default is 4 hours
- Modify timeout in `auth.php`
- Check server session settings

### Getting Help
- Check PHP error logs
- Verify database connections
- Test individual components
- Review browser console for JavaScript errors

---

**Professional admin panel for professional tattoo artists** 🔥

