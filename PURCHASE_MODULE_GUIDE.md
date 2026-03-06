# Purchase Management Module - Complete Guide

## Overview
The Purchase Management Module allows you to record medicine purchases from suppliers, automatically update stock levels, and track purchase history.

## Features

### 1. Purchase List (`admin/purchases.php`)
- View all purchase records
- Filter by supplier, date, or status
- Quick access to purchase details
- Payment status tracking
- Purchase history

### 2. New Purchase (`admin/new_purchase.php`)
- Select supplier from active suppliers list
- Set purchase date
- Add multiple medicines to purchase
- Edit batch numbers for each item
- Adjust unit prices
- Set quantities
- Automatic total calculations
- Tax calculation
- Payment status selection (Paid/Pending/Partial)
- Notes field

### 3. Process Purchase (`admin/process_purchase.php`)
- Transaction-based processing
- Automatic purchase number generation
- Stock addition to inventory
- Update medicine purchase prices
- Update batch numbers
- Set medicine status to 'available'
- Error handling with rollback

### 4. View Purchase (`admin/view_purchase.php`)
- Complete purchase information
- Supplier details
- Itemized list of medicines
- Payment status
- Total calculations
- Purchase history

## Key Features

### Automatic Stock Management
✅ Adds purchased quantity to existing stock
✅ Updates medicine purchase price
✅ Updates batch numbers
✅ Sets medicine status to 'available'
✅ No overselling - stock is increased

### Purchase Number Generation
- Format: `PUR-YYYYMMDD-XXXX`
- Example: `PUR-20240315-0001`
- Automatic duplicate checking
- Unique for each purchase

### Transaction Safety
- MySQL transactions ensure data integrity
- Automatic rollback on errors
- Prevents partial purchases
- Maintains database consistency

### Flexible Pricing
- Edit unit price for each item
- Different prices for different batches
- Update purchase price in inventory
- Track price changes

## Database Tables

### Purchases Table
```sql
- id: Primary key
- purchase_number: Unique identifier
- supplier_id: Link to supplier
- user_id: User who created purchase
- purchase_date: Date of purchase
- total_amount: Sum of all items
- tax_amount: Calculated tax
- grand_total: Final amount
- payment_status: paid/pending/partial
- notes: Additional information
- created_at: Timestamp
```

### Purchase Items Table
```sql
- id: Primary key
- purchase_id: Link to purchases
- medicine_id: Link to medicines
- quantity: Quantity purchased
- unit_price: Price per unit
- subtotal: Line item total
```

## How to Use

### Creating a New Purchase

1. **Navigate to Purchases**
   - Click "Purchases" in sidebar
   - Click "+ New Purchase" button

2. **Select Supplier**
   - Choose from active suppliers
   - If supplier doesn't exist, add via Suppliers menu first

3. **Set Purchase Date**
   - Defaults to today
   - Can select past or future dates

4. **Add Medicines**
   - Select medicine from dropdown
   - Medicine shows current purchase price
   - Click to add to list

5. **Edit Item Details**
   - Update batch number (optional)
   - Adjust unit price if needed
   - Set quantity purchased
   - Remove items if needed

6. **Set Tax**
   - Enter tax percentage if applicable
   - Tax amount calculated automatically

7. **Select Payment Status**
   - Paid: Full payment received
   - Pending: Payment not yet received
   - Partial: Partial payment received

8. **Add Notes**
   - Optional notes about the purchase
   - Invoice numbers, special terms, etc.

9. **Complete Purchase**
   - Click "Complete Purchase"
   - Stock automatically updated
   - Redirected to purchase details

### Viewing Purchases

1. Go to Purchases list
2. Click "View" on any purchase
3. See complete details including:
   - Purchase information
   - Supplier details
   - Items purchased
   - Total calculations

## Stock Update Logic

When a purchase is completed:

```
For each item:
1. Current Stock: 100 units
2. Purchase Quantity: 50 units
3. New Stock: 150 units (100 + 50)
4. Purchase Price: Updated to new price
5. Batch Number: Updated if provided
6. Status: Set to 'available'
```

## Payment Status Options

### Paid
- Full payment received
- Badge: Green
- Use when: Payment completed at time of purchase

### Pending
- No payment received yet
- Badge: Yellow/Warning
- Use when: Credit purchase, payment due later

### Partial
- Some payment received
- Badge: Yellow/Warning
- Use when: Advance payment, balance due

## Calculations

### Total Amount
```
Total = Sum of (Unit Price × Quantity) for all items
```

### Tax Amount
```
Tax = (Total Amount × Tax Percentage) / 100
```

### Grand Total
```
Grand Total = Total Amount + Tax Amount
```

## Example Scenarios

