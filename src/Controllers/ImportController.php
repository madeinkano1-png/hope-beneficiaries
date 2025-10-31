<?php
/**
 * Import Controller
 * Handles CSV file upload and import operations
 */

class ImportController {
    private $csvImportService;
    
    public function __construct() {
        requireAuth();
        requirePermission('import_csv');
        $this->csvImportService = new CsvImportService();
    }
    
    /**
     * Display import form
     */
    public function index() {
        $pageTitle = "Import CSV - " . APP_NAME;
        $pageHeader = "Import Beneficiaries";
        $pageActions = '<a href="/beneficiaries" class="btn btn-outline-primary">
            <i class="bi bi-people me-1"></i>
            View Beneficiaries
        </a>';
        
        // Get expected headers for display
        $expectedHeaders = $this->csvImportService->getExpectedHeaders();
        
        ob_start();
        include 'views/import/upload.php';
        $content = ob_get_clean();
        
        include 'views/layouts/app.php';
    }
    
    /**
     * Handle file upload and validation
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /import');
            exit;
        }
        
        try {
            // Validate file upload
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload failed");
            }
            
            $file = $_FILES['csv_file'];
            
            // Validate file type
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, ALLOWED_UPLOAD_TYPES)) {
                throw new Exception("Invalid file type. Only CSV files are allowed.");
            }
            
            // Validate file size
            if ($file['size'] > MAX_UPLOAD_SIZE) {
                throw new Exception("File size exceeds maximum allowed size of " . (MAX_UPLOAD_SIZE / 1024 / 1024) . "MB");
            }
            
            // Generate unique filename
            $filename = uniqid('import_') . '_' . time() . '.csv';
            $uploadPath = UPLOAD_PATH . '/' . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Failed to save uploaded file");
            }
            
            // Create import session
            $currentUser = getCurrentUser();
            $sessionId = $this->csvImportService->createImportSession(
                $filename,
                $file['name'],
                $file['size'],
                $currentUser['id']
            );
            
            // Process and validate file
            $result = $this->csvImportService->processFile($uploadPath, $currentUser['id']);
            
            if ($result['success']) {
                // Store validation results in session for preview
                $_SESSION['import_data'] = [
                    'session_id' => $sessionId,
                    'filename' => $filename,
                    'original_filename' => $file['name'],
                    'rows' => $result['rows'],
                    'total_rows' => $result['total_rows']
                ];
                
                $_SESSION['flash_success'] = "File validated successfully. " . $result['total_rows'] . " rows ready for import.";
                header('Location: /import/preview');
            } else {
                // Validation failed, show errors
                $_SESSION['import_errors'] = $result['errors'];
                $_SESSION['flash_error'] = "File validation failed with " . $result['error_count'] . " errors.";
                header('Location: /import/errors');
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /import');
        }
        
        exit;
    }
    
    /**
     * Preview import data
     */
    public function preview() {
        if (!isset($_SESSION['import_data'])) {
            $_SESSION['flash_error'] = "No import data found. Please upload a file first.";
            header('Location: /import');
            exit;
        }
        
        $importData = $_SESSION['import_data'];
        $previewRows = array_slice($importData['rows'], 0, 10); // Show first 10 rows
        
        $pageTitle = "Preview Import - " . APP_NAME;
        $pageHeader = "Preview Import Data";
        $pageActions = '<a href="/import" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Upload
        </a>';
        
        ob_start();
        include 'views/import/preview.php';
        $content = ob_get_clean();
        
        include 'views/layouts/app.php';
    }
    
