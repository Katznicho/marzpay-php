<?php

/**
 * Test runner script for MarzPay PHP SDK
 * Provides multiple testing options and configurations
 */

require_once __DIR__ . '/vendor/autoload.php';

class TestRunner
{
    private array $options;
    
    public function __construct()
    {
        $this->options = [
            'unit' => 'Run unit tests only',
            'integration' => 'Run integration tests only (requires real API credentials)',
            'feature' => 'Run feature tests only (requires real API credentials)',
            'all' => 'Run all tests',
            'coverage' => 'Run tests with code coverage report',
            'quick' => 'Run quick validation test',
            'help' => 'Show this help message'
        ];
    }
    
    public function run(array $argv): void
    {
        $command = $argv[1] ?? 'help';
        
        echo "MarzPay PHP SDK - Test Runner\n";
        echo str_repeat('=', 50) . "\n\n";
        
        switch ($command) {
            case 'unit':
                $this->runUnitTests();
                break;
            case 'integration':
                $this->runIntegrationTests();
                break;
            case 'feature':
                $this->runFeatureTests();
                break;
            case 'all':
                $this->runAllTests();
                break;
            case 'coverage':
                $this->runTestsWithCoverage();
                break;
            case 'quick':
                $this->runQuickTest();
                break;
            case 'help':
            default:
                $this->showHelp();
                break;
        }
    }
    
    private function runUnitTests(): void
    {
        echo "Running unit tests...\n";
        $this->executeCommand('vendor/bin/phpunit tests/Unit --colors=always');
    }
    
    private function runIntegrationTests(): void
    {
        echo "Running integration tests...\n";
        echo "Note: Integration tests require real API credentials\n";
        $this->executeCommand('vendor/bin/phpunit tests/Integration --colors=always');
    }
    
    private function runFeatureTests(): void
    {
        echo "Running feature tests...\n";
        echo "Note: Feature tests require real API credentials\n";
        $this->executeCommand('vendor/bin/phpunit tests/Feature --colors=always');
    }
    
    private function runAllTests(): void
    {
        echo "Running all tests...\n";
        $this->executeCommand('vendor/bin/phpunit --colors=always');
    }
    
    private function runTestsWithCoverage(): void
    {
        echo "Running tests with code coverage...\n";
        $this->executeCommand('vendor/bin/phpunit --coverage-html coverage --colors=always');
        echo "\nCoverage report generated in 'coverage' directory\n";
    }
    
    private function runQuickTest(): void
    {
        echo "Running quick validation test...\n";
        $this->executeCommand('vendor/bin/phpunit tests/Unit --colors=always');
    }
    
    private function executeCommand(string $command): void
    {
        echo "Executing: $command\n";
        echo str_repeat('-', 50) . "\n";
        
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        echo implode("\n", $output) . "\n";
        echo str_repeat('-', 50) . "\n";
        
        if ($returnCode === 0) {
            echo "✅ Command executed successfully!\n";
        } else {
            echo "❌ Command failed with exit code: $returnCode\n";
        }
        echo "\n";
    }
    
    private function showHelp(): void
    {
        echo "Usage: php run-tests.php <command>\n\n";
        echo "Available commands:\n";
        
        foreach ($this->options as $command => $description) {
            echo sprintf("  %-12s %s\n", $command, $description);
        }
        
        echo "\nExamples:\n";
        echo "  php run-tests.php unit          # Run only unit tests\n";
        echo "  php run-tests.php integration   # Run integration tests (needs real API credentials)\n";
        echo "  php run-tests.php coverage      # Run all tests with coverage report\n";
        echo "  php run-tests.php quick         # Run quick validation test\n";
        
        echo "\nEnvironment Setup:\n";
        echo "1. Copy env.testing to .env\n";
        echo "2. Add your real API credentials to .env for integration/feature tests\n";
        echo "3. Run: composer install\n";
        echo "4. Run tests with: php run-tests.php <command>\n";
        
        echo "\nTest Types:\n";
        echo "- Unit Tests: Test individual components without external dependencies\n";
        echo "- Integration Tests: Test API interactions (requires real credentials)\n";
        echo "- Feature Tests: Test complete workflows (requires real credentials)\n";
        echo "- Quick Test: Basic SDK validation without external API calls\n";
    }
}

// Run the test runner
$runner = new TestRunner();
$runner->run($argv);
