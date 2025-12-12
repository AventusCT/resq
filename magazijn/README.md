# ResQ - Inventory & Reservation Management System

Full-stack web application for inventory and reservation management with QR code support.

## Project Information

- **Project Name:** ResQ
- **Client:** ResQ B.V.
- **Contact:** contact@resq.nl, +31 6 9350
- **Team:** Luc, Jamal, Assim
- **Timeline:** September 5, 2025 - January 9, 2026

## Features

### For Employees (Medewerkers)
- ✅ Scan QR codes to change item status
- ✅ Reserve items for specific dates/times
- ✅ Pick up and return items via QR scanning
- ✅ View personal reservations
- ✅ View available items and locations
- ✅ Mobile-friendly interface

### For Admins (Beheerders)
- ✅ Manage users and roles
- ✅ Add, edit, and remove items
- ✅ Generate QR codes for items
- ✅ Manage warehouse locations
- ✅ View all reservations
- ✅ Mark items as defective or in maintenance
- ✅ View statistics and analytics

## Technology Stack

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **QR Code Library:** jsQR (client-side), Endroid QR Code (server-side)
- **Server:** XAMPP (Apache, MySQL, PHP)

## Installation & Setup

### Prerequisites
- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser with camera access (for QR scanning)

### Step 1: Database Setup

1. Start XAMPP and ensure MySQL is running
2. Create a new database named `resq_db`:
   ```sql
   CREATE DATABASE resq_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import the schema:
   ```bash
   mysql -u root -p resq_db < magazijn/sql/schema.sql
   ```
   Or use phpMyAdmin to import `magazijn/sql/schema.sql`

### Step 2: Configure Database Connection

Edit `magazijn/public/Includes/db.php` and update if needed:
```php
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL password
$database = "resq_db";
```

### Step 3: Install Dependencies (Optional)

If you want server-side QR code generation, install Composer dependencies:
```bash
cd magazijn
composer install
```

Note: The application works without Composer, but QR code generation will be limited.

### Step 4: Set Permissions

Ensure the following directories are writable:
```bash
chmod 755 magazijn/public/assets/qr_codes/
```

Or create the directory if it doesn't exist:
```bash
mkdir -p magazijn/public/assets/qr_codes/
chmod 755 magazijn/public/assets/qr_codes/
```

### Step 5: Access the Application

1. Place the project in your XAMPP htdocs folder (or configure your web server)
2. Access via browser: `http://localhost/resq/magazijn/public/`
3. Default admin credentials:
   - **Email:** admin@resq.nl
   - **Password:** admin123
   - ⚠️ **Change this password immediately in production!**

## Project Structure

```
magazijn/
├── docs/                 # Project documentation
├── public/              # Web-accessible files
│   ├── assets/         # Images, QR codes
│   ├── beheer/         # Admin management pages
│   ├── classes/        # PHP classes (User, Item, Reservation)
│   ├── css/            # Stylesheets
│   ├── Includes/       # Header, footer, database connection
│   ├── js/             # JavaScript files
│   └── *.php           # Main application pages
├── sql/                # Database schema
└── README.md           # This file
```

## Usage Guide

### For Employees

1. **Login:** Use your employee account credentials
2. **Reserve Item:**
   - Go to "Reserveringen" → "Nieuwe Reservering"
   - Select item and date/time
   - Confirm reservation
3. **Pick Up Item:**
   - Go to "Reserveringen" → "QR Code Scannen"
   - Select "Ophalen" tab
   - Scan QR code with camera
4. **Return Item:**
   - Go to "Reserveringen" → "QR Code Scannen"
   - Select "Terugbrengen" tab
   - Scan QR code with camera

### For Admins

1. **Manage Users:**
   - Go to "Beheer" → "Gebruikersbeheer"
   - Add, edit roles, or delete users
2. **Manage Items:**
   - Go to "Inventarisbeheer"
   - Add/edit/delete items
   - Generate QR codes
3. **Manage Reservations:**
   - Go to "Beheer" → "Reserveringen"
   - View and update all reservations

## Security Features

- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Auto-logout after 30 minutes inactivity
- ✅ CSRF protection (forms)

## Browser Compatibility

- Chrome/Edge (recommended for QR scanning)
- Firefox
- Safari (iOS 11+)
- Mobile browsers with camera access

## Troubleshooting

### QR Code Scanning Not Working
- Ensure HTTPS or localhost (browsers require secure context for camera)
- Grant camera permissions in browser settings
- Use Chrome/Edge for best compatibility

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database credentials in `db.php`
- Ensure database `resq_db` exists

### QR Codes Not Generating
- Check `assets/qr_codes/` directory exists and is writable
- Install Composer dependencies for server-side generation
- Or use client-side QR code display (QR code data is stored in database)

## Development Notes

- Session timeout: 30 minutes
- QR code format: `RESQ-XXXXXXXX` (8 character hash)
- Item statuses: available, reserved, picked_up, returned, defective, maintenance
- Reservation statuses: pending, confirmed, picked_up, returned, cancelled

## License

Proprietary - ResQ B.V.

## Support

For issues or questions, contact: contact@resq.nl

