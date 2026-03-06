# Customer Name Feature - Implementation Complete

## Overview
Added the ability to enter custom customer names for walk-in customers so their names appear on printed receipts.

## What Was Changed

### 1. Database Update
**File:** `database/update_sales_table.sql`
- Added `customer_name` VARCHAR(255) column to `sales` table
- This column stores custom customer names for walk-in customers
- Also ensures `amount_paid` and `change_amount` columns exist

**To apply the database changes:**
```sql
-- Run this SQL in your database:
USE pharmacy_db;

ALTER TABLE sales 
ADD COLUMN customer_name VARCHAR(255) NULL AFTER customer_id;

ALTER TABLE sales 
ADD COLUMN amount_paid DECIMAL(10,2) DEFAULT 0 AFTER payment_status;

ALTER TABLE sales 
ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0 AFTER amount_paid;
```

### 2. New Sale Form (admin/new_sale.php)
- Added "Or Enter Customer Name" input field
- When an existing customer is selected, their name auto-fills the field (read-only)
- When no customer is selected, the field is editable for manual entry
- If left blank, receipt will show "Walk-in Customer"

### 3. Sale Processing (admin/process_sale.php)
- Captures `customer_name` from POST data
- Stores it in the database when creating a sale
- Priority: custom name > selected customer > Walk-in Customer

### 4. Receipt Printing (admin/print_invoice.php)
- Updated to display customer name with priority:
  1. Custom `customer_name` from sales table (if entered)
  2. Customer name from customers table (if customer selected)
  3. "Walk-in Customer" (if neither)
- Receipt now shows the custom name on thermal printer

### 5. Bulk Sales (admin/bulk_sales.php)
- Added same customer name input field to each sale block
- Auto-fills when existing customer is selected
- Allows manual entry for walk-in customers

### 6. Bulk Sales Processing (admin/process_bulk_sales.php)
- Handles `customer_name` field for each sale
- Stores custom names in database

## How to Use

### For Single Sales (New Sale):
1. Go to "New Sale" page
2. Either:
   - Select an existing customer from dropdown (name auto-fills)
   - OR enter a custom name in "Or Enter Customer Name" field
   - OR leave both blank for "Walk-in Customer"
3. Add items and complete the sale
4. Print receipt - customer name will appear on receipt

### For Bulk Sales:
1. Go to "Bulk Sales Entry" page
2. For each sale block:
   - Select existing customer OR enter custom name
3. Add items to each sale
4. Process sales (individually or all at once)
5. Print receipts with customer names

## Receipt Display Priority
The system uses this priority when displaying customer name on receipt:
1. **Custom customer_name** (entered in "Or Enter Customer Name" field)
2. **Database customer name** (from selected customer)
3. **"Walk-in Customer"** (default if nothing entered)

## Benefits
- No need to create customer records for one-time walk-in customers
- Personalized receipts with customer names
- Faster checkout process
- Better customer experience
- Existing customer selection still works as before

## Files Modified
1. `database/update_sales_table.sql` - Database schema update
2. `admin/new_sale.php` - Added customer name input field
3. `admin/process_sale.php` - Handle customer_name in sale processing
4. `admin/print_invoice.php` - Display custom customer name on receipt
5. `admin/bulk_sales.php` - Added customer name input to bulk sales
6. `admin/process_bulk_sales.php` - Handle customer_name in bulk processing

## Testing
1. Run the SQL update script to add the column
2. Create a new sale with a custom customer name
3. Print the receipt and verify the name appears
4. Test with existing customer selection
5. Test with blank (should show "Walk-in Customer")
6. Test bulk sales with custom names

## Notes
- Customer name is optional - can be left blank
- Works for both single and bulk sales
- Thermal receipt format (80mm) displays name properly
- No changes needed to existing sales data
