<?php
/**
 * Bootstrap 5.3.6 Class Migration Script
 * Updates deprecated Bootstrap classes to their modern equivalents
 */

$startTime = microtime(true);
$updatedFiles = [];
$skippedFiles = [];
$totalReplacements = 0;

// Define replacement mappings
$replacements = [
    // Text utilities
    '/\btext-muted\b/' => 'text-body-secondary',

    // Margin utilities (left/right to start/end)
    '/\bml-0\b/' => 'ms-0',
    '/\bml-1\b/' => 'ms-1',
    '/\bml-2\b/' => 'ms-2',
    '/\bml-3\b/' => 'ms-3',
    '/\bml-4\b/' => 'ms-4',
    '/\bml-5\b/' => 'ms-5',
    '/\bml-auto\b/' => 'ms-auto',

    '/\bmr-0\b/' => 'me-0',
    '/\bmr-1\b/' => 'me-1',
    '/\bmr-2\b/' => 'me-2',
    '/\bmr-3\b/' => 'me-3',
    '/\bmr-4\b/' => 'me-4',
    '/\bmr-5\b/' => 'me-5',
    '/\bmr-auto\b/' => 'me-auto',

    // Padding utilities (left/right to start/end)
    '/\bpl-0\b/' => 'ps-0',
    '/\bpl-1\b/' => 'ps-1',
    '/\bpl-2\b/' => 'ps-2',
    '/\bpl-3\b/' => 'ps-3',
    '/\bpl-4\b/' => 'ps-4',
    '/\bpl-5\b/' => 'ps-5',

    '/\bpr-0\b/' => 'pe-0',
    '/\bpr-1\b/' => 'pe-1',
    '/\bpr-2\b/' => 'pe-2',
    '/\bpr-3\b/' => 'pe-3',
    '/\bpr-4\b/' => 'pe-4',
    '/\bpr-5\b/' => 'pe-5',

    // Float utilities
    '/\bfloat-left\b/' => 'float-start',
    '/\bfloat-right\b/' => 'float-end',

    // Text alignment
    '/\btext-left\b/' => 'text-start',
    '/\btext-right\b/' => 'text-end',
];

// Directories to scan
$directories = [
    __DIR__ . '/app/views'
];

// File extensions to process
$extensions = ['php', 'html', 'htm'];

// Files to skip
$skipFiles = [
    'update_bootstrap_classes.php',
    'BOOTSTRAP_UPDATE_SUMMARY.md'
];

echo "Bootstrap 5.3.6 Class Migration Script\n";
echo "=====================================\n\n";

/**
 * Recursively scan directory for files
 */
function scanDirectory($dir, $extensions) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                $files[] = $file->getPathname();
            }
        }
    }

    return $files;
}

/**
 * Update deprecated classes in a file
 */
function updateFile($filePath, $replacements) {
    global $totalReplacements;

    $content = file_get_contents($filePath);
    $originalContent = $content;
    $localReplacements = 0;

    foreach ($replacements as $pattern => $replacement) {
        $count = 0;
        $content = preg_replace($pattern, $replacement, $content, -1, $count);
        $localReplacements += $count;
        $totalReplacements += $count;
    }

    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        return $localReplacements;
    }

    return 0;
}

// Process all directories
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "Directory not found: $dir\n";
        continue;
    }

    echo "Scanning directory: $dir\n";
    $files = scanDirectory($dir, $extensions);

    foreach ($files as $file) {
        $filename = basename($file);

        // Skip specified files
        if (in_array($filename, $skipFiles)) {
            $skippedFiles[] = $file;
            continue;
        }

        // Check if file contains any deprecated classes
        $content = file_get_contents($file);
        $hasDeprecated = false;

        foreach (array_keys($replacements) as $pattern) {
            if (preg_match($pattern, $content)) {
                $hasDeprecated = true;
                break;
            }
        }

        if ($hasDeprecated) {
            $replacementCount = updateFile($file, $replacements);
            if ($replacementCount > 0) {
                $relativePath = str_replace(__DIR__ . '/', '', $file);
                $updatedFiles[] = [
                    'path' => $relativePath,
                    'replacements' => $replacementCount
                ];
                echo "  ✓ Updated: $relativePath ($replacementCount replacements)\n";
            }
        }
    }
}

// Generate summary report
echo "\n";
echo "Migration Summary\n";
echo "=================\n";
echo "Total files scanned: " . ($updatedFiles ? count($updatedFiles) + count($skippedFiles) : 0) . "\n";
echo "Files updated: " . count($updatedFiles) . "\n";
echo "Total replacements: $totalReplacements\n";
echo "Execution time: " . round(microtime(true) - $startTime, 2) . " seconds\n";

if ($updatedFiles) {
    echo "\nUpdated Files:\n";
    echo "--------------\n";
    foreach ($updatedFiles as $file) {
        echo "• {$file['path']} ({$file['replacements']} changes)\n";
    }
}

if ($skippedFiles) {
    echo "\nSkipped Files:\n";
    echo "--------------\n";
    foreach ($skippedFiles as $file) {
        $relativePath = str_replace(__DIR__ . '/', '', $file);
        echo "• $relativePath\n";
    }
}

// Save detailed report
$reportContent = "Bootstrap 5.3.6 Migration Report\n";
$reportContent .= "Generated: " . date('Y-m-d H:i:s') . "\n";
$reportContent .= "=====================================\n\n";

$reportContent .= "Replacement Mappings Applied:\n";
foreach ($replacements as $old => $new) {
    $oldClean = str_replace(['/', '\\b'], '', $old);
    $reportContent .= "• $oldClean → $new\n";
}

$reportContent .= "\nFiles Updated:\n";
foreach ($updatedFiles as $file) {
    $reportContent .= "• {$file['path']} ({$file['replacements']} replacements)\n";
}

$reportContent .= "\nSummary:\n";
$reportContent .= "• Total files updated: " . count($updatedFiles) . "\n";
$reportContent .= "• Total replacements: $totalReplacements\n";
$reportContent .= "• Execution time: " . round(microtime(true) - $startTime, 2) . " seconds\n";

file_put_contents(__DIR__ . '/bootstrap_migration_report.txt', $reportContent);

echo "\n✅ Migration complete! Report saved to bootstrap_migration_report.txt\n";