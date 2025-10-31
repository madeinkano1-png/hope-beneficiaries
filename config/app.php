<?php
/**
 * Application Configuration
 */

// Application settings
define('APP_NAME', 'HoPE Beneficiaries Management System');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', '_token');
define('PASSWORD_MIN_LENGTH', 8);

// File upload settings
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_UPLOAD_TYPES', ['csv', 'xlsx', 'xls']);

// Pagination settings
define('RECORDS_PER_PAGE', 25);
define('MAX_RECORDS_PER_PAGE', 100);

// CSV processing settings
define('CSV_CHUNK_SIZE', 1000);
define('CSV_MAX_ROWS', 50000);

// Date and time settings
define('DEFAULT_TIMEZONE', 'Africa/Lagos');
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Validation rules
define('VALIDATION_RULES', [
    'nidhh' => [
        'required' => true,
        'max_length' => 50,
        'unique' => true
    ],
    'phone' => [
        'min_length' => 7,
        'max_length' => 15,
        'pattern' => '/^[\+]?[0-9\-\(\)\s]+$/'
    ],
    'age' => [
        'min' => 0,
        'max' => 120,
        'type' => 'integer'
    ],
    'gender' => [
        'options' => ['Male', 'Female', 'Other', 'Unknown']
    ],
    'tranche_status' => [
        'options' => ['NotStarted', 'Partial', 'Completed']
    ],
    'id_type' => [
        'options' => ['NIN', 'BVN', 'Voters Card', 'Drivers License', 'International Passport', 'Other']
    ]
]);

// User roles
define('USER_ROLES', [
    'admin' => 'Admin',
    'standard' => 'StandardUser'
]);

// Error messages
define('ERROR_MESSAGES', [
    'login_failed' => 'Invalid username or password',
    'access_denied' => 'Access denied. Insufficient permissions.',
    'session_expired' => 'Your session has expired. Please login again.',
    'file_upload_error' => 'File upload failed. Please try again.',
    'csv_validation_error' => 'CSV validation failed. Please check the file format.',
    'duplicate_nidhh' => 'Duplicate NIDHH found. Each beneficiary must have a unique NIDHH.',
    'database_error' => 'Database operation failed. Please try again.'
]);
?>
