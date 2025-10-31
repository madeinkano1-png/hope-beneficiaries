-- HoPE Beneficiaries and Tranche Payment Management System
-- Complete Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS hope_beneficiaries CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hope_beneficiaries;

-- Users table for authentication and authorization
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'standard') NOT NULL DEFAULT 'standard',
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Main beneficiaries table with nidhh as primary key
CREATE TABLE beneficiaries (
    nidhh VARCHAR(50) PRIMARY KEY,
    State VARCHAR(100) NOT NULL,
    LGA VARCHAR(100) NOT NULL,
    Ward VARCHAR(100) NOT NULL,
    Community VARCHAR(100) NOT NULL,
    HouseHoldNo VARCHAR(50),
    HAddress TEXT,
    TrancheStatus ENUM('NotStarted', 'Partial', 'Completed') DEFAULT 'NotStarted',
    TotalAmount DECIMAL(15,2) DEFAULT 0.00,
    
    -- First Tranche Details
    FirstTrancheRecipient VARCHAR(100),
    FirstTrancheAccountNumber VARCHAR(20),
    FirstTrancheBankName VARCHAR(100),
    FirstTranchePaymentDate DATE NULL,
    FirstTranchePhone VARCHAR(15),
    FirstTrancheGender ENUM('Male', 'Female', 'Other', 'Unknown'),
    FirstTrancheAge INT CHECK (FirstTrancheAge >= 0 AND FirstTrancheAge <= 120),
    FirstTrancheIDType ENUM('NIN', 'BVN', 'Voters Card', 'Drivers License', 'International Passport', 'Other'),
    FirstTrancheAmount DECIMAL(15,2) DEFAULT 0.00,
    
    -- Second Tranche Details
    SecondTrancheRecipient VARCHAR(100),
    SecondTrancheAccountNumber VARCHAR(20),
    SecondTrancheBankName VARCHAR(100),
    SecondTranchePaymentDate DATE NULL,
    SecondTranchePhone VARCHAR(15),
    SecondTrancheGender ENUM('Male', 'Female', 'Other', 'Unknown'),
    SecondTrancheAge INT CHECK (SecondTrancheAge >= 0 AND SecondTrancheAge <= 120),
    SecondTrancheIDType ENUM('NIN', 'BVN', 'Voters Card', 'Drivers License', 'International Passport', 'Other'),
    SecondTrancheAmount DECIMAL(15,2) DEFAULT 0.00,
    
    -- Third Tranche Details
    ThirdTrancheRecipient VARCHAR(100),
    ThirdTrancheAccountNumber VARCHAR(20),
    ThirdTrancheBankName VARCHAR(100),
    ThirdTranchePaymentDate DATE NULL,
    ThirdTranchePhone VARCHAR(15),
    ThirdTrancheGender ENUM('Male', 'Female', 'Other', 'Unknown'),
    ThirdTrancheAge INT CHECK (ThirdTrancheAge >= 0 AND ThirdTrancheAge <= 120),
    ThirdTrancheIDType ENUM('NIN', 'BVN', 'Voters Card', 'Drivers License', 'International Passport', 'Other'),
    ThirdTrancheAmount DECIMAL(15,2) DEFAULT 0.00,
    
    -- Audit fields
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    
    -- Indexes for performance
    INDEX idx_state (State),
    INDEX idx_lga (LGA),
    INDEX idx_ward (Ward),
    INDEX idx_community (Community),
    INDEX idx_tranche_status (TrancheStatus),
    INDEX idx_created_at (created_at),
    INDEX idx_updated_at (updated_at),
    INDEX idx_first_payment_date (FirstTranchePaymentDate),
    INDEX idx_second_payment_date (SecondTranchePaymentDate),
    INDEX idx_third_payment_date (ThirdTranchePaymentDate),
    
    -- Foreign key constraints
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Audit log table for tracking all changes
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50) NOT NULL,
    record_id VARCHAR(50) NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_action (action),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- CSV import sessions table for tracking import operations