### Scenario 1: Simple Purchase
```
Supplier: PharmaCorp Ltd
Date: 2024-03-15
Items:
  - Paracetamol 500mg: 100 units @ Rs 20 = Rs 2,000
  - Amoxicillin 250mg: 50 units @ Rs 80 = Rs 4,000
Total Amount: Rs 6,000
Tax (5%): Rs 300
Grand Total: Rs 6,300
Payment: Paid
```

### Scenario 2: Purchase with Batch Update
```
Supplier: MediLife Industries
Date: 2024-03-15
Items:
  - Vitamin C 1000mg: 200 units @ Rs 30 = Rs 6,000
    Batch: BATCH-2024-03-A
Total Amount: Rs 6,000
Tax (0%): Rs 0
Grand Total: Rs 6,000
Payment: Pending
```

## Supplier Management

Before creating purchases, ensure suppliers are added:

1. Go to Suppliers menu
2. Add supplier with:
   - Supplier name (required)
   - Contact person
   - Phone number
   - Email
   - Address

## Best Practices

### 1. Regular Stock Updates
- Record purchases immediately upon receipt
- Verify quantities before completing
- Check batch numbers and expiry dates

### 2. Accurate Pricing
- Update unit prices if changed
- Keep track of price variations
- Document price changes in notes

### 3. Payment Tracking
- Set correct payment status
- Update status when payment received
- Use notes for payment terms

### 4. Batch Management
- Always enter batch numbers
- Update batch for each purchase
- Track different batches separately

### 5. Supplier Relations
- Keep supplier information updated
- Track purchase history per supplier
- Maintain good records for audits

## Reports & Analytics

### Available Information:
- Total purchases by date range
- Purchases by supplier
- Payment status summary
- Stock additions over time
- Purchase price trends

## Security Features

✅ Session-based authentication
✅ SQL injection prevention
✅ Input validation
✅ Transaction-based operations
✅ User tracking (who created purchase)

## Error Handling

### Common Errors & Solutions

**Error: "No items in the purchase!"**
- Solution: Add at least one medicine before completing

**Error: "Please select a supplier!"**
- Solution: Choose a supplier from dropdown

**Error: "Error updating stock"**
- Solution: Check medicine exists in database

**Error: Transaction failed**
- Solution: Check database connection and permissions

## Integration with Other Modules

### Medicines Module
- Stock automatically updated
- Purchase price updated
- Batch numbers updated
- Status set to available

### Suppliers Module
- Links to supplier information
- Tracks purchases per supplier
- Supplier contact details

### Reports Module
- Purchase history
- Stock movement reports
- Supplier performance
- Cost analysis

## Validation Rules

1. **Supplier**: Must be selected
2. **Purchase Date**: Required, valid date
3. **Items**: At least one item required
4. **Quantity**: Must be positive integer
5. **Unit Price**: Must be positive number
6. **Payment Status**: Must be paid/pending/partial

## Future Enhancements

Potential additions:
- Purchase returns/refunds
- Supplier payment tracking
- Purchase order management
- Automatic reorder suggestions
- Price comparison across suppliers
- Barcode scanning for items
- Bulk import from CSV
- Purchase approval workflow
- Supplier rating system
- Purchase analytics dashboard

## Troubleshooting

### Purchase not completing
- Check database connection
- Verify supplier exists
- Ensure items are valid
- Check user permissions

### Stock not updating
- Verify transaction completed
- Check medicine IDs are correct
- Review error logs
- Ensure database triggers work

### Incorrect calculations
- Clear browser cache
- Check JavaScript console
- Verify input values
- Test with simple numbers

## API Endpoints

Currently no API endpoints. Future versions may include:
- GET /api/purchases - List purchases
- POST /api/purchases - Create purchase
- GET /api/purchases/{id} - Get purchase details
- PUT /api/purchases/{id} - Update purchase

## Mobile Responsiveness

The purchase module is fully responsive:
- Desktop: Full layout with all features
- Tablet: Optimized table views
- Mobile: Stacked forms, scrollable tables

## Browser Compatibility

✅ Chrome/Edge: Full support
✅ Firefox: Full support
✅ Safari: Full support
⚠️ IE11: Basic support (no modern CSS)

## Performance Tips

1. Index frequently queried columns
2. Use pagination for large purchase lists
3. Cache supplier list
4. Optimize medicine dropdown
5. Limit date range in reports

## Backup & Recovery

### Important Data to Backup:
- purchases table
- purchase_items table
- medicines table (stock levels)
- suppliers table

### Recovery Process:
1. Restore database from backup
2. Verify stock levels
3. Check purchase numbers
4. Validate supplier links

## Version
Purchase Management Module v1.0 - Complete Implementation
