-- Sample Data for Testing Sales Module
USE pharmacy_db;

-- Insert Sample Companies
INSERT INTO companies (company_name, contact_person, phone, email, city, country, status) VALUES
('PharmaCorp Ltd', 'John Smith', '+91-9876543210', 'contact@pharmacorp.com', 'Mumbai', 'India', 'active'),
('MediLife Industries', 'Sarah Johnson', '+91-9876543211', 'info@medilife.com', 'Delhi', 'India', 'active'),
('HealthCare Pharma', 'Michael Brown', '+91-9876543212', 'sales@healthcare.com', 'Bangalore', 'India', 'active');

-- Insert Sample Customers
INSERT INTO customers (customer_name, phone, email, address, city, gender) VALUES
('Rajesh Kumar', '+91-9123456789', 'rajesh@email.com', '123 MG Road', 'Mumbai', 'male'),
('Priya Sharma', '+91-9123456790', 'priya@email.com', '456 Park Street', 'Delhi', 'female'),
('Amit Patel', '+91-9123456791', 'amit@email.com', '789 Brigade Road', 'Bangalore', 'male'),
('Sneha Reddy', '+91-9123456792', 'sneha@email.com', '321 Jubilee Hills', 'Hyderabad', 'female');

-- Insert Sample Medicines
INSERT INTO medicines (medicine_name, generic_name, category_id, company_id, batch_number, barcode, 
    manufacturing_date, expiry_date, purchase_price, selling_price, mrp, quantity, reorder_level, 
    rack_location, description, dosage, status) VALUES
('Paracetamol 500mg', 'Paracetamol', 2, 1, 'BATCH001', 'BAR001', '2024-01-01', '2026-01-01', 
    20.00, 50.00, 55.00, 500, 50, 'A1', 'Pain relief and fever reducer', '1-2 tablets every 4-6 hours', 'available'),
('Amoxicillin 250mg', 'Amoxicillin', 1, 2, 'BATCH002', 'BAR002', '2024-02-01', '2025-12-01', 
    80.00, 150.00, 165.00, 300, 30, 'A2', 'Antibiotic for bacterial infections', '1 capsule 3 times daily', 'available'),
('Vitamin C 1000mg', 'Ascorbic Acid', 3, 3, 'BATCH003', 'BAR003', '2024-03-01', '2027-03-01', 
    30.00, 80.00, 90.00, 400, 40, 'B1', 'Vitamin supplement', '1 tablet daily', 'available'),
('Aspirin 75mg', 'Acetylsalicylic Acid', 4, 1, 'BATCH004', 'BAR004', '2024-01-15', '2026-01-15', 
    15.00, 40.00, 45.00, 600, 60, 'A3', 'Blood thinner and pain relief', '1 tablet daily', 'available'),
('Omeprazole 20mg', 'Omeprazole', 7, 2, 'BATCH005', 'BAR005', '2024-02-15', '2025-11-15', 
    50.00, 120.00, 135.00, 250, 25, 'B2', 'Reduces stomach acid', '1 capsule before breakfast', 'available'),
('Cetirizine 10mg', 'Cetirizine', 6, 3, 'BATCH006', 'BAR006', '2024-03-15', '2026-03-15', 
    10.00, 30.00, 35.00, 800, 80, 'C1', 'Antihistamine for allergies', '1 tablet at bedtime', 'available'),
('Metformin 500mg', 'Metformin', 5, 1, 'BATCH007', 'BAR007', '2024-01-20', '2025-12-20', 
    25.00, 60.00, 70.00, 350, 35, 'A4', 'Diabetes medication', '1 tablet twice daily with meals', 'available'),
('Ibuprofen 400mg', 'Ibuprofen', 2, 2, 'BATCH008', 'BAR008', '2024-02-20', '2026-02-20', 
    35.00, 85.00, 95.00, 450, 45, 'B3', 'Pain relief and anti-inflammatory', '1 tablet every 6-8 hours', 'available');

-- Note: To test the sales module, you can now:
-- 1. Login with username: admin, password: admin123
-- 2. Go to Sales → New Sale
-- 3. Select a customer or leave blank for walk-in
-- 4. Add medicines from the dropdown
-- 5. Adjust quantities, tax, and discount
-- 6. Complete the sale
-- 7. View and print the invoice

-- Sample Sale (Optional - for demonstration)
INSERT INTO sales (invoice_number, customer_id, user_id, subtotal, tax_percentage, tax_amount, 
    discount_percentage, discount_amount, grand_total, payment_method, payment_status, notes) 
VALUES ('INV-20240101-0001', 1, 1, 200.00, 5.00, 10.00, 2.00, 4.00, 206.00, 'cash', 'paid', 'Sample sale for testing');

-- Sample Sale Items
INSERT INTO sale_items (sale_id, medicine_id, batch_number, quantity, unit_price, subtotal) VALUES
(1, 1, 'BATCH001', 2, 50.00, 100.00),
(1, 3, 'BATCH003', 1, 80.00, 80.00),
(1, 6, 'BATCH006', 1, 30.00, 30.00);

-- Update medicine quantities after sample sale
UPDATE medicines SET quantity = quantity - 2 WHERE id = 1;
UPDATE medicines SET quantity = quantity - 1 WHERE id = 3;
UPDATE medicines SET quantity = quantity - 1 WHERE id = 6;
