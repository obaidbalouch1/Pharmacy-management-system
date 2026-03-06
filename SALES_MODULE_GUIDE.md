# Sales Module - Complete Guide

## Overview
The Sales Module is a complete, working system for managing pharmacy sales transactions with invoice generation, stock management, and customer tracking.

## Features

### 1. Sales Management (`admin/sales.php`)
- View all sales transactions
- Filter and search sales
- Quick access to invoice details
- Payment status tracking
- Cashier information

### 2. New Sale Creation (`admin/new_sale.php`)
- Dynamic medicine selection with real-time stock checking
- Customer selection (or walk-in customer option)
- Multiple payment methods (Cash, Card, UPI, Other)
- Automatic calculations:
  - Subtotal
  - Tax (configurable percentage)
  - Discount (configurable percentage)
  - Grand Total
- Real-time item management (add/remove/update quantities)
- Stock validation before adding items
- Notes field for additional information

### 3. Sale Processing (`admin/process_sale.php`)
- Transaction-based processing (ensures data integrity)
- Automatic invoice number generation
- Stock deduction after successful sale
- Automatic stock status updates
- Error handling and rollback on failure

### 4. View Sale Details (`admin/view_sale.php`)
- Complete sale information display
- Customer details
- Itemized list of medicines sold
- Payment information
- Quick access to print invoice

### 5. Invoice Printing (`admin/print_invoice.php`)
- Professional invoice layout
- Company header with logo space
- Customer billing information
- Itemized product list
- Tax and discount breakdown
- Payment details
- Signature sections
- Print-optimized CSS

## Database Tables Used

### Sales Table
```sql
- id: Primary key
- invoice_number: Unique invoice identifier
- customer_id: Link to customer (nullable for walk-in)
- user_id: Cashier who processed the sale
- sale_date: Transaction timestamp
- subtotal: Total before tax/discount
- tax_percentage: Tax rate applied
- tax_amount: Calculated tax
- discount_percentage: Discount rate applied
- discount_amount: Calculated discount
- grand_total: Final amount
- payment_method: Cash/Card/UPI/Other
- payment_status: Paid/Pending/Partial
- notes: Additional information
```

### Sale Items Table
```sql
- id: Primary key
- sale_id: Link to sales table
- medicine_id: Link to medicines table
- batch_number: Medicine batch
- quantity: Quantity sold
- unit_price: Price per unit
- subtotal: Line item total
```

## How to Use

### Creating a New Sale

1. Navigate to Sales → New Sale
2. Select customer (optional - leave blank for walk-in)
3. Choose payment method
4. Search and select medicines from dropdown
5. Adjust quantities as needed
6. Add tax percentage if applicable
7. Add discount percentage if applicable
8. Review totals
9. Add notes if needed
10. Click "Complete Sale"

### Viewing Sales

1. Navigate to Sales Management
2. Browse all sales in the table
3. Click "View" to see details
4. Click "Print" to generate invoice

### Printing Invoices

1. From sales list or sale details, click "Print Invoice"
2. Review invoice in new window
3. Click "Print Invoice" button or use Ctrl+P
4. Select printer and print

## Key Features

### Stock Management
- Automatic stock deduction on sale
- Real-time stock availability checking
- Prevents overselling
- Updates medicine status to "out_of_stock" when quantity reaches 0

### Transaction Safety
- Uses MySQL transactions
- Automatic rollback on errors
- Ensures data consistency
- Prevents partial sales

### Invoice Generation
- Unique invoice numbers (INV-YYYYMMDD-XXXX format)
- Automatic duplicate checking
- Sequential numbering

### Calculations
- Automatic subtotal calculation
- Configurable tax percentage
- Configurable discount percentage
- Real-time grand total updates

## API Endpoints

### Get Medicine Info
**Endpoint:** `admin/api/get_medicine_info.php?id={medicine_id}`
**Method:** GET
**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "medicine_name": "Paracetamol",
    "selling_price": 50.00,
    "quantity": 100,
    "batch_number": "BATCH001"
  }
}
```

## Customization

### Invoice Header
Edit `admin/print_invoice.php` to customize:
- Company name
- Address
- Contact information
- Logo (add image tag)
- GST number

### Tax Settings
Default tax is 0%. Modify in `admin/new_sale.php`:
```html
<input type="number" name="tax_percentage" id="tax_percentage" 
       value="0" min="0" max="100" step="0.01">
```

### Discount Settings
Default discount is 0%. Modify in `admin/new_sale.php`:
```html
<input type="number" name="discount_percentage" id="discount_percentage" 
       value="0" min="0" max="100" step="0.01">
```

## Security Features

- Session-based authentication
- SQL injection prevention (mysqli_real_escape_string)
- Input validation
- Transaction-based operations
- Stock validation

## Error Handling

The module includes comprehensive error handling:
- Stock availability checks
- Transaction rollback on failure
- User-friendly error messages
- Session-based error/success notifications

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- IE11: Basic support (no CSS Grid)

## Mobile Responsive

The sales module is responsive and works on:
- Desktop (1920px+)
- Laptop (1366px)
- Tablet (768px)
- Mobile (320px+)

## Future Enhancements

Potential additions:
- Barcode scanning
- Receipt printer integration
- SMS notifications
- Email invoices
- Sales analytics dashboard
- Return/refund management
- Loyalty points integration
- Multi-currency support

## Troubleshooting

### Sale not completing
- Check database connection
- Verify stock availability
- Check session authentication
- Review browser console for JavaScript errors

### Invoice not printing
- Check browser print settings
- Verify popup blocker settings
- Try different browser
- Check CSS print media queries

### Stock not updating
- Verify transaction completion
- Check database triggers
- Review error logs
- Ensure proper permissions

## Support

For issues or questions:
1. Check error messages in session
2. Review browser console
3. Check database logs
4. Verify user permissions
