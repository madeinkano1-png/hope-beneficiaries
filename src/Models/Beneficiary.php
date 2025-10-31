<?php
/**
 * Beneficiary Model
 * Handles beneficiary data management and tranche operations
 */

class Beneficiary {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create new beneficiary
     */
    public function create($data, $userId) {
        // Validate required fields
        $required = ['nidhh', 'State', 'LGA', 'Ward', 'Community'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }
        
        // Check if nidhh already exists
        if ($this->exists($data['nidhh'])) {
            throw new Exception("Beneficiary with NIDHH {$data['nidhh']} already exists");
        }
        
        // Add audit fields
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;
        
        // Calculate total amount based on tranche amounts
        $data['TotalAmount'] = $this->calculateTotalAmount($data);
        
        // Update tranche status
        $data['TrancheStatus'] = $this->calculateTrancheStatus($data);
        
        return $this->db->insert('beneficiaries', $data);
    }
    
    /**
     * Update beneficiary
     */
    public function update($nidhh, $data, $userId) {
        if (!$this->exists($nidhh)) {
            throw new Exception("Beneficiary not found");
        }
        
        // Add audit fields
        $data['updated_by'] = $userId;
        
        // Calculate total amount based on tranche amounts
        $data['TotalAmount'] = $this->calculateTotalAmount($data);
        
        // Update tranche status
        $data['TrancheStatus'] = $this->calculateTrancheStatus($data);
        
        return $this->db->update('beneficiaries', $data, 'nidhh = :nidhh', ['nidhh' => $nidhh]);
    }
    
    /**
     * Get beneficiary by nidhh
     */
    public function findByNidhh($nidhh) {
        $sql = "SELECT * FROM beneficiaries WHERE nidhh = :nidhh";
        return $this->db->fetch($sql, ['nidhh' => $nidhh]);
    }
    
