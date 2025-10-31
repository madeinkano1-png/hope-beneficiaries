<?php
/**
 * Database Migration Runner
 * Run this script to set up the database schema
 */

require_once 'config/database.php';

class MigrationRunner {
    private $db;
    
    public function __construct() {
        // Connect without specifying database first
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $this->db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function runMigrations() {
        echo "Starting database migration...\n";
        
        try {
            // Read and execute the schema file
            $schema = file_get_contents('database/schema.sql');
            
            if ($schema === false) {
                throw new Exception("Could not read schema file");
            }
            
            // Split the schema into individual statements
            $statements = $this->splitSqlStatements($schema);
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || strpos($statement, '--') === 0) {
                    continue;
                }
                
                try {
                    $this->db->exec($statement);
                    echo "✓ Executed statement successfully\n";
                } catch (PDOException $e) {
                    // Skip if table already exists
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        echo "⚠ Table already exists, skipping...\n";
                        continue;
                    }
                    throw $e;
                }
            }
            
            echo "\n✅ Database migration completed successfully!\n";
            echo "Default admin user created:\n";
            echo "  Username: admin\n";
            echo "  Password: admin123\n";
            echo "  Email: admin@hope.gov.ng\n\n";
            
        } catch (Exception $e) {
            echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
            return false;
        }
        
        return true;
    }
    
    private function splitSqlStatements($sql) {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Split by semicolon, but be careful with DELIMITER statements
        $statements = [];
        $current = '';
        $delimiter = ';';
        
        $lines = explode("\n", $sql);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                continue;
            }
            
            // Handle DELIMITER changes
            if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
                $delimiter = trim($matches[1]);
                continue;
            }
            
            $current .= $line . "\n";
            
            // Check if statement ends with current delimiter
            if (substr(rtrim($line), -strlen($delimiter)) === $delimiter) {
                $statements[] = substr($current, 0, -strlen($delimiter) - 1);
                $current = '';
            }
        }
        
        // Add any remaining statement
        if (!empty(trim($current))) {
            $statements[] = $current;
        }
        
        return $statements;
    }
    
    public function checkConnection() {
        try {
            $this->db->query('SELECT 1');
            echo "✅ Database connection successful\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
}

// Run migrations if called directly
if (php_sapi_name() === 'cli') {
    $runner = new MigrationRunner();
    
    echo "HoPE Beneficiaries Management System - Database Migration\n";
    echo "========================================================\n\n";
    
    if ($runner->checkConnection()) {
        $runner->runMigrations();
    }
} else {
    echo "This script should be run from the command line.\n";
    echo "Usage: php migrate.php\n";
}
?>