CREATE TABLE csv_import_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    total_rows INT DEFAULT 0,
    processed_rows INT DEFAULT 0,
    successful_rows INT DEFAULT 0,
    failed_rows INT DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    error_report JSON NULL,
    user_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_started_at (started_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- System settings table for application configuration
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key),
    INDEX idx_is_active (is_active)
);

-- Bank list table for validation
CREATE TABLE banks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100) NOT NULL UNIQUE,
    bank_code VARCHAR(10),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_bank_name (bank_name),
    INDEX idx_bank_code (bank_code),
    INDEX idx_is_active (is_active)
);

-- User sessions table for session management
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create triggers for automatic audit logging
DELIMITER //

CREATE TRIGGER beneficiaries_after_insert
AFTER INSERT ON beneficiaries
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id, ip_address)
    VALUES ('beneficiaries', NEW.nidhh, 'INSERT', JSON_OBJECT(
        'nidhh', NEW.nidhh,
        'State', NEW.State,
        'LGA', NEW.LGA,
        'Ward', NEW.Ward,
        'Community', NEW.Community,
        'TrancheStatus', NEW.TrancheStatus,
        'TotalAmount', NEW.TotalAmount
    ), NEW.created_by, @user_ip);
END//

CREATE TRIGGER beneficiaries_after_update
AFTER UPDATE ON beneficiaries
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, user_id, ip_address)
    VALUES ('beneficiaries', NEW.nidhh, 'UPDATE', JSON_OBJECT(
        'nidhh', OLD.nidhh,
        'State', OLD.State,
        'LGA', OLD.LGA,
        'Ward', OLD.Ward,
        'Community', OLD.Community,
        'TrancheStatus', OLD.TrancheStatus,
        'TotalAmount', OLD.TotalAmount
    ), JSON_OBJECT(
        'nidhh', NEW.nidhh,
        'State', NEW.State,
        'LGA', NEW.LGA,
        'Ward', NEW.Ward,
        'Community', NEW.Community,
        'TrancheStatus', NEW.TrancheStatus,
        'TotalAmount', NEW.TotalAmount
    ), NEW.updated_by, @user_ip);
END//

CREATE TRIGGER beneficiaries_after_delete
AFTER DELETE ON beneficiaries
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, user_id, ip_address)
    VALUES ('beneficiaries', OLD.nidhh, 'DELETE', JSON_OBJECT(
        'nidhh', OLD.nidhh,
        'State', OLD.State,
        'LGA', OLD.LGA,
        'Ward', OLD.Ward,
        'Community', OLD.Community,
        'TrancheStatus', OLD.TrancheStatus,
        'TotalAmount', OLD.TotalAmount
    ), OLD.updated_by, @user_ip);
END//

DELIMITER ;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, role, first_name, last_name) VALUES
('admin', 'admin@hope.gov.ng', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator');

-- Insert some default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('app_name', 'HoPE Beneficiaries Management System', 'Application name'),
('max_upload_size', '52428800', 'Maximum file upload size in bytes (50MB)'),
('records_per_page', '25', 'Default number of records per page'),
('session_timeout', '3600', 'Session timeout in seconds'),
('enable_audit_log', '1', 'Enable audit logging'),
('default_tranche_amount', '50000.00', 'Default amount per tranche');

-- Insert common Nigerian banks
INSERT INTO banks (bank_name, bank_code) VALUES
('Access Bank', '044'),
('Citibank Nigeria', '023'),
('Diamond Bank', '063'),
('Ecobank Nigeria', '050'),
('Fidelity Bank', '070'),
('First Bank of Nigeria', '011'),
('First City Monument Bank', '214'),
('Guaranty Trust Bank', '058'),
('Heritage Bank', '030'),
('Keystone Bank', '082'),
('Polaris Bank', '076'),
('Providus Bank', '101'),
('Stanbic IBTC Bank', '221'),
('Standard Chartered Bank', '068'),
('Sterling Bank', '232'),
('Union Bank of Nigeria', '032'),
('United Bank For Africa', '033'),
('Unity Bank', '215'),
('Wema Bank', '035'),
('Zenith Bank', '057');