    /**
     * Get all beneficiaries with pagination and filtering
     */
    public function getAll($page = 1, $limit = 25, $filters = []) {
        $offset = ($page - 1) * $limit;
        
        $whereConditions = [];
        $params = [];
        
        // Apply filters
        if (!empty($filters['state'])) {
            $whereConditions[] = "State = :state";
            $params['state'] = $filters['state'];
        }
        
        if (!empty($filters['lga'])) {
            $whereConditions[] = "LGA = :lga";
            $params['lga'] = $filters['lga'];
        }
        
        if (!empty($filters['ward'])) {
            $whereConditions[] = "Ward = :ward";
            $params['ward'] = $filters['ward'];
        }
        
        if (!empty($filters['community'])) {
            $whereConditions[] = "Community = :community";
            $params['community'] = $filters['community'];
        }
        
        if (!empty($filters['tranche_status'])) {
            $whereConditions[] = "TrancheStatus = :tranche_status";
            $params['tranche_status'] = $filters['tranche_status'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(nidhh LIKE :search OR HouseHoldNo LIKE :search OR HAddress LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT * FROM beneficiaries {$whereClause} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total count with filters
     */
    public function getTotalCount($filters = []) {
        $whereConditions = [];
        $params = [];
        
        // Apply same filters as getAll method
        if (!empty($filters['state'])) {
            $whereConditions[] = "State = :state";
            $params['state'] = $filters['state'];
        }
        
        if (!empty($filters['lga'])) {
            $whereConditions[] = "LGA = :lga";
            $params['lga'] = $filters['lga'];
        }
        
        if (!empty($filters['ward'])) {
            $whereConditions[] = "Ward = :ward";
            $params['ward'] = $filters['ward'];
        }
        
        if (!empty($filters['community'])) {
            $whereConditions[] = "Community = :community";
            $params['community'] = $filters['community'];
        }
        
        if (!empty($filters['tranche_status'])) {
            $whereConditions[] = "TrancheStatus = :tranche_status";
            $params['tranche_status'] = $filters['tranche_status'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(nidhh LIKE :search OR HouseHoldNo LIKE :search OR HAddress LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT COUNT(*) as total FROM beneficiaries {$whereClause}";
        $result = $this->db->fetch($sql, $params);
        
        return $result['total'];
    }
    
    /**
     * Check if beneficiary exists
     */
    public function exists($nidhh) {
        $sql = "SELECT COUNT(*) as count FROM beneficiaries WHERE nidhh = :nidhh";
        $result = $this->db->fetch($sql, ['nidhh' => $nidhh]);
        return $result['count'] > 0;
    }
    
    /**
     * Delete beneficiary
     */
    public function delete($nidhh, $userId) {
        if (!$this->exists($nidhh)) {
            throw new Exception("Beneficiary not found");
        }
        
        // Update the updated_by field before deletion for audit trail
        $this->db->update('beneficiaries', ['updated_by' => $userId], 'nidhh = :nidhh', ['nidhh' => $nidhh]);
        
        return $this->db->delete('beneficiaries', 'nidhh = :nidhh', ['nidhh' => $nidhh]);
    }
    
    /**
     * Get unique values for filters
     */
    public function getUniqueStates() {
        $sql = "SELECT DISTINCT State FROM beneficiaries ORDER BY State";
        return $this->db->fetchAll($sql);
    }
    
    public function getUniqueLGAs($state = null) {
        $sql = "SELECT DISTINCT LGA FROM beneficiaries";
        $params = [];
        
        if ($state) {
            $sql .= " WHERE State = :state";
            $params['state'] = $state;
        }
        
        $sql .= " ORDER BY LGA";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUniqueWards($state = null, $lga = null) {
        $whereConditions = [];
        $params = [];
        
        if ($state) {
            $whereConditions[] = "State = :state";
            $params['state'] = $state;
        }
        
        if ($lga) {
            $whereConditions[] = "LGA = :lga";
            $params['lga'] = $lga;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT DISTINCT Ward FROM beneficiaries {$whereClause} ORDER BY Ward";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUniqueCommunities($state = null, $lga = null, $ward = null) {
        $whereConditions = [];
        $params = [];
        
        if ($state) {
            $whereConditions[] = "State = :state";
            $params['state'] = $state;
        }
        
        if ($lga) {
            $whereConditions[] = "LGA = :lga";
            $params['lga'] = $lga;
        }
        
        if ($ward) {
            $whereConditions[] = "Ward = :ward";
            $params['ward'] = $ward;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        $sql = "SELECT DISTINCT Community FROM beneficiaries {$whereClause} ORDER BY Community";
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Calculate total amount from tranche amounts
     */
    private function calculateTotalAmount($data) {
        $total = 0;
        
        if (!empty($data['FirstTrancheAmount'])) {
            $total += floatval($data['FirstTrancheAmount']);
        }
        
        if (!empty($data['SecondTrancheAmount'])) {
            $total += floatval($data['SecondTrancheAmount']);
        }
        
        if (!empty($data['ThirdTrancheAmount'])) {
            $total += floatval($data['ThirdTrancheAmount']);
        }
        
        return $total;
    }
    
    /**
     * Calculate tranche status based on payment dates
     */
    private function calculateTrancheStatus($data) {
        $completedTranches = 0;
        
        if (!empty($data['FirstTranchePaymentDate'])) {
            $completedTranches++;
        }
        
        if (!empty($data['SecondTranchePaymentDate'])) {
            $completedTranches++;
        }
        
        if (!empty($data['ThirdTranchePaymentDate'])) {
            $completedTranches++;
        }
        
        if ($completedTranches === 0) {
            return 'NotStarted';
        } elseif ($completedTranches === 3) {
            return 'Completed';
        } else {
            return 'Partial';
        }
    }
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total beneficiaries
        $sql = "SELECT COUNT(*) as total FROM beneficiaries";
        $result = $this->db->fetch($sql);
        $stats['total_beneficiaries'] = $result['total'];
        
        // Tranche status counts
        $sql = "SELECT TrancheStatus, COUNT(*) as count FROM beneficiaries GROUP BY TrancheStatus";
        $trancheStats = $this->db->fetchAll($sql);
        
        $stats['tranche_status'] = [
            'NotStarted' => 0,
            'Partial' => 0,
            'Completed' => 0
        ];
        
        foreach ($trancheStats as $stat) {
            $stats['tranche_status'][$stat['TrancheStatus']] = $stat['count'];
        }
        
        // Total amount disbursed
        $sql = "SELECT SUM(TotalAmount) as total_amount FROM beneficiaries WHERE TrancheStatus != 'NotStarted'";
        $result = $this->db->fetch($sql);
        $stats['total_disbursed'] = $result['total_amount'] ?? 0;
        
        // Gender distribution
        $sql = "SELECT 
                    SUM(CASE WHEN FirstTrancheGender = 'Male' THEN 1 ELSE 0 END +
                        CASE WHEN SecondTrancheGender = 'Male' THEN 1 ELSE 0 END +
                        CASE WHEN ThirdTrancheGender = 'Male' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN FirstTrancheGender = 'Female' THEN 1 ELSE 0 END +
                        CASE WHEN SecondTrancheGender = 'Female' THEN 1 ELSE 0 END +
                        CASE WHEN ThirdTrancheGender = 'Female' THEN 1 ELSE 0 END) as female_count
                FROM beneficiaries";
        $result = $this->db->fetch($sql);
        
        $stats['gender_distribution'] = [
            'Male' => $result['male_count'] ?? 0,
            'Female' => $result['female_count'] ?? 0
        ];
        
        return $stats;
    }
    
    /**
     * Bulk insert beneficiaries (for CSV import)
     */
    public function bulkInsert($beneficiaries, $userId) {
        $this->db->beginTransaction();
        
        try {
            $inserted = 0;
            $errors = [];
            
            foreach ($beneficiaries as $index => $data) {
                try {
                    // Add audit fields
                    $data['created_by'] = $userId;
                    $data['updated_by'] = $userId;
                    
                    // Calculate total amount and tranche status
                    $data['TotalAmount'] = $this->calculateTotalAmount($data);
                    $data['TrancheStatus'] = $this->calculateTrancheStatus($data);
                    
                    $this->db->insert('beneficiaries', $data);
                    $inserted++;
                } catch (Exception $e) {
                    $errors[] = [
                        'row' => $index + 1,
                        'nidhh' => $data['nidhh'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'inserted' => $inserted,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
?>
