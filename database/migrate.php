<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Migration;

/**
 * Database Migration Command Line Tool
 * 
 * This script provides a command-line interface for running database migrations.
 * 
 * Usage:
 *   php database/migrate.php [command] [options]
 * 
 * Commands:
 *   migrate     Run all pending migrations
 *   rollback    Rollback the last batch of migrations
 *   reset       Reset all migrations
 *   status      Show migration status
 *   help        Show this help message
 * 
 * Options:
 *   --force     Force the operation without confirmation
 */

// Parse command line arguments
$command = $argv[1] ?? 'help';
$options = array_slice($argv, 2);
$force = in_array('--force', $options);

// Create migration instance
$migration = new Migration();

// Execute command
switch ($command) {
    case 'migrate':
        migrateCommand($migration, $force);
        break;
    case 'rollback':
        rollbackCommand($migration, $force);
        break;
    case 'reset':
        resetCommand($migration, $force);
        break;
    case 'status':
        statusCommand($migration);
        break;
    case 'help':
    default:
        showHelp();
        break;
}

/**
 * Run the migrate command
 * 
 * @param Migration $migration The migration instance
 * @param bool $force Whether to force the operation
 * @return void
 */
function migrateCommand(Migration $migration, bool $force): void
{
    $pending = $migration->getPendingMigrations();
    
    if (empty($pending)) {
        echo "No pending migrations.\n";
        return;
    }
    
    echo "The following migrations will be applied:\n";
    foreach ($pending as $file) {
        echo "  - {$file}\n";
    }
    
    if (!$force && !confirm("Do you want to continue?")) {
        echo "Operation cancelled.\n";
        return;
    }
    
    $result = $migration->migrate();
    echo $result['message'] . "\n";
    
    if (isset($result['migrations'])) {
        foreach ($result['migrations'] as $migrationResult) {
            $status = $migrationResult['status'] === 'success' ? 'SUCCESS' : 'ERROR';
            echo "  - {$migrationResult['migration']}: {$status}";
            
            if (isset($migrationResult['message'])) {
                echo " ({$migrationResult['message']})";
            }
            
            echo "\n";
        }
    }
}

/**
 * Run the rollback command
 * 
 * @param Migration $migration The migration instance
 * @param bool $force Whether to force the operation
 * @return void
 */
function rollbackCommand(Migration $migration, bool $force): void
{
    if (!$force && !confirm("Are you sure you want to rollback the last batch of migrations?")) {
        echo "Operation cancelled.\n";
        return;
    }
    
    $result = $migration->rollback();
    echo $result['message'] . "\n";
    
    if (isset($result['migrations'])) {
        foreach ($result['migrations'] as $migrationResult) {
            $status = $migrationResult['status'] === 'success' ? 'SUCCESS' : 'ERROR';
            echo "  - {$migrationResult['migration']}: {$status}";
            
            if (isset($migrationResult['message'])) {
                echo " ({$migrationResult['message']})";
            }
            
            echo "\n";
        }
    }
}

/**
 * Run the reset command
 * 
 * @param Migration $migration The migration instance
 * @param bool $force Whether to force the operation
 * @return void
 */
function resetCommand(Migration $migration, bool $force): void
{
    if (!$force && !confirm("Are you sure you want to reset all migrations? This will delete all data!")) {
        echo "Operation cancelled.\n";
        return;
    }
    
    $result = $migration->reset();
    echo $result['message'] . "\n";
    
    if (isset($result['migrations'])) {
        foreach ($result['migrations'] as $migrationResult) {
            $status = $migrationResult['status'] === 'success' ? 'SUCCESS' : 'ERROR';
            echo "  - {$migrationResult['migration']}: {$status}";
            
            if (isset($migrationResult['message'])) {
                echo " ({$migrationResult['message']})";
            }
            
            echo "\n";
        }
    }
}

/**
 * Run the status command
 * 
 * @param Migration $migration The migration instance
 * @return void
 */
function statusCommand(Migration $migration): void
{
    $applied = $migration->getAppliedMigrations();
    $pending = $migration->getPendingMigrations();
    
    echo "Applied migrations:\n";
    if (empty($applied)) {
        echo "  No migrations have been applied.\n";
    } else {
        foreach ($applied as $migrationItem) {
            echo "  - {$migrationItem['migration']} (Batch: {$migrationItem['batch']}, Applied: {$migrationItem['executed_at']})\n";
        }
    }
    
    echo "\nPending migrations:\n";
    if (empty($pending)) {
        echo "  No pending migrations.\n";
    } else {
        foreach ($pending as $pendingMigration) {
            echo "  - {$pendingMigration}\n";
        }
    }
}

/**
 * Show help message
 * 
 * @return void
 */
function showHelp(): void
{
    echo <<<HELP
Database Migration Command Line Tool

Usage:
  php database/migrate.php [command] [options]

Commands:
  migrate     Run all pending migrations
  rollback    Rollback the last batch of migrations
  reset       Reset all migrations
  status      Show migration status
  help        Show this help message

Options:
  --force     Force the operation without confirmation

HELP;
}

/**
 * Confirm an operation with the user
 * 
 * @param string $message The confirmation message
 * @return bool Whether the user confirmed
 */
function confirm(string $message): bool
{
    echo $message . " [y/N] ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);
    return strtolower($line) === 'y';
}