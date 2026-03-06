# Store Settings Feature - Implementation Complete

## Overview
Admins can now customize store information that appears on receipts and throughout the system.

## What Was Added

### 1. Database Table
**File:** `database/create_settings_table.sql`
- Created `settings` table to store configuration
- Stores key-value pairs for all settings
- Includes default values for new installations

### 2. Settings Page (Admin Only)
**File:** `admin/settings.php`
- Full settings management interface
- Live receipt preview
- Only accessible to admin role users

**Editable Settings:**
- Store Name (e.g., "KamranVaccine")
- Store Tagline
- Store Address
- Phone Number
- Email Address
- GST/Tax Number
- Receipt Footer Message

### 3. Settings Helper Functions
**File:** `config/settings.php`
- `get_setting($conn, $key, $default)` - Get single setting
- `get_all_settings($conn)` - Get all settings as array
- Includes default fallback values

### 4. Updated Receipt Template
**File:** `admin/print_invoice.php`
- Now uses dynamic store settings
- Header shows custom store name and info
- Footer shows custom message
- All information pulled from database

### 5. Sidebar Menu
**File:** `admin/includes/header.php`
- Added "⚙️ Store Settings" link for admins
- Appears below "Backup & Restore"

## How to Use

### For Admins:
1. Login as admin
2. Click "⚙️ Store Settings" in sidebar
3. Edit store information:
   - Store Name: Your pharmacy name
   - Address: Full address
   - Phone: Contact number
   - GST: Tax registration number
   - Footer: Custom thank you message
4. Click "💾 Save Settings"
5. View live preview of receipt

### Receipt Preview:
- Shows exactly how receipt will look
- Updates in real-time as you type
- 80mm thermal printer format
- All customizations visible

## Features

**Security:**
- Only admin role can access settings
- All inputs sanitized and escaped
- Activity logging for all changes

**User Experience:**
- Live preview of changes
- Clear form labels and hints
- Success/error messages
- Responsive design

**Flexibility:**
- All text fields customizable
- Multi-line address support
- Optional fields (email, GST)
- Default values if not set

## Database Structure

```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Default Settings

```
store_name: "AsadsPharma"
store_tagline: "Management System"
store_address: "123 Medical Street"
store_phone: "+92 334 0540325"
store_email: "info@pharmacy.com"
store_gst: "29XXXXX1234X1ZX"
receipt_footer: "Thank you for your business!"
```

## Files Modified/Created

**Created:**
1. `database/create_settings_table.sql` - Database schema
2. `config/settings.php` - Helper functions
3. `admin/settings.php` - Settings management page
4. `STORE_SETTINGS_FEATURE.md` - This documentation

**Modified:**
1. `admin/print_invoice.php` - Use dynamic settings
2. `admin/includes/header.php` - Added settings menu link

## Testing

1. Access settings page: `admin/settings.php`
2. Change store name to "KamranVaccine"
3. Update address and phone
4. Save settings
5. Create a test sale
6. Print invoice - verify custom name appears
7. Check receipt preview matches actual print

## Benefits

- Professional branding on receipts
- Easy to update contact information
- No code changes needed for customization
- Consistent branding across system
- Admin-controlled configuration

## Future Enhancements

Possible additions:
- Logo upload for receipts
- Multiple language support
- Currency settings
- Tax rate configuration
- Receipt template selection
- Email settings for notifications
