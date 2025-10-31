<?php
/**
 * CSV Import Service
 * Handles CSV file validation, processing, and import
 */

class CsvImportService {
    private $db;
    private $beneficiaryModel;
    private $validationErrors = [];
    private $expectedHeaders = [
        'State', 'LGA', 'Ward', 'Community', 'nidhh', 'HouseHoldNo', 'HAddress',
        'TrancheStatus', 'TotalAmount',
        'FirstTrancheRecipient', 'FirstTrancheAccountNumber', 'FirstTrancheBankName',
        'FirstTranchePaymentDate', 'FirstTranchePhone', 'FirstTrancheGender',
        'FirstTrancheAge', 'FirstTrancheIDType',
        'SecondTrancheRecipient', 'SecondTrancheAccountNumber', 'SecondTrancheBankName',
        'SecondTranchePaymentDate', 'SecondTranchePhone', 'SecondTrancheGender',
        'SecondTrancheAge', 'SecondTrancheIDType',
        'ThirdTrancheRecipient', 'ThirdTrancheAccountNumber', 'ThirdTrancheBankName',
        'ThirdTranchePaymentDate', 'ThirdTranchePhone', 'ThirdTrancheGender',
        'ThirdTrancheAge', 'ThirdTrancheIDType'
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->beneficiaryModel = new Beneficiary();
    }
    
    /**
     * Validate and process CSV file
     */
    public function processFile($filePath, $userId) {
        $this->validationErrors = [];
        
        try {
            // Validate file exists and is readable
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new Exception("File not found or not readable");
            }
            
            // Validate file size
            $fileSize = filesize($filePath);
            if ($fileSize > MAX_UPLOAD_SIZE) {
                throw new Exception("File size exceeds maximum allowed size");
            }
            
            // Open file for reading
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new Exception("Unable to open file for reading");
            }
            
            // Read and validate headers
            $headers = fgetcsv($handle);
            if (!$this->validateHeaders($headers)) {
                fclose($handle);
                throw new Exception("Invalid CSV headers");
            }
            
            // Process rows
            $rows = [];
            $rowNumber = 1; // Start from 1 (header is row 0)
            $duplicateNidhhs = [];
            $existingNidhhs = $this->getExistingNidhhs();
            
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Validate row has correct number of columns
                if (count($row) !== count($this->expectedHeaders)) {
                    $this->validationErrors[] = [
                        'row' => $rowNumber,
                        'error' => "Row has " . count($row) . " columns, expected " . count($this->expectedHeaders)
                    ];
                    continue;
                }
                
                // Combine headers with row data
                $data = array_combine($this->expectedHeaders, $row);
                
                // Validate row data
                $rowErrors = $this->validateRowData($data, $rowNumber);
                if (!empty($rowErrors)) {
                    $this->validationErrors = array_merge($this->validationErrors, $rowErrors);
                    continue;
                }
                
                // Check for duplicate nidhh within file
                $nidhh = trim($data['nidhh']);
                if (in_array($nidhh, $duplicateNidhhs)) {
                    $this->validationErrors[] = [
                        'row' => $rowNumber,
                        'nidhh' => $nidhh,
                        'error' => "Duplicate NIDHH found within file"
                    ];
                    continue;
                }
                
                // Check if nidhh already exists in database
                if (in_array($nidhh, $existingNidhhs)) {
                    $this->validationErrors[] = [
                        'row' => $rowNumber,
                        'nidhh' => $nidhh,
                        'error' => "NIDHH already exists in database"
                    ];
                    continue;
                }
                
                $duplicateNidhhs[] = $nidhh;
                $rows[] = [
                    'row_number' => $rowNumber,
                    'data' => $this->sanitizeRowData($data)
                ];
                
