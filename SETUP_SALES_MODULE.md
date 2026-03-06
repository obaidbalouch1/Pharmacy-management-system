# Sales Module Setup Guide

## Quick Setup (5 Minutes)

### Step 1: Database Setup
The sales tables are already included in your main schema. If you haven't set up the database yet:

```bash
# Import the main schema
mysql -u root -p < database/schema.sql

# (Optional) Import sample data for testing
mysql -u root -p < database/sample_data.sql
```

Or using phpMyAdmin:
1. Open phpMyAdmin
2. Create database `pharmacy_db` (if not exists)
3. Import `database/schema.sql`
4. Import `database/sample_data.sql` (optional)

### Step 2: Verify Database Connection
Check `config/db.php` and ensure your database credentials are correct:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pharmacy_db');
```

### Step 3: Login
1. Open your browser and navigate to your project URL
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`

### Step 4: Test the Sales Module

#### Create Your First Sale:
1. Click on "Sales" in the sidebar
2. Click "+ New Sale" button
3. Select a customer (or leave blank for walk-in)
4. Choose payment method
5. Select medicines from the dropdown
6. Adjust quantities
7. Add tax/discount if needed
8. Click "Complete Sale"

#### View and Print Invoice:
1. After completing sale, you'll be redirected to sale details
2. Click "Print Invoice" to generate printable invoice
3. Use browser print function (Ctrl+P) to print

## File Structure

```
admin/
├── sales.php              # Main sales list page
├── new_sale.php           # Create new sale form
├── process_sale.php       # Backend processing
├── view_sale.php          # View sale details
├── print_invoice.php      # Printable invoice
└── api/
    └── get_medicine_info.php  # API endpoint

database/
├── schema.sql             # Main database schema
└── sample_data.sql        # Test data

css/
└── style.css              # Updated with sales styles
```

## Features Checklist

✅ Create new sales with multiple items
✅ Customer selection (or walk-in)
✅ Real-time stock checking
✅ Automatic calculations (subtotal, tax, discount)
✅ Multiple payment methods
✅ Invoice generation with unique numbers
✅ Automatic stock deduction
✅ Transaction-based processing
✅ Professional invoice printing
✅ Sale history and details view
✅ Responsive design

## Testing Checklist

### Test 1: Basic Sale
- [ ] Create a sale with 1 item
- [ ] Verify stock is deducted
- [ ] Check invoice is generated
- [ ] Print invoice

### Test 2: Multiple Items
- [ ] Create a sale with 3+ items
- [ ] Adjust quantities
- [ ] Remove an item
- [ ] Complete sale

### Test 3: Tax and Discount
- [ ] Add 5% tax
- [ ] Add 10% discount
- [ ] Verify calculations are correct

### Test 4: Customer Sale
- [ ] Select a customer
- [ ] Complete sale
- [ ] Verify customer info on invoice

### Test 5: Stock Validation
- [ ] Try to sell more than available stock
- [ ] Verify error message appears
- [ ] Check stock doesn't go negative

### Test 6: Invoice Printing
- [ ] Open print invoice
- [ ] Verify all details are correct
- [ ] Test print functionality
- [ ] Check print layout

## Common Issues & Solutions

### Issue: "No items in the sale!"
**Solution:** Make sure to add at least one medicine before clicking "Complete Sale"

### Issue: "Insufficient stock"
**Solution:** Check medicine quantity in inventory. Add more stock through purchases module.

### Issue: Invoice not printing
**Solution:** 
- Check browser popup blocker
- Try different browser
- Use Ctrl+P manually

### Issue: Stock not updating
**Solution:**
- Check database connection
- Verify transaction completed successfully
- Check for error messages in session

### Issue: Calculations incorrect
**Solution:**
- Clear browser cache
- Check JavaScript console for errors
- Verify tax/discount percentages

## Customization Tips

### Change Invoice Header
Edit `admin/print_invoice.php` line 60-65:
```php
<h1>YOUR PHARMACY NAME</h1>
<p>Your Address Here</p>
<p>Phone: Your Phone | Email: Your Email</p>
```

### Change Default Tax Rate
Edit `admin/new_sale.php` line 95:
```html
<input type="number" name="tax_percentage" id="tax_percentage" 
       value="5" min="0" max="100" step="0.01">
```

### Change Invoice Number Format
Edit `admin/process_sale.php` line 30:
```php
$invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
```

### Add Company Logo
Edit `admin/print_invoice.php` and add before line 60:
```html
<img src="../images/logo.png" alt="Logo" style="max-width: 150px;">
```

## Security Notes

1. Always use prepared statements for production
2. Implement CSRF protection
3. Add rate limiting for API endpoints
4. Use HTTPS in production
5. Regular database backups
6. Implement proper user permissions

## Performance Tips

1. Add indexes on frequently queried columns
2. Use pagination for large sales lists
3. Implement caching for medicine list
4. Optimize images for invoices
5. Use CDN for static assets

## Next Steps

After setting up the sales module:
1. Configure your company information in invoice
2. Add more medicines to inventory
3. Add customers to database
4. Train staff on using the system
5. Set up regular backups
6. Monitor sales reports

## Support

For detailed documentation, see `SALES_MODULE_GUIDE.md`

## Version
Sales Module v1.0 - Complete Working Implementation
