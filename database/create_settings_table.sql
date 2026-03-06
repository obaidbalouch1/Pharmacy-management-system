-- Create settings table for store configuration
USE pharmacy_db;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('store_name', 'AsadsPharma'),
('store_address', '123 Medical Street'),
('store_phone', '+92 334 0540325'),
('store_email', 'info@asadspharmacy.com'),
('store_gst', '29XXXXX1234X1ZX'),
('store_tagline', 'Management System'),
('receipt_footer', 'Thank you for your business!')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