                // Limit number of rows to prevent memory issues
                if (count($rows) >= CSV_MAX_ROWS) {
                    $this->validationErrors[] = [
                        'row' => $rowNumber,
                        'error' => "Maximum number of rows (" . CSV_MAX_ROWS . ") exceeded"
                    ];
                    break;
                }
            }
            
            fclose($handle);
            
            return [
                'success' => empty($this->validationErrors),
                'rows' => $rows,
                'total_rows' => count($rows),
                'errors' => $this->validationErrors,
                'error_count' => count($this->validationErrors)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'rows' => [],
                'errors' => $this->validationErrors
            ];
        }
    }
    
    /**
     * Import validated data to database
     */
    public function importData($rows, $userId, $sessionId = null) {
        $this->db->beginTransaction();
        
        try {
            $imported = 0;
            $errors = [];
            
            // Update import session status
            if ($sessionId) {
                $this->updateImportSession($sessionId, [
                    'status' => 'processing',
                    'total_rows' => count($rows)
                ]);
            }
            
            foreach ($rows as $rowData) {
                try {
                    $data = $rowData['data'];
                    $rowNumber = $rowData['row_number'];
                    
                    // Create beneficiary
                    $this->beneficiaryModel->create($data, $userId);
                    $imported++;
                    
                    // Update progress periodically
                    if ($sessionId && $imported % 100 === 0) {
                        $this->updateImportSession($sessionId, [
                            'processed_rows' => $imported
                        ]);
                    }
                    
                } catch (Exception $e) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'nidhh' => $data['nidhh'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            $this->db->commit();
            
            // Update final import session status
            if ($sessionId) {
                $this->updateImportSession($sessionId, [
                    'status' => 'completed',
                    'processed_rows' => $imported,
                    'successful_rows' => $imported,
                    'failed_rows' => count($errors),
                    'error_report' => json_encode($errors),
                    'completed_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return [
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
                'error_count' => count($errors)
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            
            // Update import session with error
            if ($sessionId) {
                $this->updateImportSession($sessionId, [
                    'status' => 'failed',
                    'error_report' => json_encode([['error' => $e->getMessage()]])
                ]);
            }
            
            throw $e;
        }
    }
    
    /**
     * Validate CSV headers
     */
    private function validateHeaders($headers) {
        if (!$headers || count($headers) !== count($this->expectedHeaders)) {
            return false;
        }
        
        // Check each header matches expected
        for ($i = 0; $i < count($this->expectedHeaders); $i++) {
            if (trim($headers[$i]) !== $this->expectedHeaders[$i]) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate individual row data
     */
    private function validateRowData($data, $rowNumber) {
        $errors = [];
        
        // Required fields validation
        $requiredFields = ['State', 'LGA', 'Ward', 'Community', 'nidhh'];
        foreach ($requiredFields as $field) {
            if (empty(trim($data[$field]))) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $field,
                    'error' => "Required field '{$field}' is empty"
                ];
            }
        }
        
        // NIDHH validation
        $nidhh = trim($data['nidhh']);
        if (strlen($nidhh) > 50) {
            $errors[] = [
                'row' => $rowNumber,
                'field' => 'nidhh',
                'error' => "NIDHH exceeds maximum length of 50 characters"
            ];
        }
        
        // Validate tranche status
        if (!empty($data['TrancheStatus'])) {
            $validStatuses = VALIDATION_RULES['tranche_status']['options'];
            if (!in_array($data['TrancheStatus'], $validStatuses)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'TrancheStatus',
                    'error' => "Invalid tranche status. Must be one of: " . implode(', ', $validStatuses)
                ];
            }
        }
        
        // Validate total amount
        if (!empty($data['TotalAmount'])) {
            if (!is_numeric($data['TotalAmount']) || floatval($data['TotalAmount']) < 0) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'TotalAmount',
                    'error' => "Total amount must be a positive number"
                ];
            }
        }
        
        // Validate tranche data
        $tranches = ['First', 'Second', 'Third'];
        foreach ($tranches as $tranche) {
            $errors = array_merge($errors, $this->validateTrancheData($data, $tranche, $rowNumber));
        }
        
        return $errors;
    }
    
    /**
     * Validate tranche-specific data
     */
    private function validateTrancheData($data, $tranche, $rowNumber) {
        $errors = [];
        
        // Phone validation
        $phoneField = $tranche . 'TranchePhone';
        if (!empty($data[$phoneField])) {
            $phone = trim($data[$phoneField]);
            if (!preg_match(VALIDATION_RULES['phone']['pattern'], $phone) ||
                strlen($phone) < VALIDATION_RULES['phone']['min_length'] ||
                strlen($phone) > VALIDATION_RULES['phone']['max_length']) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $phoneField,
                    'error' => "Invalid phone number format"
                ];
            }
        }
        
        // Gender validation
        $genderField = $tranche . 'TrancheGender';
        if (!empty($data[$genderField])) {
            $validGenders = VALIDATION_RULES['gender']['options'];
            if (!in_array($data[$genderField], $validGenders)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $genderField,
                    'error' => "Invalid gender. Must be one of: " . implode(', ', $validGenders)
                ];
            }
        }
        
        // Age validation
        $ageField = $tranche . 'TrancheAge';
        if (!empty($data[$ageField])) {
            $age = intval($data[$ageField]);
            if ($age < VALIDATION_RULES['age']['min'] || $age > VALIDATION_RULES['age']['max']) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $ageField,
                    'error' => "Age must be between " . VALIDATION_RULES['age']['min'] . " and " . VALIDATION_RULES['age']['max']
                ];
            }
        }
        
        // ID Type validation
        $idTypeField = $tranche . 'TrancheIDType';
        if (!empty($data[$idTypeField])) {
            $validIdTypes = VALIDATION_RULES['id_type']['options'];
            if (!in_array($data[$idTypeField], $validIdTypes)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $idTypeField,
                    'error' => "Invalid ID type. Must be one of: " . implode(', ', $validIdTypes)
                ];
            }
        }
        
        // Payment date validation
        $dateField = $tranche . 'TranchePaymentDate';
        if (!empty($data[$dateField])) {
            $date = trim($data[$dateField]);
            if (!$this->validateDate($date)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => $dateField,
                    'error' => "Invalid date format. Use YYYY-MM-DD"
                ];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate date format
     */
    private function validateDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Sanitize row data
     */
    private function sanitizeRowData($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $value = trim($value);
            
            // Convert empty strings to null for optional fields
            if ($value === '') {
                $value = null;
            }
            
            // Special handling for specific fields
            if (strpos($key, 'Amount') !== false && $value !== null) {
                $value = floatval($value);
            } elseif (strpos($key, 'Age') !== false && $value !== null) {
                $value = intval($value);
            } elseif (strpos($key, 'PaymentDate') !== false && $value !== null) {
                // Ensure date is in correct format
                $date = DateTime::createFromFormat('Y-m-d', $value);
                $value = $date ? $date->format('Y-m-d') : null;
            }
            
            $sanitized[$key] = $value;
        }
        
        return $sanitized;
    }
    
    /**
     * Get existing NIHDHs from database
     */
    private function getExistingNidhhs() {
        $sql = "SELECT nidhh FROM beneficiaries";
        $results = $this->db->fetchAll($sql);
        
        return array_column($results, 'nidhh');
    }
    
    /**
     * Create import session record
     */
    public function createImportSession($filename, $originalFilename, $fileSize, $userId) {
        $data = [
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_size' => $fileSize,
            'user_id' => $userId,
            'status' => 'pending'
        ];
        
        return $this->db->insert('csv_import_sessions', $data);
    }
    
    /**
     * Update import session
     */
    private function updateImportSession($sessionId, $data) {
        $this->db->update('csv_import_sessions', $data, 'id = :id', ['id' => $sessionId]);
    }
    
    /**
     * Get import session
     */
    public function getImportSession($sessionId) {
        $sql = "SELECT * FROM csv_import_sessions WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $sessionId]);
    }
    
    /**
     * Get validation errors
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }
    
    /**
     * Get expected headers
     */
    public function getExpectedHeaders() {
        return $this->expectedHeaders;
    }
}
?>
