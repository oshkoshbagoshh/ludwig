<?php

namespace App\Core;

use PDO;
use PDOException;
use Exception;

/**
 * Database connection class
 * 
 * Handles database connectivity and provides a PDO instance for database operations.
 * Supports both MySQL and SQLite connections based on configuration.
 */
class Database
{
    /**
     * @var PDO|null The PDO connection instance
     */
    private ?PDO $connection = null;
    
    /**
     * @var array The database configuration
     */
    private array $config;
    
    /**
     * Constructor
     * 
     * @param array|null $config Optional database configuration override
     */
    public function __construct(?array $config = null)
    {
        // Load configuration from config.php if not provided
        if ($config === null) {
            $mainConfig = require __DIR__ . '/../../config.php';
            $this->config = $mainConfig['database'] ?? [];
        } else {
            $this->config = $config;
        }
    }
    
    /**
     * Get the PDO connection instance
     * 
     * @return PDO The PDO connection instance
     * @throws Exception If connection fails
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Connect to the database
     * 
     * @return void
     * @throws Exception If connection fails
     */
    private function connect(): void
    {
        $connection = $this->config['connection'] ?? 'mysql';
        
        try {
            switch ($connection) {
                case 'sqlite':
                    $this->connectSqlite();
                    break;
                case 'mysql':
                default:
                    $this->connectMysql();
                    break;
            }
            
            // Set error mode to exceptions
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Use associative arrays for fetch by default
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Use native prepared statements
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Connect to MySQL database
     * 
     * @return void
     * @throws PDOException If connection fails
     */
    private function connectMysql(): void
    {
        $host = $this->config['mysql']['host'] ?? 'localhost';
        $port = $this->config['mysql']['port'] ?? 3306;
        $database = $this->config['mysql']['database'] ?? 'music_platform';
        $username = $this->config['mysql']['username'] ?? 'root';
        $password = $this->config['mysql']['password'] ?? '';
        $charset = $this->config['mysql']['charset'] ?? 'utf8mb4';
        
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        
        $this->connection = new PDO($dsn, $username, $password);
    }
    
    /**
     * Connect to SQLite database
     * 
     * @return void
     * @throws PDOException If connection fails
     */
    private function connectSqlite(): void
    {
        $path = $this->config['sqlite']['path'] ?? 'database/music.sqlite';
        
        // Ensure the path is absolute
        if (!str_starts_with($path, '/')) {
            $path = __DIR__ . '/../../' . $path;
        }
        
        // Ensure the directory exists
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new Exception("Failed to create directory: {$directory}");
        }
        
        $this->connection = new PDO("sqlite:{$path}");
    }
    
    /**
     * Begin a transaction
     * 
     * @return bool True on success or false on failure
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * Commit a transaction
     * 
     * @return bool True on success or false on failure
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * Roll back a transaction
     * 
     * @return bool True on success or false on failure
     */
    public function rollBack(): bool
    {
        return $this->getConnection()->rollBack();
    }
    
    /**
     * Execute a query and return the statement
     * 
     * @param string $query The SQL query
     * @param array $params The parameters to bind
     * @return \PDOStatement The PDO statement
     */
    public function query(string $query, array $params = []): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Execute a query and return all results
     * 
     * @param string $query The SQL query
     * @param array $params The parameters to bind
     * @return array The query results
     */
    public function fetchAll(string $query, array $params = []): array
    {
        return $this->query($query, $params)->fetchAll();
    }
    
    /**
     * Execute a query and return a single result
     * 
     * @param string $query The SQL query
     * @param array $params The parameters to bind
     * @return array|false The query result or false if no result
     */
    public function fetch(string $query, array $params = []): array|false
    {
        return $this->query($query, $params)->fetch();
    }
    
    /**
     * Execute a query and return the last inserted ID
     * 
     * @param string $query The SQL query
     * @param array $params The parameters to bind
     * @return string The last inserted ID
     */
    public function insert(string $query, array $params = []): string
    {
        $this->query($query, $params);
        return $this->getConnection()->lastInsertId();
    }
    
    /**
     * Execute a query and return the number of affected rows
     * 
     * @param string $query The SQL query
     * @param array $params The parameters to bind
     * @return int The number of affected rows
     */
    public function execute(string $query, array $params = []): int
    {
        return $this->query($query, $params)->rowCount();
    }
    
    /**
     * Set a PDO instance directly
     * 
     * This is primarily used for testing or when a PDO instance is already available.
     * 
     * @param PDO $pdo The PDO instance to use
     * @return void
     */
    public function setPDO(PDO $pdo): void
    {
        $this->connection = $pdo;
    }
}