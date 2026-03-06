# Returns & Refund System - Complete Guide

## Overview
Complete returns/refund system that allows you to process medicine returns and automatically restore stock to inventory.

## Features Added

### 1. Invoice Search
**File:** `admin/search_invoice.php`
- Search invoices by invoice number or customer name
- Quick access to view, print, or return any sale
- Fast search with real-time results

### 2. Return Sale Page
**File:** `admin/return_sale.php`
- Select items to return (full or partial)
- Specify quantity for each item
- Add return reason (required)
- Real-time refund calculation
- Prevents duplicate returns

### 3. Process Return
**File:** `admin/process_return.php`
- Validates return quantities
- Creates return record
- Restores stock to inventory automatically
- Updates medicine status if needed
- Transaction-based (all or nothing)
- Activity logging

### 4. View Return Details
**File:** `admin/view_return.php`
- Complete return information
- Shows refund amount
- Lists returned items
- Links to original sale
- Confirms stock restoration

### 5. Database Tables
**File:** `database/create_returns_table.sql`
- `sale_returns` - Return records
- `sale_return_items` - Returned items details

## How to Use

### Search for an Invoice

1. **Access Search:**
   - Click "🔍 Search Invoice" in sidebar
   - Or go to: `admin/search_invoice.php`

2. **Search:**
   - Enter invoice number (e.g., INV-20260304-0001)
   - Or enter customer name
   - Click "Search"

3. **Results:**
   - View matching invoices
   - Click "Return" button to process return

### Process a Return

1. **Start Return:**
   - From search results, click "Return" button
   - Or from sales list, click "Return"
   - Or from view sale page

2. **Select Items:**
   - Check items to return
   - Adjust quantity if partial return
   - See refund amount update in real-time

3. **Enter Details:**
   - Return Reason (required): Why is it being returned?
   - Additional Notes (optional): Any extra information

4. **Review:**
   - Check total refund amount
   - Verify selected items
   - Confirm stock will be restored

5. **Submit:**
   - Click "Process Return & Refund"
   - Confirm the action
   - Stock automatically restored!

### View Return History

1. **From Return Confirmation:**
   - After processing, view return details
   - See all returned items
   - Confirm stock restoration

2. **From Original Sale:**
   - View sale page shows if returned
   - Link to return details

## What Happens During Return

### Automatic Actions:

1. **Stock Restoration:**
   - Returned quantity added back to inventory
   - Medicine status updated to "available" if was out of stock
   - Current stock increases immediately

2. **Financial Records:**
   - Return record created with refund amount
   - Original sale marked as returned
   - Return date and time recorded

3. **Activity Logging:**
   - Who processed the return
   - When it was processed
   - What was returned
   - Refund amount

4. **Data Integrity:**
   - Transaction-based processing
   - If any step fails, everything rolls back
   - No partial returns that could cause issues

## Example Scenarios

### Scenario 1: Full Return
**Customer returns entire purchase**
- Original Sale: 3 medicines, Rs 500
- Return: All 3 medicines
- Refund: Rs 500
- Stock: All quantities restored

### Scenario 2: Partial Return
**Customer returns some items**
- Original Sale: Medicine A (5 qty), Medicine B (3 qty)
- Return: Medicine A (2 qty only)
- Refund: Calculated for 2 units of Medicine A
- Stock: Only 2 units of Medicine A restored

### Scenario 3: Damaged Product
**Medicine damaged, customer wants refund**
- Return Reason: "Damaged product - bottle broken"
- Return: Full quantity
- Refund: Full amount
- Stock: Restored (can be removed manually if damaged)

## Database Structure

### sale_returns Table
```sql
- id: Return ID
- sale_id: Original sale reference
- return_date: When returned
- return_amount: Total refund
- return_reason: Why returned
- processed_by: User who processed
- status: completed/pending/cancelled
- notes: Additional information
```

### sale_return_items Table
```sql
- id: Return item ID
- return_id: Return reference
- sale_item_id: Original sale item
- medicine_id: Medicine reference
- quantity_returned: How many returned
- unit_price: Price per unit
- subtotal: Total for this item
```

## Security & Validation

### Prevents:
- ✓ Duplicate returns (can't return same sale twice)
- ✓ Over-returning (can't return more than sold)
- ✓ Invalid quantities (must be positive)
- ✓ Unauthorized access (login required)

### Validates:
- ✓ Return quantity ≤ sold quantity
- ✓ Return reason is provided
- ✓ Sale exists and is valid
- ✓ Items belong to the sale

### Logs:
- ✓ Who processed the return
- ✓ When it was processed
- ✓ What was returned
- ✓ Refund amount

## Menu Navigation

**Sidebar Menu:**
- 🛒 Sales - View all sales
- 🔍 Search Invoice - Find specific invoice
- (From any sale) → Return button

**Quick Access:**
- Dashboard → Recent sales → View → Return
- Sales list → Return button
- Search invoice → Return button

## Reports & Tracking

### View Returns:
- Individual return details page
- Shows refund amount
- Lists returned items
- Confirms stock restoration

### Activity Logs:
- All returns logged
- Searchable by user, date, amount
- Audit trail maintained

## Best Practices

### When Processing Returns:

1. **Verify Customer:**
   - Check original invoice
   - Confirm customer identity
   - Verify purchase date

2. **Inspect Items:**
   - Check condition of returned items
   - Note any damage in notes field
   - Take photos if needed (external system)

3. **Document Reason:**
   - Be specific in return reason
   - Include relevant details
   - Use notes for additional context

4. **Check Stock:**
   - Verify items are being added back
   - Check if items are still sellable
   - Remove damaged items manually if needed

5. **Process Refund:**
   - Confirm refund amount with customer
   - Process payment refund (external)
   - Give customer return receipt

## Troubleshooting

### Can't Find Invoice?
- Check spelling of customer name
- Try partial invoice number
- Check date range in sales list

### Return Button Disabled?
- Sale already returned (check notes)
- Check if you have permission
- Verify sale exists

### Stock Not Updating?
- Check medicine inventory
- Verify return was completed
- Check activity logs
- Contact administrator

### Wrong Amount Refunded?
- Returns are based on original sale prices
- Partial returns calculate proportionally
- Check return items list

## Files Created/Modified

**Created:**
1. `database/create_returns_table.sql` - Database schema
2. `admin/search_invoice.php` - Invoice search
3. `admin/return_sale.php` - Return form
4. `admin/process_return.php` - Process return
5. `admin/view_return.php` - View return details
6. `RETURNS_FEATURE_GUIDE.md` - This guide

**Modified:**
1. `admin/includes/header.php` - Added search menu link

## Benefits

✅ **For Business:**
- Track all returns
- Maintain accurate inventory
- Financial records complete
- Audit trail for compliance

✅ **For Staff:**
- Easy to process returns
- Automatic stock restoration
- Clear refund calculations
- Prevents errors

✅ **For Customers:**
- Quick return process
- Transparent refund amounts
- Professional service
- Receipt of return

## Future Enhancements

Possible additions:
- Return receipt printing
- Return statistics/reports
- Partial refund options
- Store credit instead of refund
- Return approval workflow
- Damaged item tracking
- Return reasons analytics

Your pharmacy now has a complete returns system! 🎉
