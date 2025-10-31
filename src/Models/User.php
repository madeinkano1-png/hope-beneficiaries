<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Authenticate user login
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE (username = :username OR email = :username) AND is_active = 1";
        $user = $this->db->fetch($sql, ['username' => $username]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        // Validate required fields
        $required = ['username', 'email', 'password', 'first_name', 'last_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }
        
        // Check if username or email already exists
        if ($this->usernameExists($data['username'])) {
            throw new Exception("Username already exists");
        }
        
        if ($this->emailExists($data['email'])) {
            throw new Exception("Email already exists");
        }
        
        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'standard';
        }
        
        return $this->db->insert('users', $data);
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Get user by ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get user by username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        return $this->db->fetch($sql, ['username' => $username]);
    }
    
    /**
     * Get user by email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->db->fetch($sql, ['email' => $email]);
    }
    
    /**
     * Get all users with pagination
     */
    public function getAll($page = 1, $limit = 25, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE username LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search";
            $params['search'] = "%{$search}%";
        }
        
        $sql = "SELECT id, username, email, role, first_name, last_name, is_active, last_login, created_at 
                FROM users {$whereClause} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total user count
     */
    public function getTotalCount($search = '') {
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = "WHERE username LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search";
            $params['search'] = "%{$search}%";
        }
        
        $sql = "SELECT COUNT(*) as total FROM users {$whereClause}";
        $result = $this->db->fetch($sql, $params);
        
        return $result['total'];
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $params = ['username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql, ['id' => $userId]);
    }
    
    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken($email) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->update('users', [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ], 'id = :id', ['id' => $user['id']]);
        
        return $token;
    }
    
    /**
     * Reset password using token
     */
    public function resetPassword($token, $newPassword) {
        $sql = "SELECT * FROM users WHERE password_reset_token = :token AND password_reset_expires > NOW()";
        $user = $this->db->fetch($sql, ['token' => $token]);
        
        if (!$user) {
            return false;
        }
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $this->db->update('users', [
            'password_hash' => $passwordHash,
            'password_reset_token' => null,
            'password_reset_expires' => null
        ], 'id = :id', ['id' => $user['id']]);
        
        return true;
    }
    
    /**
     * Toggle user active status
     */
    public function toggleActive($id) {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        
        $newStatus = $user['is_active'] ? 0 : 1;
        return $this->db->update('users', ['is_active' => $newStatus], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        return $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $permission) {
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Define role permissions
        $permissions = [
            'standard' => [
                'view_beneficiaries',
                'create_beneficiaries',
                'update_beneficiaries',
                'import_csv',
                'export_csv',
                'view_dashboard'
            ],
            'admin' => [
                'view_beneficiaries',
                'create_beneficiaries',
                'update_beneficiaries',
                'delete_beneficiaries',
                'import_csv',
                'export_csv',
                'view_dashboard',
                'manage_users',
                'view_audit_logs',
                'system_settings'
            ]
        ];
        
        return in_array($permission, $permissions[$user['role']] ?? []);
    }
}
?>
