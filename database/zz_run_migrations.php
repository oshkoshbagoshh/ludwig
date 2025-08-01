<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

/**
 * Database Migration Runner
 * 
 * This script runs all database migrations in the correct order.
 */
class MigrationRunner
{
    /**
     * @var Database The database instance
     */
    private Database $db;
    
    /**
     * @var array The migration files to run
     */
    private array $migrations = [
        'schema.sql',
        'auth_extensions.sql',
        'permissions.sql'
    ];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = new Database();
    }
    
    /**
     * Run all migrations
     * 
     * @return void
     */
    public function run(): void
    {
        foreach ($this->migrations as $migration) {
            $this->runMigration($migration);
        }
        
        echo "All migrations completed successfully.\n";
    }
    
    /**
     * Run a single migration
     * 
     * @param string $file The migration file
     * @return void
     */
    private function runMigration(string $file): void
    {
        $path = __DIR__ . '/' . $file;
        
        if (!file_exists($path)) {
            echo "Migration file not found: {$file}\n";
            return;
        }
        
        echo "Running migration: {$file}... ";
        
        $sql = file_get_contents($path);
        $statements = $this->splitSqlStatements($sql);
        
        $this->db->beginTransaction();
        
        try {
            foreach ($statements as $statement) {
                if (trim($statement) !== '') {
                    $this->db->execute($statement);
                }
            }
            
            $this->db->commit();
            echo "Done.\n";
        } catch (Exception $e) {
            $this->db->rollBack();
            echo "Failed: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Split SQL statements by semicolons, ignoring those in comments
     * 
     * @param string $sql The SQL to split
     * @return array The SQL statements
     */
    private function splitSqlStatements(string $sql): array
    {
        // Remove comments
        $sql = preg_replace('/--(.*?)\\n/', '', $sql);
        $sql = preg_replace('/\\/\\*[\\s\\S]*?\\*\\//', '', $sql);
        
        // Split by semicolons
        $statements = preg_split('/;\\s*\\n/', $sql);
        
        return $statements;
    }
}

// Run the migrations
$runner = new MigrationRunner();
$runner->run();
