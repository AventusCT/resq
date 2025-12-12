# ResQ Quick Start Guide

## 🚀 Quick Setup (5 minutes)

### 1. Database Setup
```sql
CREATE DATABASE resq_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Then import: `magazijn/sql/schema.sql`

### 2. Configure Database
Edit `magazijn/public/Includes/db.php`:
- Set your MySQL password if needed
- Database name: `resq_db`

### 3. Create QR Codes Directory
```bash
mkdir -p magazijn/public/assets/qr_codes
chmod 755 magazijn/public/assets/qr_codes
```

Or run: `http://localhost/resq/magazijn/public/setup.php` (then delete it!)

### 4. Access Application
- URL: `http://localhost/resq/magazijn/public/`
- Default Admin Login:
  - Email: `admin@resq.nl`
  - Password: `admin123`
  - ⚠️ **CHANGE THIS IMMEDIATELY!**

## 📱 First Steps

### As Admin:
1. Login with admin credentials
2. Go to "Beheer" → "Gebruikersbeheer"
3. Create employee accounts
4. Go to "Inventarisbeheer"
5. Add items (QR codes auto-generate)

### As Employee:
1. Login with your account
2. Go to "Reserveringen" → "Nieuwe Reservering"
3. Select item and date/time
4. Use "QR Code Scannen" to pick up/return items

## 🔒 Security Checklist

- [ ] Change default admin password
- [ ] Delete `setup.php` after setup
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable HTTPS in production
- [ ] Review `.htaccess` settings
- [ ] Regular database backups

## 🐛 Common Issues

**QR Scanning not working?**
- Use HTTPS or localhost
- Grant camera permissions
- Use Chrome/Edge browser

**Database connection error?**
- Check MySQL is running
- Verify credentials in `db.php`
- Ensure database exists

**QR codes not generating?**
- Check `assets/qr_codes/` is writable
- Install Composer dependencies (optional)
- QR data is stored in DB even without images

## 📞 Support

Contact: contact@resq.nl

