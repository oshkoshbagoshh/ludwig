<?php

namespace App\Core;

use PDO;
use PDOException;
use Exception;

/**
 * Migration class for database schema management
 * 
 * This class provides functionality to manage database migrations,
 * including tracking which migrations have been applied.
 */
class Migration
{
    /**
     * @var Database The database instance
     */
    private Database $db;
    
    /**
     * @var string The directory where migration files are stored
     */
    private string $migrationsDir;
    
    /**
     * Constructor
     * 
     * @param Database|null $db Optional database instance
     * @param string|null $migrationsDir Optional migrations directory path
     */
    public function __construct(?Database $db = null, ?string $migrationsDir = null)
    {
        $this->db = $db ?? new Database();
        $this->migrationsDir = $migrationsDir ?? __DIR__ . '/../../database/migrations';
        
        // Ensure migrations directory exists
        if (!is_dir($this->migrationsDir) && !mkdir($this->migrationsDir, 0755, true) && !is_dir($this->migrationsDir)) {
            throw new Exception("Failed to create migrations directory: {$this->migrationsDir}");
        }
        
        // Ensure migrations table exists
        $this->createMigrationsTable();
    }
    
    /**
     * Create the migrations table if it doesn't exist
     * 
     * @return void
     */
    private function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                batch INTEGER NOT NULL,
                executed_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
            )
        ";
        
        try {
            $this->db->execute($sql);
        } catch (PDOException $e) {
            throw new Exception("Failed to create migrations table: " . $e->getMessage());
        }
    }
    
    /**
     * Get all applied migrations
     * 
     * @return array List of applied migrations
     */
    public function getAppliedMigrations(): array
    {
        try {
            return $this->db->fetchAll("SELECT * FROM migrations ORDER BY id");
        } catch (PDOException $e) {
            throw new Exception("Failed to get applied migrations: " . $e->getMessage());
        }
    }
    
    /**
     * Get the latest batch number
     * 
     * @return int The latest batch number
     */
    private function getLatestBatch(): int
    {
        try {
            $result = $this->db->fetch("SELECT MAX(batch) as max_batch FROM migrations");
            return (int)($result['max_batch'] ?? 0);
        } catch (PDOException $e) {
            throw new Exception("Failed to get latest batch: " . $e->getMessage());
        }
    }
    
    /**
     * Get all available migrations
     * 
     * @return array List of available migration files
     */
    public function getAvailableMigrations(): array
    {
        $files = scandir($this->migrationsDir);
        $migrations = [];
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            // Only include .php and .sql files
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php' || pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $migrations[] = $file;
            }
        }
        
        sort($migrations);
        return $migrations;
    }
    
    /**
     * Get pending migrations
     * 
     * @return array List of pending migrations
     */
    public function getPendingMigrations(): array
    {
        $available = $this->getAvailableMigrations();
        $applied = array_column($this->getAppliedMigrations(), 'migration');
        
        return array_diff($available, $applied);
    }
    
    /**
     * Run all pending migrations
     * 
     * @return array Results of the migration operation
     */
    public function migrate(): array
    {
        $pending = $this->getPendingMigrations();
        $batch = $this->getLatestBatch() + 1;
        $results = [];
        
        if (empty($pending)) {
            return ['message' => 'No pending migrations.'];
        }
        
        foreach ($pending as $migration) {
            $result = $this->runMigration($migration, $batch);
            $results[] = $result;
        }
        
        return [
            'message' => count($results) . ' migration(s) completed.',
            'migrations' => $results
        ];
    }
    
    /**
     * Run a single migration
     * 
     * @param string $migration The migration file name
     * @param int $batch The batch number
     * @return array Result of the migration
     */
    private function runMigration(string $migration, int $batch): array
    {
        $path = $this->migrationsDir . '/' . $migration;
        $extension = pathinfo($migration, PATHINFO_EXTENSION);
        
        try {
            $this->db->beginTransaction();
            
            if ($extension === 'php') {
                // Execute PHP migration
                require_once $path;
                $className = pathinfo($migration, PATHINFO_FILENAME);
                $migrationClass = new $className($this->db);
                $migrationClass->up();
            } else {
                // Execute SQL migration
                $sql = file_get_contents($path);
                $statements = $this->splitSqlStatements($sql);
                
                foreach ($statements as $statement) {
                    if (trim($statement) !== '') {
                        $this->db->execute($statement);
                    }
                }
            }
            
            // Record the migration
            $this->db->execute(
                "INSERT INTO migrations (migration, batch) VALUES (?, ?)",
                [$migration, $batch]
            );
            
            $this->db->commit();
            
            return [
                'migration' => $migration,
                'status' => 'success'
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            
            return [
                'migration' => $migration,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Rollback the last batch of migrations
     * 
     * @return array Results of the rollback operation
     */
    public function rollback(): array
    {
        $latestBatch = $this->getLatestBatch();
        
        if ($latestBatch === 0) {
            return ['message' => 'Nothing to rollback.'];
        }
        
        $migrations = $this->db->fetchAll(
            "SELECT * FROM migrations WHERE batch = ? ORDER BY id DESC",
            [$latestBatch]
        );
        
        $results = [];
        
        foreach ($migrations as $migration) {
            $result = $this->rollbackMigration($migration['migration']);
            $results[] = $result;
        }
        
        return [
            'message' => count($results) . ' migration(s) rolled back.',
            'migrations' => $results
        ];
    }
    
    /**
     * Rollback a single migration
     * 
     * @param string $migration The migration file name
     * @return array Result of the rollback
     */
    private function rollbackMigration(string $migration): array
    {
        $path = $this->migrationsDir . '/' . $migration;
        $extension = pathinfo($migration, PATHINFO_EXTENSION);
        
        try {
            $this->db->beginTransaction();
            
            if ($extension === 'php') {
                // Execute PHP migration rollback
                require_once $path;
                $className = pathinfo($migration, PATHINFO_FILENAME);
                $migrationClass = new $className($this->db);
                $migrationClass->down();
            } else {
                // For SQL migrations, we don't have automatic rollback
                // This would require a down.sql file or similar convention
                throw new Exception("SQL migrations don't support automatic rollback.");
            }
            
            // Remove the migration record
            $this->db->execute(
                "DELETE FROM migrations WHERE migration = ?",
                [$migration]
            );
            
            $this->db->commit();
            
            return [
                'migration' => $migration,
                'status' => 'success'
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            
            return [
                'migration' => $migration,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Reset all migrations
     * 
     * @return array Results of the reset operation
     */
    public function reset(): array
    {
        $migrations = $this->db->fetchAll("SELECT * FROM migrations ORDER BY id DESC");
        $results = [];
        
        foreach ($migrations as $migration) {
            $result = $this->rollbackMigration($migration['migration']);
            $results[] = $result;
        }
        
        return [
            'message' => count($results) . ' migration(s) reset.',
            'migrations' => $results
        ];
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