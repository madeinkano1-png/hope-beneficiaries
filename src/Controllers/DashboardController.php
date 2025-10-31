<?php
/**
 * Dashboard Controller
 * Handles dashboard display and analytics
 */

class DashboardController {
    private $beneficiaryModel;
    
    public function __construct() {
        // Require authentication
        requireAuth();
        $this->beneficiaryModel = new Beneficiary();
    }
    
    /**
     * Display dashboard
     */
    public function index() {
        requirePermission('view_dashboard');
        
        try {
            // Get dashboard statistics
            $stats = $this->beneficiaryModel->getDashboardStats();
            
            // Get recent beneficiaries
            $recentBeneficiaries = $this->beneficiaryModel->getAll(1, 5);
            
            // Calculate additional metrics
            $totalPending = $stats['tranche_status']['NotStarted'] + $stats['tranche_status']['Partial'];
            $completionRate = $stats['total_beneficiaries'] > 0 
                ? round(($stats['tranche_status']['Completed'] / $stats['total_beneficiaries']) * 100, 1)
                : 0;
            
            // Get state distribution
            $stateDistribution = $this->getStateDistribution();
            
            // Get monthly disbursement trend (last 6 months)
            $disbursementTrend = $this->getMonthlyDisbursementTrend();
            
            // Prepare data for charts
            $chartData = [
                'tranche_status' => [
                    'labels' => ['Not Started', 'Partial', 'Completed'],
                    'data' => [
                        $stats['tranche_status']['NotStarted'],
                        $stats['tranche_status']['Partial'],
                        $stats['tranche_status']['Completed']
                    ],
                    'colors' => ['#6c757d', '#ffc107', '#198754']
                ],
                'gender_distribution' => [
                    'labels' => ['Male', 'Female'],
                    'data' => [
                        $stats['gender_distribution']['Male'],
                        $stats['gender_distribution']['Female']
                    ],
                    'colors' => ['#0d6efd', '#e83e8c']
                ],
                'state_distribution' => $stateDistribution,
                'disbursement_trend' => $disbursementTrend
            ];
            
            $pageTitle = "Dashboard - " . APP_NAME;
            $pageHeader = "Dashboard";
            $pageActions = '<a href="/beneficiaries" class="btn btn-primary">
                <i class="bi bi-people me-1"></i>
                View All Beneficiaries
            </a>';
            
            ob_start();
            include 'views/dashboard/index.php';
            $content = ob_get_clean();
            
            include 'views/layouts/app.php';
            
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error loading dashboard: " . $e->getMessage();
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Get state distribution for chart
     */
    private function getStateDistribution() {
        $db = Database::getInstance();
        $sql = "SELECT State, COUNT(*) as count FROM beneficiaries GROUP BY State ORDER BY count DESC LIMIT 10";
        $results = $db->fetchAll($sql);
        
        $labels = [];
        $data = [];
        
        foreach ($results as $result) {
            $labels[] = $result['State'];
            $data[] = $result['count'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * Get monthly disbursement trend
     */
    private function getMonthlyDisbursementTrend() {
        $db = Database::getInstance();
        
        // Get disbursements by month for the last 6 months
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(TotalAmount) as total_amount,
                    COUNT(*) as beneficiary_count
                FROM beneficiaries 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                    AND TrancheStatus != 'NotStarted'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month";
        
        $results = $db->fetchAll($sql);
        
        $labels = [];
        $amounts = [];
        $counts = [];
        
        foreach ($results as $result) {
            $labels[] = date('M Y', strtotime($result['month'] . '-01'));
            $amounts[] = floatval($result['total_amount']);
            $counts[] = intval($result['beneficiary_count']);
        }
        
        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'counts' => $counts
        ];
    }
    
    /**
     * Get analytics data (AJAX endpoint)
     */
    public function analytics() {
        requirePermission('view_dashboard');
        
        header('Content-Type: application/json');
        
        try {
            $stats = $this->beneficiaryModel->getDashboardStats();
            $stateDistribution = $this->getStateDistribution();
            $disbursementTrend = $this->getMonthlyDisbursementTrend();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'state_distribution' => $stateDistribution,
                    'disbursement_trend' => $disbursementTrend
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
?>
