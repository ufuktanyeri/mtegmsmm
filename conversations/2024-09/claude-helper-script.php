<?php
/**
 * Claude Code Helper Script
 * Context recovery and session management for Claude AI
 */

class ClaudeHelper {
    private string $projectRoot;
    private string $contextFile;
    private string $currentTaskFile;

    public function __construct() {
        $this->projectRoot = dirname(dirname(__DIR__));
        $this->contextFile = $this->projectRoot . '/PROJECT_CONTEXT.md';
        $this->currentTaskFile = $this->projectRoot . '/_dev/docs/context/CURRENT_TASK.md';
    }

    /**
     * Show current context
     */
    public function showContext(): void {
        echo "\n=== PROJECT CONTEXT ===\n";
        if (file_exists($this->contextFile)) {
            echo file_get_contents($this->contextFile);
        } else {
            echo "Context file not found!\n";
        }

        echo "\n=== CURRENT TASK ===\n";
        if (file_exists($this->currentTaskFile)) {
            echo file_get_contents($this->currentTaskFile);
        } else {
            echo "Current task file not found!\n";
        }

        echo "\n=== GIT STATUS ===\n";
        echo shell_exec('git status --short');

        echo "\n=== MIGRATION STATUS ===\n";
        $this->showMigrationStatus();
    }

    /**
     * Update current task
     */
    public function updateTask(string $task): void {
        $content = file_get_contents($this->currentTaskFile);
        $content = preg_replace('/## 🎯 Active Task\n\*\*Task:\*\* .+/m',
                               "## 🎯 Active Task\n**Task:** $task",
                               $content);
        file_put_contents($this->currentTaskFile, $content);
        echo "Task updated: $task\n";

        // Auto commit
        shell_exec('git add ' . $this->currentTaskFile);
        shell_exec('git commit -m "Update task: ' . $task . '"');
    }

    /**
     * Create context snapshot
     */
    public function createSnapshot(): void {
        $timestamp = date('Ymd_His');
        $snapshotDir = $this->projectRoot . '/_dev/docs/snapshots';

        if (!is_dir($snapshotDir)) {
            mkdir($snapshotDir, 0755, true);
        }

        $snapshotFile = "$snapshotDir/context_$timestamp.md";
        copy($this->contextFile, $snapshotFile);
        echo "Snapshot created: context_$timestamp.md\n";
    }

    /**
     * Show migration status
     */
    private function showMigrationStatus(): void {
        $modules = [
            'context_management' => ['status' => 'completed', 'icon' => '✅'],
            'mvc_structure' => ['status' => 'in_progress', 'icon' => '🔄'],
            'superadmin_system' => ['status' => 'pending', 'icon' => '⏳'],
            'user_management' => ['status' => 'pending', 'icon' => '⏳'],
            'cove_management' => ['status' => 'pending', 'icon' => '⏳'],
            'widget_system' => ['status' => 'pending', 'icon' => '⏳'],
            'security_layer' => ['status' => 'pending', 'icon' => '⏳'],
            'testing' => ['status' => 'pending', 'icon' => '⏳']
        ];

        foreach ($modules as $name => $info) {
            echo "{$info['icon']} $name: {$info['status']}\n";
        }
    }

    /**
     * Check legacy files
     */
    public function checkLegacy(): void {
        echo "\n=== LEGACY FILE CHECK ===\n";
        $gitDiff = shell_exec('git diff --name-only');

        if (strpos($gitDiff, 'app/') !== false && strpos($gitDiff, 'app_new/') === false) {
            echo "⚠️ WARNING: Legacy files modified!\n";
            $modifiedFiles = explode("\n", $gitDiff);
            foreach ($modifiedFiles as $file) {
                if (strpos($file, 'app/') === 0 && strpos($file, 'app_new/') !== 0) {
                    echo "  - $file\n";
                }
            }
            echo "\nRevert legacy changes? (y/n): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            if (trim($line) === 'y') {
                shell_exec('git checkout -- app/');
                echo "✅ Legacy changes reverted\n";
            }
            fclose($handle);
        } else {
            echo "✅ No legacy files modified\n";
        }
    }
}

// CLI Usage
if (php_sapi_name() === 'cli') {
    $helper = new ClaudeHelper();

    if ($argc < 2) {
        echo "Usage: php claude-helper.php [command]\n";
        echo "Commands:\n";
        echo "  context    - Show current context\n";
        echo "  task [msg] - Update current task\n";
        echo "  snapshot   - Create context snapshot\n";
        echo "  legacy     - Check legacy files\n";
        exit(1);
    }

    switch ($argv[1]) {
        case 'context':
            $helper->showContext();
            break;
        case 'task':
            if ($argc < 3) {
                echo "Error: Task message required\n";
                exit(1);
            }
            $helper->updateTask(implode(' ', array_slice($argv, 2)));
            break;
        case 'snapshot':
            $helper->createSnapshot();
            break;
        case 'legacy':
            $helper->checkLegacy();
            break;
        default:
            echo "Unknown command: {$argv[1]}\n";
            exit(1);
    }
}