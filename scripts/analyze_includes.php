<?php
/**
 * Include/Require Analyzer Script
 * Analyzes all PHP files in app/ directory for include/require statements
 * Outputs results in JSON format
 */

// Configuration
$baseDir = dirname(__DIR__);
$appDir = $baseDir . '/app';
$outputFile = $baseDir . '/analyze_result.json';

/**
 * Recursively get all PHP files in a directory
 * @param string $dir Directory to scan
 * @return array Array of file paths
 */
function getPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

/**
 * Extract include/require statements from a PHP file
 * @param string $filePath Path to PHP file
 * @return array Array of included files
 */
function extractIncludes($filePath) {
    $content = file_get_contents($filePath);
    $includes = [];

    // Regex patterns for different include/require formats
    $patterns = [
        // require/include with quotes
        '/(?:require|include|require_once|include_once)\s*[\(\s]*[\'"]([^\'"]+)[\'"]\s*[\)]?\s*;/i',
        // require/include with __DIR__ or dirname
        '/(?:require|include|require_once|include_once)\s*[\(\s]*(?:__DIR__|dirname\([^)]+\))\s*\.\s*[\'"]([^\'"]+)[\'"]\s*[\)]?\s*;/i',
        // require/include with variable concatenation
        '/(?:require|include|require_once|include_once)\s*[\(\s]*\$\w+\s*\.\s*[\'"]([^\'"]+)[\'"]\s*[\)]?\s*;/i',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $match) {
                // Clean up the path
                $cleanPath = trim($match);

                // Skip if it's a variable or complex expression
                if (strpos($cleanPath, '$') !== false) {
                    continue;
                }

                // Normalize the path
                $cleanPath = str_replace(['\\\\', '\\'], '/', $cleanPath);

                // Remove leading slashes
                $cleanPath = ltrim($cleanPath, '/');

                if (!in_array($cleanPath, $includes)) {
                    $includes[] = $cleanPath;
                }
            }
        }
    }

    // Also check for simple patterns without concatenation
    $simplePattern = '/(?:require|include|require_once|include_once)\s+[\'"]([^\'"]+)[\'"]/i';
    if (preg_match_all($simplePattern, $content, $matches)) {
        foreach ($matches[1] as $match) {
            $cleanPath = trim($match);
            $cleanPath = str_replace(['\\\\', '\\'], '/', $cleanPath);
            $cleanPath = ltrim($cleanPath, '/');

            if (!in_array($cleanPath, $includes) && strpos($cleanPath, '$') === false) {
                $includes[] = $cleanPath;
            }
        }
    }

    return array_unique($includes);
}

/**
 * Main analysis function
 */
function analyzeIncludes($appDir, $baseDir) {
    $results = [];
    $phpFiles = getPhpFiles($appDir);

    echo "Found " . count($phpFiles) . " PHP files to analyze\n";
    echo "Starting analysis...\n\n";

    foreach ($phpFiles as $file) {
        // Get relative path from base directory
        $relativePath = str_replace('\\', '/', substr($file, strlen($baseDir) + 1));

        // Extract includes
        $includes = extractIncludes($file);

        // Add to results
        $results[] = [
            'file' => $relativePath,
            'includes' => $includes,
            'include_count' => count($includes)
        ];

        // Display progress
        echo "Analyzed: $relativePath";
        if (count($includes) > 0) {
            echo " (" . count($includes) . " includes found)";
        }
        echo "\n";
    }

    return $results;
}

/**
 * Generate statistics from results
 */
function generateStats($results) {
    $totalFiles = count($results);
    $totalIncludes = 0;
    $filesWithIncludes = 0;
    $maxIncludes = 0;
    $maxIncludesFile = '';

    foreach ($results as $result) {
        $includeCount = $result['include_count'];
        $totalIncludes += $includeCount;

        if ($includeCount > 0) {
            $filesWithIncludes++;
        }

        if ($includeCount > $maxIncludes) {
            $maxIncludes = $includeCount;
            $maxIncludesFile = $result['file'];
        }
    }

    return [
        'total_files' => $totalFiles,
        'files_with_includes' => $filesWithIncludes,
        'files_without_includes' => $totalFiles - $filesWithIncludes,
        'total_includes' => $totalIncludes,
        'average_includes' => $filesWithIncludes > 0 ? round($totalIncludes / $filesWithIncludes, 2) : 0,
        'max_includes' => $maxIncludes,
        'max_includes_file' => $maxIncludesFile
    ];
}

// Run the analysis
echo "=================================\n";
echo "PHP Include/Require Analyzer\n";
echo "=================================\n\n";
echo "Base Directory: $baseDir\n";
echo "App Directory: $appDir\n";
echo "Output File: $outputFile\n\n";

if (!is_dir($appDir)) {
    die("Error: App directory not found: $appDir\n");
}

// Perform analysis
$results = analyzeIncludes($appDir, $baseDir);

// Generate statistics
$stats = generateStats($results);

// Create output structure
$output = [
    'metadata' => [
        'timestamp' => date('Y-m-d H:i:s'),
        'base_directory' => $baseDir,
        'app_directory' => $appDir,
        'php_version' => phpversion()
    ],
    'statistics' => $stats,
    'files' => $results
];

// Save to JSON file
$jsonOutput = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if (file_put_contents($outputFile, $jsonOutput)) {
    echo "\n=================================\n";
    echo "Analysis Complete!\n";
    echo "=================================\n\n";
    echo "Statistics:\n";
    echo "- Total PHP files: " . $stats['total_files'] . "\n";
    echo "- Files with includes: " . $stats['files_with_includes'] . "\n";
    echo "- Files without includes: " . $stats['files_without_includes'] . "\n";
    echo "- Total include statements: " . $stats['total_includes'] . "\n";
    echo "- Average includes per file: " . $stats['average_includes'] . "\n";
    echo "- Maximum includes in a file: " . $stats['max_includes'] . " ($maxIncludesFile)\n";
    echo "\nResults saved to: $outputFile\n";
} else {
    die("\nError: Failed to save results to $outputFile\n");
}

// Display files with most includes (top 5)
echo "\nTop 5 files with most includes:\n";
usort($results, function($a, $b) {
    return $b['include_count'] - $a['include_count'];
});

$topFiles = array_slice($results, 0, 5);
foreach ($topFiles as $i => $file) {
    if ($file['include_count'] > 0) {
        echo ($i + 1) . ". " . $file['file'] . " (" . $file['include_count'] . " includes)\n";
        foreach ($file['includes'] as $include) {
            echo "   - $include\n";
        }
    }
}

echo "\n✅ Analysis completed successfully!\n";