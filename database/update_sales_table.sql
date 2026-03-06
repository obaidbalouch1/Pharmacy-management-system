-- Add customer_name column to sales table for walk-in customers
-- This allows entering custom customer names without creating customer records

USE pharmacy_db;

-- Add customer_name column after customer_id
ALTER TABLE sales 
ADD COLUMN customer_name VARCHAR(255) NULL AFTER customer_id;

-- Add columns for amount_paid and change_amount if they don't exist
ALTER TABLE sales 
ADD COLUMN amount_paid DECIMAL(10,2) DEFAULT 0 AFTER payment_status;

ALTER TABLE sales 
ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0 AFTER amount_paid;
