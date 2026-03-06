# How to Edit Store Name

## Quick Guide

### Step 1: Login as Admin
- Go to your pharmacy system
- Login with admin credentials

### Step 2: Access Settings
- Look at the left sidebar
- Click on "⚙️ Store Settings" (near the bottom, below "Backup & Restore")

### Step 3: Edit Store Name
- You'll see a form with all store information
- The first field is "Store Name" - currently shows "Pharma"
- Click in the field and change it to "KamranVaccine" (or any name you want)

### Step 4: Edit Other Information (Optional)
- Store Tagline: Subtitle below store name
- Store Address: Your full address
- Phone Number: Contact number
- Email: Store email
- GST/Tax Number: Registration number
- Receipt Footer: Thank you message

### Step 5: Save Changes
- Click the green "💾 Save Settings" button at the bottom
- You'll see a success message

### Step 6: Verify Changes
- Look at the "Receipt Preview" section on the same page
- Your new store name will appear in the preview
- Create a test sale and print invoice to see it on actual receipt
- Check the sidebar - it will now show your new store name

## Current Settings

Based on the database check:
- Store Name: **Pharma** (editable)
- Store Address: 123 Medical Street
- Store Phone: 00000000
- Store Tagline: Management System
- Receipt Footer: Thank you for your business!

## Where Store Name Appears

After changing the store name, it will appear in:
1. **Sidebar** - Top left of every admin page
2. **Receipts** - Header of printed invoices
3. **Receipt Footer** - "Powered by [Your Store Name] System"

## Troubleshooting

**Can't see Settings menu?**
- Make sure you're logged in as admin (not cashier/pharmacist)
- Only admin role can access settings

**Changes not showing?**
- Refresh the page (F5)
- Clear browser cache
- Log out and log back in

**Settings page not found?**
- Make sure file exists: `admin/settings.php`
- Check database table exists: `settings`

## Direct Access

You can also access settings directly:
- URL: `http://localhost/pharmacy/admin/settings.php`
- Or: `http://your-domain/admin/settings.php`

## Screenshot Guide

1. Login → Admin Dashboard
2. Sidebar → Click "⚙️ Store Settings"
3. Form → Edit "Store Name" field
4. Button → Click "💾 Save Settings"
5. Preview → See changes in receipt preview
6. Sidebar → See new name in sidebar header

## Need Help?

The store name is 100% editable. If you're having issues:
1. Make sure you're logged in as admin
2. Check the sidebar for "⚙️ Store Settings" link
3. The form field should be a normal text input (not disabled)
4. Try typing in the field - it should accept text
5. Click Save Settings button

The current store name "Pharma" can be changed to anything you want!
