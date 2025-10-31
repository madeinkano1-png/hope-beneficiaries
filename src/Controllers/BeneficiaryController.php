<?php
/**
 * Beneficiary Controller
 * Handles CRUD operations for beneficiaries
 */

class BeneficiaryController {
    private $beneficiaryModel;
    
    public function __construct() {
        requireAuth();
        $this->beneficiaryModel = new Beneficiary();
    }
    
    /**
     * List all beneficiaries with filtering and pagination
     */
    public function index() {
        requirePermission('view_beneficiaries');
        
        // Handle export request
        if (isset($_GET['export'])) {
            $this->export();
            return;
        }
        
        // Get filter parameters
        $filters = [
            'state' => $_GET['state'] ?? '',
            'lga' => $_GET['lga'] ?? '',
            'ward' => $_GET['ward'] ?? '',
            'community' => $_GET['community'] ?? '',
            'tranche_status' => $_GET['tranche_status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        // Get pagination parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = intval($_GET['per_page'] ?? PAGINATION_PER_PAGE);
        
        try {
            // Get beneficiaries with filters
            $beneficiaries = $this->beneficiaryModel->getAll($page, $perPage, $filters);
            $totalCount = $this->beneficiaryModel->getCount($filters);
            
            // Calculate pagination
            $totalPages = ceil($totalCount / $perPage);
            $pagination = [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'total_pages' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
                'prev_page' => $page - 1,
                'next_page' => $page + 1
            ];
            
            // Get filter options
            $filterOptions = [
                'states' => $this->beneficiaryModel->getUniqueStates(),
                'lgas' => $this->beneficiaryModel->getUniqueLGAs($filters['state']),
                'wards' => $this->beneficiaryModel->getUniqueWards($filters['state'], $filters['lga']),
                'communities' => $this->beneficiaryModel->getUniqueCommunities($filters['state'], $filters['lga'], $filters['ward']),
                'tranche_statuses' => ['NotStarted', 'Partial', 'Completed']
            ];
            
            $pageTitle = "Beneficiaries - " . APP_NAME;
            $pageHeader = "Beneficiaries";
            $pageActions = '';
            
            if (hasPermission('create_beneficiaries')) {
                $pageActions .= '<a href="/beneficiaries/create" class="btn btn-primary me-2">
                    <i class="bi bi-person-plus me-1"></i>
                    Add Beneficiary
                </a>';
            }
            
            if (hasPermission('import_csv')) {
                $pageActions .= '<a href="/import" class="btn btn-success me-2">
                    <i class="bi bi-upload me-1"></i>
                    Import CSV
                </a>';
            }
            
            if (hasPermission('export_csv')) {
                $pageActions .= '<button type="button" class="btn btn-info" onclick="exportBeneficiaries()">
                    <i class="bi bi-download me-1"></i>
                    Export
                </button>';
            }
            
            ob_start();
            include 'views/beneficiaries/index.php';
            $content = ob_get_clean();
            
            include 'views/layouts/app.php';
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error loading beneficiaries: " . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Show create beneficiary form
     */
    public function create() {
        requirePermission('create_beneficiaries');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }
        
        // Get banks for dropdown
        $banks = $this->getBanks();
        
        $pageTitle = "Add Beneficiary - " . APP_NAME;
        $pageHeader = "Add New Beneficiary";
        $pageActions = '<a href="/beneficiaries" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to List
        </a>';
        
        ob_start();
        include 'views/beneficiaries/create.php';
        $content = ob_get_clean();
        
        include 'views/layouts/app.php';
    }
    
    /**
     * Store new beneficiary
     */
    private function store() {
        try {
            $data = $this->validateBeneficiaryData($_POST);
            $currentUser = getCurrentUser();
            
            $this->beneficiaryModel->create($data, $currentUser['id']);
            
            $_SESSION['flash_success'] = "Beneficiary created successfully!";
            header('Location: /beneficiaries');
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error creating beneficiary: " . $e->getMessage();
            header('Location: /beneficiaries/create');
        }
        
        exit;
    }
    
    /**
     * Show edit beneficiary form
     */
    public function edit() {
        requirePermission('edit_beneficiaries');
        
        $nidhh = $_GET['nidhh'] ?? '';
        if (empty($nidhh)) {
            $_SESSION['flash_error'] = "Beneficiary ID is required";
            header('Location: /beneficiaries');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($nidhh);
            return;
        }
        
        try {
            $beneficiary = $this->beneficiaryModel->findByNidhh($nidhh);
            if (!$beneficiary) {
                $_SESSION['flash_error'] = "Beneficiary not found";
                header('Location: /beneficiaries');
                exit;
            }
            
            // Get banks for dropdown
            $banks = $this->getBanks();
            
            $pageTitle = "Edit Beneficiary - " . APP_NAME;
            $pageHeader = "Edit Beneficiary: " . htmlspecialchars($beneficiary['nidhh']);
            $pageActions = '<a href="/beneficiaries" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>
                Back to List
            </a>
            <a href="/beneficiaries/view?nidhh=' . urlencode($beneficiary['nidhh']) . '" class="btn btn-outline-info">
                <i class="bi bi-eye me-1"></i>
                View Details
            </a>';
            
            ob_start();
            include 'views/beneficiaries/edit.php';
            $content = ob_get_clean();
            
            include 'views/layouts/app.php';
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error loading beneficiary: " . $e->getMessage();
            header('Location: /beneficiaries');
            exit;
        }
    }
    
    /**
     * Update beneficiary
     */
    private function update($nidhh) {
        try {
            $data = $this->validateBeneficiaryData($_POST);
            $currentUser = getCurrentUser();
            
            $this->beneficiaryModel->update($nidhh, $data, $currentUser['id']);
            
            $_SESSION['flash_success'] = "Beneficiary updated successfully!";
            header('Location: /beneficiaries');
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error updating beneficiary: " . $e->getMessage();
            header('Location: /beneficiaries/edit?nidhh=' . urlencode($nidhh));
        }
        
        exit;
    }
    
    /**
     * View beneficiary details
     */
    public function view() {
        requirePermission('view_beneficiaries');
        
        $nidhh = $_GET['nidhh'] ?? '';
        if (empty($nidhh)) {
            $_SESSION['flash_error'] = "Beneficiary ID is required";
            header('Location: /beneficiaries');
            exit;
        }
        
        try {
            $beneficiary = $this->beneficiaryModel->findByNidhh($nidhh);
            if (!$beneficiary) {
                $_SESSION['flash_error'] = "Beneficiary not found";
                header('Location: /beneficiaries');
                exit;
            }
            
            // Get audit trail
            $auditLogs = $this->getAuditLogs($nidhh);
            
            $pageTitle = "View Beneficiary - " . APP_NAME;
            $pageHeader = "Beneficiary Details: " . htmlspecialchars($beneficiary['nidhh']);
            $pageActions = '<a href="/beneficiaries" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>
                Back to List
            </a>';
            
            if (hasPermission('edit_beneficiaries')) {
                $pageActions .= '<a href="/beneficiaries/edit?nidhh=' . urlencode($beneficiary['nidhh']) . '" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>
                    Edit
                </a>';
            }
            
            ob_start();
            include 'views/beneficiaries/view.php';
            $content = ob_get_clean();
            
            include 'views/layouts/app.php';
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error loading beneficiary: " . $e->getMessage();
            header('Location: /beneficiaries');
            exit;
        }
    }
    
    /**
     * Delete beneficiary
     */
    public function delete() {
        requirePermission('delete_beneficiaries');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /beneficiaries');
            exit;
        }
        
        $nidhh = $_POST['nidhh'] ?? '';
        if (empty($nidhh)) {
            $_SESSION['flash_error'] = "Beneficiary ID is required";
            header('Location: /beneficiaries');
            exit;
        }
        
        try {
            $currentUser = getCurrentUser();
            $this->beneficiaryModel->delete($nidhh, $currentUser['id']);
            
            $_SESSION['flash_success'] = "Beneficiary deleted successfully!";
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error deleting beneficiary: " . $e->getMessage();
        }
        
        header('Location: /beneficiaries');
        exit;
    }
    
    /**
     * Export beneficiaries to CSV
     */
    public function export() {
        requirePermission('export_csv');
        
        // Get filter parameters
        $filters = [
            'state' => $_GET['state'] ?? '',
            'lga' => $_GET['lga'] ?? '',
            'ward' => $_GET['ward'] ?? '',
            'community' => $_GET['community'] ?? '',
            'tranche_status' => $_GET['tranche_status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        try {
            // Get all beneficiaries matching filters (no pagination for export)
            $beneficiaries = $this->beneficiaryModel->getAllForExport($filters);
            
            // Generate filename
            $filename = 'hope_beneficiaries_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            
            // Output CSV
            $output = fopen('php://output', 'w');
            
            // Write headers
            $headers = [
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
                'ThirdTrancheAge', 'ThirdTrancheIDType',
                'Created At', 'Updated At'
            ];
            
            fputcsv($output, $headers);
            
            // Write data rows
            foreach ($beneficiaries as $beneficiary) {
                $row = [
                    $beneficiary['State'],
                    $beneficiary['LGA'],
                    $beneficiary['Ward'],
                    $beneficiary['Community'],
                    $beneficiary['nidhh'],
                    $beneficiary['HouseHoldNo'],
                    $beneficiary['HAddress'],
                    $beneficiary['TrancheStatus'],
                    $beneficiary['TotalAmount'],
                    $beneficiary['FirstTrancheRecipient'],
                    $beneficiary['FirstTrancheAccountNumber'],
                    $beneficiary['FirstTrancheBankName'],
                    $beneficiary['FirstTranchePaymentDate'],
                    $beneficiary['FirstTranchePhone'],
                    $beneficiary['FirstTrancheGender'],
                    $beneficiary['FirstTrancheAge'],
                    $beneficiary['FirstTrancheIDType'],
                    $beneficiary['SecondTrancheRecipient'],
                    $beneficiary['SecondTrancheAccountNumber'],
                    $beneficiary['SecondTrancheBankName'],
                    $beneficiary['SecondTranchePaymentDate'],
                    $beneficiary['SecondTranchePhone'],
                    $beneficiary['SecondTrancheGender'],
                    $beneficiary['SecondTrancheAge'],
                    $beneficiary['SecondTrancheIDType'],
                    $beneficiary['ThirdTrancheRecipient'],
                    $beneficiary['ThirdTrancheAccountNumber'],
                    $beneficiary['ThirdTrancheBankName'],
                    $beneficiary['ThirdTranchePaymentDate'],
                    $beneficiary['ThirdTranchePhone'],
                    $beneficiary['ThirdTrancheGender'],
                    $beneficiary['ThirdTrancheAge'],
                    $beneficiary['ThirdTrancheIDType'],
                    $beneficiary['created_at'],
                    $beneficiary['updated_at']
                ];
                
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error exporting data: " . $e->getMessage();
            header('Location: /beneficiaries');
            exit;
        }
    }
    
    /**
     * Validate beneficiary data
     */
    private function validateBeneficiaryData($data) {
        $errors = [];
        
        // Required fields
        $requiredFields = ['State', 'LGA', 'Ward', 'Community', 'nidhh'];
        foreach ($requiredFields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $errors[] = "Field '{$field}' is required";
            }
        }
        
        // NIDHH validation
        $nidhh = trim($data['nidhh'] ?? '');
        if (strlen($nidhh) > 50) {
            $errors[] = "NIDHH cannot exceed 50 characters";
        }
        
        // Validate amounts
        $amountFields = ['TotalAmount', 'FirstTrancheAmount', 'SecondTrancheAmount', 'ThirdTrancheAmount'];
        foreach ($amountFields as $field) {
            if (!empty($data[$field]) && (!is_numeric($data[$field]) || floatval($data[$field]) < 0)) {
                $errors[] = "Field '{$field}' must be a positive number";
            }
        }
        
        // Validate ages
        $ageFields = ['FirstTrancheAge', 'SecondTrancheAge', 'ThirdTrancheAge'];
        foreach ($ageFields as $field) {
            if (!empty($data[$field])) {
                $age = intval($data[$field]);
                if ($age < 0 || $age > 120) {
                    $errors[] = "Field '{$field}' must be between 0 and 120";
                }
            }
        }
        
        // Validate genders
        $genderFields = ['FirstTrancheGender', 'SecondTrancheGender', 'ThirdTrancheGender'];
        $validGenders = ['Male', 'Female', 'Other', 'Unknown'];
        foreach ($genderFields as $field) {
            if (!empty($data[$field]) && !in_array($data[$field], $validGenders)) {
                $errors[] = "Field '{$field}' must be one of: " . implode(', ', $validGenders);
            }
        }
        
        // Validate dates
        $dateFields = ['FirstTranchePaymentDate', 'SecondTranchePaymentDate', 'ThirdTranchePaymentDate'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                $date = DateTime::createFromFormat('Y-m-d', $data[$field]);
                if (!$date || $date->format('Y-m-d') !== $data[$field]) {
                    $errors[] = "Field '{$field}' must be in YYYY-MM-DD format";
                }
            }
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
        
        // Sanitize data
        $sanitized = [];
        foreach ($data as $key => $value) {
            $value = trim($value);
            $sanitized[$key] = $value === '' ? null : $value;
        }
        
        return $sanitized;
    }
    
    /**
     * Get banks list
     */
    private function getBanks() {
        $db = Database::getInstance();
        $sql = "SELECT code, name FROM banks ORDER BY name";
        return $db->fetchAll($sql);
    }
    
    /**
     * Get audit logs for beneficiary
     */
    private function getAuditLogs($nidhh) {
        if (!hasPermission('view_audit_logs')) {
            return [];
        }
        
        $db = Database::getInstance();
        $sql = "SELECT al.*, u.username 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                WHERE al.table_name = 'beneficiaries' 
                AND JSON_EXTRACT(al.record_data, '$.nidhh') = :nidhh 
                ORDER BY al.created_at DESC 
                LIMIT 10";
        
        return $db->fetchAll($sql, ['nidhh' => $nidhh]);
    }
}
?>
