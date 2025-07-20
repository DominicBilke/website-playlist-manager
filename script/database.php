<?php
/**
 * Database Configuration
 * Centralized database connection settings
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    // Database configuration
    private $host = 'localhost';
    private $dbname = 'd03c87b2'; // Main database
    private $username = 'd03c87b1';
    private $password = 'WaBtpcMKcgf49wqp';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function getPDO() {
        return $this->pdo;
    }
    
    /**
     * Execute a query and return the result
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query failed: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    /**
     * Execute a query and return a single row
     */
    public function queryOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Execute a query and return all rows
     */
    public function queryAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute an INSERT, UPDATE, or DELETE query
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Get the last inserted ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Check if database exists and create if not
     */
    public function ensureDatabaseExists() {
        try {
            // Connect without database name to check if it exists
            $dsn = "mysql:host={$this->host};charset={$this->charset}";
            $tempPdo = new PDO($dsn, $this->username, $this->password);
            
            // Check if database exists
            $stmt = $tempPdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->dbname}'");
            if (!$stmt->fetch()) {
                // Create database
                $tempPdo->exec("CREATE DATABASE `{$this->dbname}` CHARACTER SET {$this->charset} COLLATE {$this->charset}_unicode_ci");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Database creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Initialize database tables
     */
    public function initializeTables() {
        try {
            $schemaFile = __DIR__ . '/../database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $this->pdo->exec($sql);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Table initialization failed: " . $e->getMessage());
            return false;
        }
    }
}

// Global database instance
function getDB() {
    return Database::getInstance();
}

// Global PDO instance for backward compatibility
function getPDO() {
    return Database::getInstance()->getPDO();
}
?> 