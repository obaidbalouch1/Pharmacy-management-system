# 📚 Complete Pharmacy Management System Guide

## 🎯 System Overview

A comprehensive, production-ready pharmacy management system with complete functionality for inventory, sales, purchases, reporting, and system administration.

---

## 📋 TABLE OF CONTENTS

1. [Quick Start](#quick-start)
2. [User Roles & Permissions](#user-roles--permissions)
3. [Module Guides](#module-guides)
4. [Search & Filter](#search--filter)
5. [Backup & Restore](#backup--restore)
6. [Activity Logs](#activity-logs)
7. [Best Practices](#best-practices)
8. [Troubleshooting](#troubleshooting)

---

## 🚀 QUICK START

### Installation (5 Minutes)

1. **Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install to default location

2. **Extract Files**
   ```
   Extract to: C:\xampp\htdocs\pharmacy
   ```

3. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

4. **Create Database**
   - Open: http://localhost/phpmyadmin
   - Create database: `pharmacy_db`
   - Import: `database/schema.sql`

5. **Access System**
   - URL: http://localhost/pharmacy
   - Username: `admin`
   - Password: `admin123`

6. **First Steps**
   - Change admin password
   - Add medicine categories
   - Add companies and suppliers
   - Add medicines
   - Start selling!

---

## 👥 USER ROLES & PERMISSIONS

### 1. Administrator (admin)
**Full System Access**
- ✅ Dashboard & Analytics
- ✅ Medicine Management
- ✅ Sales & Purchases
- ✅ Reports & Analytics
- ✅ Customer Management
- ✅ Company & Supplier Management
- ✅ User Management
- ✅ Activity Logs
- ✅ Backup & Restore
- ✅ Profile Management

### 2. Manager (manager)
**Business Operations**
- ✅ Dashboard & Analytics
- ✅ Medicine Management
- ✅ Sales & Purchases
- ✅ Reports & Analytics
- ✅ Customer Management
- ✅ Company & Supplier Management
- ❌ User Management
- ❌ Activity Logs
- ❌ Backup & Restore
- ✅ Profile Management

### 3. Pharmacist (pharmacist)
**Pharmacy Operations**
- ✅ Dashboard
- ✅ Medicine Management
- ✅ Sales
- ✅ Reports (view only)
- ✅ Customer Management
- ❌ Purchases
- ❌ Company & Supplier Management
- ❌ User Management
- ❌ Activity Logs
- ❌ Backup & Restore
- ✅ Profile Management

### 4. Cashier (cashier)
**Sales Only**
- ✅ Dashboard (view only)
- ❌ Medicine Management
- ✅ Sales (create only)
- ❌ Reports
- ❌ Customer Management
- ❌ Purchases
- ❌ Company & Supplier Management
- ❌ User Management
- ❌ Activity Logs
- ❌ Backup & Restore
- ✅ Profile Management

---

## 📦 MODULE GUIDES

### 1. Dashboard

**Features:**
- Statistics cards (Medicines, Revenue, Expiring, Low Stock)
- Circular doughnut chart (Top 10 selling medicines)
- Sales breakdown with color indicators
- Today's sales table with all transactions
- Real-time data updates

**How to Use:**
1. Login to system
2. Dashboard loads automatically
3. View key metrics at a glance
4. Check today's sales
5. Monitor expiring medicines
6. Review low stock items

**Today's Sales Section:**
- Shows ALL sales from current day
- Displays time, customer, amount, payment
- View and Print buttons for each sale
- Total amount at bottom
- Empty state if no sales today

---

### 2. Medicine Management

**Features:**
- Add/Edit/Delete medicines
- Batch tracking
- Barcode support
- Category assignment
- Company selection
- Stock management
- Expiry date tracking
- **Search & Filter** (NEW)

**How to Add Medicine:**
1. Go to Medicines page
2. Click "+ Add New Medicine"
3. Fill in all details:
   - Medicine name (required)
   - Generic name
   - Category
   - Company
   - Batch number
   - Barcode
   - Manufacturing date
   - Expiry date
   - Purchase price
   - Selling price
   - MRP
   - Initial quantity
   - Reorder level
   - Rack location
4. Click "Add Medicine"

**Search & Filter:**
- Search by: Name, generic name, or batch
- Filter by: Category
- Filter by: Status (Available, Out of Stock, Expired)
- Click "Search" to apply
- Click "Clear Filters" to reset

---

### 3. Sales Management (POS)

**Features:**
- Point of Sale interface
- Multi-item sales
- Customer selection or walk-in
- Real-time stock validation
- Tax & discount calculation
- **Amount paid & change calculator**
- Invoice generation
- Print functionality
- **Search & Filter** (NEW)

**How to Create Sale:**
1. Go to Sales → New Sale
2. Select customer (or leave for walk-in)
3. Add medicines:
   - Select medicine from dropdown
   - Enter quantity
   - Click "Add to Cart"
4. Review cart items
5. Enter tax percentage (if applicable)
6. Enter discount (if applicable)
7. System calculates grand total
8. Enter amount paid
9. System calculates change
   - Green background = sufficient payment
   - Red background = insufficient payment
10. Click "Complete Sale"
11. Print invoice

**Search Sales:**
- Search by: Invoice number or customer name
- Filter by: Date range (From - To)
- Filter by: Payment status
- View record count
- Click "Clear Filters" to reset

**Bulk Sales:**
- Create multiple sales at once
- Each sale has own payment validation
- All sales processed together

---

### 4. Purchase Management

**Features:**
- Purchase order creation
- Supplier selection
- Multi-item purchases
- Editable batch numbers
- Adjustable unit prices
- Tax calculation
- **Automatic stock addition**
- Payment status tracking
- **Search & Filter** (NEW)

**How to Create Purchase:**
1. Go to Purchases → New Purchase
2. Select supplier
3. Select purchase date
4. Add medicines:
   - Select medicine
   - Enter quantity
   - Edit unit price if needed
   - Edit batch number if needed
   - Click "Add to Purchase"
5. Review items
6. Enter tax percentage
7. Select payment status
8. Add notes (optional)
9. Click "Complete Purchase"
10. Stock automatically updated

**Search Purchases:**
- Search by: Purchase number or supplier
- Filter by: Date range
- Filter by: Payment status
- View record count

---

### 5. Reports & Analytics

**Report Types:**
1. **Sales Report**
   - Daily sales breakdown
   - Revenue tracking
   - Tax and discount analysis
   - Net revenue calculation

2. **Top Medicines Report**
   - Best-selling medicines
   - Units sold
   - Times ordered
   - Revenue per medicine

3. **Purchase Report**
   - Daily purchase tracking
   - Supplier-wise breakdown
   - Total purchase amounts

4. **Customer Report**
   - Top customers by spending
   - Total orders per customer
   - Average order value

**How to Generate Report:**
1. Go to Reports page
2. Select report type
3. Set date range (From - To)
4. Click "Generate Report"
5. View online in formatted table
6. Click "Download Excel" to export
7. Click "Print Report" for hard copy

**Excel Export:**
- Downloads as .xls file
- Opens in Microsoft Excel
- Includes all data and totals
- Professional formatting
- Saves to Downloads folder

---

### 6. Analytics Dashboard

**Features:**
- 4 chart types (Doughnut, Pie, Line, Bar)
- Date range filtering
- Statistics cards
- Top selling medicines
- Sales trends
- Revenue analysis

**How to Use:**
1. Go to Analytics page
2. Select date range
3. Click "Generate Analytics"
4. View charts and statistics
5. Analyze trends

---

### 7. Customer Management

**Features:**
- Add/Edit/Delete customers
- Contact information
- Purchase history tracking
- Loyalty points (future)

**How to Add Customer:**
1. Go to Customers page
2. Click "+ Add Customer"
3. Fill in details:
   - Customer name
   - Phone
   - Email
   - Address
   - City
   - Date of birth
   - Gender
4. Click "Add Customer"

---

### 8. Company & Supplier Management

**Companies (Manufacturers):**
- Add pharmaceutical companies
- Contact information
- Status management

**Suppliers:**
- Add medicine suppliers
- Contact details
- Tax information
- Payment terms

---

### 9. User Management (Admin Only)

**Features:**
- Add/Edit/Delete users
- Role assignment
- Status management
- Password reset
- Last login tracking
- Self-protection

**How to Add User:**
1. Go to Manage Users
2. Click "+ Add User"
3. Fill in details:
   - Full name
   - Username (unique)
   - Email
   - Phone
   - Password
   - Role (Admin/Manager/Pharmacist/Cashier)
   - Status (Active/Inactive)
4. Click "Add User"

**Security Features:**
- Cannot delete yourself
- Cannot deactivate yourself
- Bcrypt password hashing
- Role-based access control

---

### 10. Profile Management

**Features:**
- Update profile information
- Change username (with password verification)
- Change password (with confirmation)
- Avatar display

**How to Update Profile:**
1. Click avatar in top bar
2. Or go to My Profile
3. Update information
4. Click "Update Profile"

**Change Username:**
1. Go to My Profile
2. Enter new username
3. Enter current password
4. Click "Change Username"

**Change Password:**
1. Go to My Profile
2. Enter current password
3. Enter new password
4. Confirm new password
5. Click "Change Password"

---

## 🔍 SEARCH & FILTER

### Medicines Search
**Location:** Medicines page

**Search Options:**
- Text search: Medicine name, generic name, batch number
- Category filter: Select from dropdown
- Status filter: Available, Out of Stock, Expired

**How to Use:**
1. Enter search term in text box
2. Select category (optional)
3. Select status (optional)
4. Click "Search" button
5. Results display instantly
6. Click "Clear Filters" to reset

**Tips:**
- Use partial text for broader results
- Combine filters for precise results
- Empty search shows all medicines

---

### Sales Search
**Location:** Sales page

**Search Options:**
- Text search: Invoice number, customer name
- Date range: From date to To date
- Payment status: Paid, Pending, Partial

**How to Use:**
1. Enter invoice or customer name
2. Select date range (optional)
3. Select payment status (optional)
4. Click "Search" button
5. View matching sales
6. Record count displayed in header

**Tips:**
- Date range is inclusive
- Leave dates empty for all dates
- Use invoice number for exact match

---

### Purchases Search
**Location:** Purchases page

**Search Options:**
- Text search: Purchase number, supplier name
- Date range: From date to To date
- Payment status: Paid, Pending, Partial

**How to Use:**
1. Enter purchase number or supplier
2. Select date range (optional)
3. Select payment status (optional)
4. Click "Search" button
5. View matching purchases
6. Record count displayed

---

## 💾 BACKUP & RESTORE

### Overview
Complete database backup and restore system for data protection and disaster recovery.

**Location:** Backup & Restore (Admin menu)

**Access:** Admin only

---

### Create Backup

**How to Create:**
1. Go to Backup & Restore page
2. Click "Create Backup Now" button
3. Confirm the action
4. Wait for completion
5. Success message displays
6. Backup file created

**Backup Details:**
- **Location:** `database/backups/`
- **Format:** .sql file
- **Naming:** `pharmacy_backup_YYYY-MM-DD_HH-MM-SS.sql`
- **Contents:** Complete database (all tables and data)
- **Size:** Displayed in KB

**What's Included:**
- All medicines
- All sales and sale items
- All purchases and purchase items
- All customers
- All companies and suppliers
- All users
- All activity logs
- All system data

---

### Restore Backup

**How to Restore:**
1. Go to Backup & Restore page
2. Click "Choose File" under Restore section
3. Select a .sql backup file
4. Click "Restore Database" button
5. Read warning carefully
6. Confirm the action
7. Wait for completion
8. Success message displays

**⚠️ IMPORTANT WARNINGS:**
- This will REPLACE ALL current data
- Create a backup before restoring
- All users will be logged out
- This action CANNOT be undone
- Test restore on development first

**When to Restore:**
- Data corruption
- Accidental deletion
- System migration
- Testing purposes
- Disaster recovery

---

### Manage Backups

**View Backups:**
- All backups listed in table
- Shows filename, size, creation date
- Sorted by date (newest first)

**Download Backup:**
1. Find backup in list
2. Click "Download" button
3. File downloads to computer
4. Save to external storage

**Delete Backup:**
1. Find backup in list
2. Click "Delete" button
3. Confirm deletion
4. Backup removed

**Best Practices:**
- Create backups before major changes
- Download backups to external storage
- Keep at least 3 recent backups
- Test restore process monthly
- Delete old backups to save space
- Store backups securely
- Never share backup files

---

### Backup Schedule Recommendations

**Daily:**
- Create backup at end of business day
- Download to external drive
- Verify backup file size

**Weekly:**
- Test restore process
- Archive weekly backup
- Clean up old backups

**Monthly:**
- Full system backup
- Offsite storage
- Documentation update

---

## 📋 ACTIVITY LOGS

### Overview
Complete system activity tracking for security, auditing, and compliance.

**Location:** Activity Logs (Admin menu)

**Access:** Admin only

---

### View Activity Logs

**How to View:**
1. Go to Activity Logs page
2. View statistics cards
3. Review logs table
4. Use filters as needed

**Statistics Dashboard:**
- **Total Logs:** Count in date range
- **Active Users:** Unique users
- **Today's Activity:** Current day logs
- **Date Range:** Selected period

---

### Filter Logs

**Filter Options:**
- **User:** Select specific user
- **Action:** Search by action text
- **Date From:** Start date
- **Date To:** End date
- **Limit:** 50, 100, 200, or 500 records

**How to Filter:**
1. Select user from dropdown (optional)
2. Enter action keyword (optional)
3. Set date range
4. Choose record limit
5. Click "Filter" button
6. View filtered results
7. Click "Clear Filters" to reset

**Default Settings:**
- Date Range: Last 7 days
- Limit: 50 records
- All users
- All actions

---

### Log Information

**Each log entry shows:**
- **ID:** Unique log identifier
- **Date & Time:** When action occurred
- **User:** Full name and username
- **Action:** What was done (color-coded)
- **Table:** Database table affected
- **Record ID:** Specific record
- **IP Address:** User's IP
- **Details:** Additional information

**Action Color Codes:**
- 🔵 Blue (Primary): Login actions
- 🟢 Green (Success): Create/Add actions
- 🟡 Yellow (Warning): Update/Edit actions
- 🔴 Red (Danger): Delete actions
- 🟣 Purple (Info): Other actions

---

### Logged Activities

**Automatically Logged:**
- User login
- User logout
- Sale created
- Purchase created
- Backup created
- Backup deleted
- Database restored
- User created
- User updated
- User deleted
- Medicine added
- Medicine updated
- Medicine deleted
- And more...

**Log Details Include:**
- Invoice numbers
- Purchase numbers
- Amounts
- Item counts
- File names
- User actions

---

### Use Cases

**Security Monitoring:**
- Track user logins
- Identify suspicious activity
- Monitor failed attempts
- Review access patterns

**Audit Trail:**
- Financial transactions
- Data modifications
- User actions
- System changes

**Compliance:**
- Regulatory requirements
- Data protection
- Transaction history
- User accountability

**Troubleshooting:**
- Identify errors
- Track changes
- Debug issues
- Review timeline

---

## 💡 BEST PRACTICES

### Daily Operations

**Morning:**
1. Check dashboard
2. Review expiry alerts
3. Check low stock items
4. Review today's schedule

**During Day:**
1. Process sales promptly
2. Update stock levels
3. Record purchases
4. Handle customer inquiries

**Evening:**
1. Generate daily reports
2. Create backup
3. Review sales summary
4. Plan for tomorrow

---

### Security

**Passwords:**
- Change default admin password immediately
- Use strong passwords (8+ characters)
- Mix letters, numbers, symbols
- Change passwords regularly
- Never share passwords

**Access Control:**
- Assign appropriate roles
- Deactivate unused accounts
- Review user access monthly
- Monitor activity logs
- Limit admin accounts

**Data Protection:**
- Create daily backups
- Download backups externally
- Test restore process
- Secure backup files
- Encrypt sensitive data

---

### Inventory Management

**Stock Control:**
- Set reorder levels
- Monitor low stock alerts
- Track expiry dates
- Update stock regularly
- Conduct physical counts

**Medicine Management:**
- Use batch numbers
- Track expiry dates
- Organize by category
- Use rack locations
- Regular audits

---

### Financial Management

**Sales:**
- Issue invoices promptly
- Verify payment amounts
- Calculate change accurately
- Record all transactions
- Daily reconciliation

**Purchases:**
- Verify supplier invoices
- Check quantities received
- Update prices
- Track payment status
- Maintain records

**Reporting:**
- Generate daily reports
- Review weekly trends
- Monthly analysis
- Export to Excel
- Archive reports

---

## 🔧 TROUBLESHOOTING

### Common Issues

**Cannot Login:**
- Check XAMPP services running
- Verify database connection
- Check username/password
- Clear browser cache
- Try different browser

**Database Connection Error:**
- Verify MySQL service running
- Check config/db.php settings
- Verify database exists
- Check user permissions
- Review error logs

**Charts Not Displaying:**
- Check internet connection (Chart.js CDN)
- Clear browser cache
- Check browser console
- Verify JavaScript enabled
- Try different browser

**Excel Download Not Working:**
- Check browser download settings
- Verify file permissions
- Check PHP memory limit
- Review server error logs
- Try different browser

**Stock Not Updating:**
- Check transaction processing
- Review error logs
- Verify database triggers
- Check user permissions
- Test simple transaction

**Backup Failed:**
- Check directory permissions
- Verify disk space
- Check database connection
- Review error message
- Try manual backup

**Slow Performance:**
- Check server resources
- Optimize database queries
- Add/rebuild indexes
- Clear old data
- Increase PHP memory
- Check network speed

---

### Error Messages

**"Insufficient stock":**
- Check medicine quantity
- Verify stock levels
- Update inventory
- Process purchase first

**"Invalid username or password":**
- Verify credentials
- Check caps lock
- Reset password if needed
- Contact administrator

**"Access denied":**
- Check user role
- Verify permissions
- Contact administrator
- Review access control

**"Backup failed":**
- Check directory permissions
- Verify disk space
- Review error details
- Try again

**"Restore failed":**
- Verify .sql file
- Check file format
- Review error message
- Contact support

---

### Getting Help

**Documentation:**
- README.md - Quick start
- SYSTEM_COMPLETE.md - Full system
- Module guides - Specific features
- This guide - Complete reference

**Support:**
- Check documentation first
- Review error messages
- Check activity logs
- Test in development
- Contact administrator

---

## 📊 SYSTEM STATISTICS

**Database:**
- 12 tables
- Normalized structure
- Indexed for performance
- ACID compliant
- Transaction support

**Security:**
- Bcrypt password hashing
- SQL injection prevention
- XSS protection
- Session management
- Role-based access
- Activity logging

**Features:**
- 10+ major modules
- 25+ pages
- 100+ functions
- Search & filter
- Backup & restore
- Activity logs
- Reports & analytics
- Excel export
- Invoice printing

---

## 🎉 CONCLUSION

This pharmacy management system is a complete, production-ready solution with all essential features for modern pharmacy operations.

**Key Strengths:**
- ✅ Complete functionality
- ✅ User-friendly interface
- ✅ Secure and reliable
- ✅ Well documented
- ✅ Easy to use
- ✅ Production ready

**Perfect For:**
- Small to medium pharmacies
- Retail pharmacy chains
- Hospital pharmacies
- Medical stores
- Healthcare facilities

---

**Version:** 1.1  
**Date:** March 2026  
**Status:** ✅ Production Ready  
**Support:** Complete documentation included

---

**Happy Managing! 🏥💊**
