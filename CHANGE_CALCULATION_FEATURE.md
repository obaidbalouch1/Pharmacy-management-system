# Change Calculation Feature

## Overview
The sales module now includes automatic change calculation to help cashiers quickly determine how much money to return to customers.

## How It Works

### During Sale Creation

1. **Add Items**: Select medicines and add them to the sale as usual
2. **Calculate Total**: The system automatically calculates:
   - Subtotal
   - Tax (if applicable)
   - Discount (if applicable)
   - Grand Total

3. **Enter Amount Paid**: 
   - Input the amount the customer gives you
   - The system automatically calculates the change to return

4. **Visual Feedback**:
   - **Red background**: Amount paid is less than total (insufficient payment)
   - **Green background**: Change amount is displayed (sufficient payment)
   - **Gray background**: No payment entered yet

### Example Scenarios

#### Scenario 1: Exact Payment
- Grand Total: Rs 100.00
- Amount Paid: Rs 100.00
- Change to Return: Rs 0.00

#### Scenario 2: Customer Pays More
- Grand Total: Rs 100.00
- Amount Paid: Rs 150.00
- Change to Return: Rs 50.00 (shown in green)

#### Scenario 3: Insufficient Payment
- Grand Total: Rs 100.00
- Amount Paid: Rs 80.00
- Change to Return: Rs 0.00 (shown in red)
- System will NOT allow sale completion

## Database Changes

### New Columns Added to `sales` Table:
```sql
- amount_paid: DECIMAL(10,2) - Amount customer paid
- change_amount: DECIMAL(10,2) - Change returned to customer
```

### To Update Your Database:
Run the SQL file:
```bash
mysql -u root -p pharmacy_db < database/update_sales_table.sql
```

Or manually execute:
```sql
ALTER TABLE sales 
ADD COLUMN amount_paid DECIMAL(10,2) DEFAULT 0 AFTER payment_status,
ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0 AFTER amount_paid;
```

## Features

✅ **Real-time Calculation**: Change updates instantly as you type
✅ **Visual Indicators**: Color-coded feedback for payment status
✅ **Validation**: Prevents completing sale with insufficient payment
✅ **Invoice Display**: Shows amount paid and change on printed invoices
✅ **Sale History**: Stores payment details for future reference

## User Interface

### New Sale Form
```
Grand Total:        Rs 100.00
Amount Paid:        [Input: 150.00]  Rs 150.00
Change to Return:   Rs 50.00 (in green)
```

### Invoice Display
```
Grand Total:        Rs 100.00
Amount Paid:        Rs 150.00
Change Returned:    Rs 50.00
```

## Validation Rules

1. **Minimum Payment**: Amount paid must be >= Grand Total
2. **Zero Payment**: If amount paid is 0, sale can still proceed (for credit sales)
3. **Negative Change**: System prevents negative change (shows 0.00)

## Benefits

### For Cashiers:
- Quick change calculation
- Reduces counting errors
- Faster checkout process
- Visual confirmation of correct payment

### For Management:
- Track exact amounts received
- Audit trail for cash handling
- Better cash reconciliation
- Reduced discrepancies

## Testing

### Test Case 1: Normal Sale with Change
1. Create a sale with total Rs 100.00
2. Enter amount paid: Rs 200.00
3. Verify change shows: Rs 100.00
4. Complete sale
5. Check invoice shows correct amounts

### Test Case 2: Exact Payment
1. Create a sale with total Rs 100.00
2. Enter amount paid: Rs 100.00
3. Verify change shows: Rs 0.00
4. Complete sale

### Test Case 3: Insufficient Payment
1. Create a sale with total Rs 100.00
2. Enter amount paid: Rs 50.00
3. Verify red warning appears
4. Try to complete sale
5. Verify error message appears

### Test Case 4: No Payment Entered
1. Create a sale with total Rs 100.00
2. Leave amount paid as 0
3. Complete sale (for credit sales)
4. Verify sale completes successfully

## Customization

### Change Default Amount Paid
Edit `admin/new_sale.php` line ~125:
```html
<input type="number" name="amount_paid" id="amount_paid" 
       class="form-control form-control-sm" value="0" min="0" step="0.01">
```

### Modify Color Indicators
Edit JavaScript in `admin/new_sale.php`:
```javascript
// Red for insufficient
changeRow.style.backgroundColor = '#fee2e2';

// Green for change
changeRow.style.backgroundColor = '#f0fdf4';

// Gray for default
changeRow.style.backgroundColor = '#f9fafb';
```

## Troubleshooting

### Issue: Change not calculating
**Solution**: Check browser console for JavaScript errors

### Issue: Database error on save
**Solution**: Run the update_sales_table.sql script to add new columns

### Issue: Change shows incorrect amount
**Solution**: Clear browser cache and refresh page

### Issue: Can't complete sale with sufficient payment
**Solution**: Ensure amount_paid is greater than or equal to grand_total

## Future Enhancements

Potential additions:
- Quick amount buttons (Rs 100, Rs 500, Rs 1000)
- Multiple payment methods in one sale
- Split payment (cash + card)
- Change denomination calculator
- Print change amount on receipt
- Cash drawer integration

## Version
Change Calculation Feature v1.0 - Added to Sales Module
