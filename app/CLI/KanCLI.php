<?php

namespace App\CLI;

use App\Core\Logger;

/**
 * KanCLI
 * 
 * Command Line Interface for the application
 */
class KanCLI
{
    /**
     * @var array
     */
    private array $commands = [];
    
    /**
     * @var Logger
     */
    private Logger $logger;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = Logger::getInstance();
        
        // Register commands
        $this->registerCommands();
    }
    
    /**
     * Register commands
     * 
     * @return void
     */
    private function registerCommands(): void
    {
        // Register built-in commands
        $this->commands['help'] = new Commands\HelpCommand();
        $this->commands['create'] = new Commands\CreateCommand();
        $this->commands['git'] = new Commands\GitCommand();
        $this->commands['backup'] = new Commands\BackupCommand();
        $this->commands['archive'] = new Commands\ArchiveCommand();
    }
    
    /**
     * Run the CLI application
     * 
     * @param array $args Command line arguments
     * @return void
     */
    public function run(array $args): void
    {
        // Remove the script name from the arguments
        array_shift($args);
        
        // If no command is provided, show help
        if (empty($args)) {
            $this->showHelp();
            return;
        }
        
        // Get the command name
        $commandName = array_shift($args);
        
        // Check if the command exists
        if (!isset($this->commands[$commandName])) {
            $this->error("Command '$commandName' not found.");
            $this->showHelp();
            return;
        }
        
        // Run the command
        try {
            $this->commands[$commandName]->execute($args);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $this->logger->error('CLI Error: ' . $e->getMessage(), [
                'command' => $commandName,
                'args' => $args,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Show help
     * 
     * @return void
     */
    private function showHelp(): void
    {
        $this->info('Usage: hey <command> [options]');
        $this->info('');
        $this->info('Available commands:');
        
        foreach ($this->commands as $name => $command) {
            $this->info(sprintf('  %-15s %s', $name, $command->getDescription()));
        }
    }
    
    /**
     * Output an info message
     * 
     * @param string $message Message to output
     * @return void
     */
    public function info(string $message): void
    {
        echo $message . PHP_EOL;
    }
    
    /**
     * Output an error message
     * 
     * @param string $message Error message
     * @return void
     */
    public function error(string $message): void
    {
        echo 'Error: ' . $message . PHP_EOL;
    }
    
    /**
     * Output a success message
     * 
     * @param string $message Success message
     * @return void
     */
    public function success(string $message): void
    {
        echo 'Success: ' . $message . PHP_EOL;
    }
}