    /**
     * Execute import
     */
    public function execute() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /import');
            exit;
        }
        
        if (!isset($_SESSION['import_data'])) {
            $_SESSION['flash_error'] = "No import data found. Please upload a file first.";
            header('Location: /import');
            exit;
        }
        
        try {
            $importData = $_SESSION['import_data'];
            $currentUser = getCurrentUser();
            
            // Execute import
            $result = $this->csvImportService->importData(
                $importData['rows'],
                $currentUser['id'],
                $importData['session_id']
            );
            
            if ($result['success']) {
                $message = "Import completed successfully! ";
                $message .= $result['imported'] . " beneficiaries imported.";
                
                if ($result['error_count'] > 0) {
                    $message .= " " . $result['error_count'] . " rows had errors.";
                }
                
                $_SESSION['flash_success'] = $message;
                $_SESSION['import_result'] = $result;
                
                // Clean up session data
                unset($_SESSION['import_data']);
                
                header('Location: /import/results');
            } else {
                throw new Exception("Import failed");
            }
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Import failed: " . $e->getMessage();
            header('Location: /import');
        }
        
        exit;
    }
    
    /**
     * Show import results
     */
    public function results() {
        if (!isset($_SESSION['import_result'])) {
            $_SESSION['flash_error'] = "No import results found.";
            header('Location: /import');
            exit;
        }
        
        $result = $_SESSION['import_result'];
        
        $pageTitle = "Import Results - " . APP_NAME;
        $pageHeader = "Import Results";
        $pageActions = '<a href="/beneficiaries" class="btn btn-primary">
            <i class="bi bi-people me-1"></i>
            View Beneficiaries
        </a>';
        
        ob_start();
        include 'views/import/results.php';
        $content = ob_get_clean();
        
        // Clean up session data
        unset($_SESSION['import_result']);
        
        include 'views/layouts/app.php';
    }
    
    /**
     * Show import errors
     */
    public function errors() {
        if (!isset($_SESSION['import_errors'])) {
            $_SESSION['flash_error'] = "No import errors found.";
            header('Location: /import');
            exit;
        }
        
        $errors = $_SESSION['import_errors'];
        
        $pageTitle = "Import Errors - " . APP_NAME;
        $pageHeader = "Import Validation Errors";
        $pageActions = '<a href="/import" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Upload
        </a>';
        
        ob_start();
        include 'views/import/errors.php';
        $content = ob_get_clean();
        
        // Clean up session data
        unset($_SESSION['import_errors']);
        
        include 'views/layouts/app.php';
    }
    
    /**
     * Download sample CSV template
     */
    public function downloadTemplate() {
        $headers = $this->csvImportService->getExpectedHeaders();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="hope_beneficiaries_template.csv"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        
        // Output CSV headers
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        // Add sample row with example data
        $sampleRow = [
            'Lagos', 'Ikeja', 'Ward 1', 'Sample Community', 'SAMPLE001', 'HH001',
            '123 Sample Street Lagos', 'NotStarted', '0.00',
            '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', ''
        ];
        
        fputcsv($output, $sampleRow);
        fclose($output);
        exit;
    }
    
    /**
     * Get import status (AJAX)
     */
    public function status() {
        if (!isset($_GET['session_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Session ID required']);
            exit;
        }
        
        $sessionId = intval($_GET['session_id']);
        $session = $this->csvImportService->getImportSession($sessionId);
        
        if (!$session) {
            http_response_code(404);
            echo json_encode(['error' => 'Session not found']);
            exit;
        }
        
        // Calculate progress percentage
        $progress = 0;
        if ($session['total_rows'] > 0) {
            $progress = round(($session['processed_rows'] / $session['total_rows']) * 100, 1);
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $session['status'],
            'total_rows' => $session['total_rows'],
            'processed_rows' => $session['processed_rows'],
            'successful_rows' => $session['successful_rows'],
            'failed_rows' => $session['failed_rows'],
            'progress' => $progress,
            'error_report' => $session['error_report'] ? json_decode($session['error_report'], true) : null
        ]);
        exit;
    }
    
    /**
     * Cancel import operation
     */
    public function cancel() {
        if (isset($_SESSION['import_data'])) {
            $importData = $_SESSION['import_data'];
            
            // Update session status to cancelled
            $db = Database::getInstance();
            $db->update('csv_import_sessions', 
                ['status' => 'cancelled'], 
                'id = :id', 
                ['id' => $importData['session_id']]
            );
            
            // Clean up uploaded file
            $filePath = UPLOAD_PATH . '/' . $importData['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Clean up session data
            unset($_SESSION['import_data']);
            
            $_SESSION['flash_info'] = "Import operation cancelled.";
        }
        
        header('Location: /import');
        exit;
    }
}
?>
