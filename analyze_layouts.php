<?php
// Analyze layout-component relationships
$json = json_decode(file_get_contents("analyze_result.json"), true);

$layouts = [];

foreach ($json["files"] as $file) {
    if (strpos($file["file"], "app/views/layouts/") !== false) {
        $layoutName = str_replace("app/views/layouts/", "", $file["file"]);
        $layoutName = str_replace(".php", "", $layoutName);

        $components = [];
        foreach ($file["includes"] as $inc) {
            // Clean up component names
            $component = $inc;
            $component = str_replace("../components/", "", $component);
            $component = str_replace("../../../includes/", "includes/", $component);
            $component = str_replace("helpers/", "", $component);
            $component = str_replace(".php", "", $component);

            // Skip unified.php references (layout inheritance)
            if ($component !== "unified") {
                $components[] = $component;
            }
        }

        $layouts[$layoutName] = $components;
    }
}

// Print markdown table
echo "| Layout File | Components Used | Count |\n";
echo "|-------------|-----------------|-------|\n";

foreach ($layouts as $layout => $components) {
    $componentList = empty($components) ? "*(none)*" : implode(", ", $components);
    $count = count($components);
    echo "| **$layout** | $componentList | $count |\n";
}

// Check for layout inheritance
echo "\n## Layout Inheritance\n\n";
echo "| Layout | Inherits From |\n";
echo "|--------|---------------|\n";

foreach ($json["files"] as $file) {
    if (strpos($file["file"], "app/views/layouts/") !== false) {
        $layoutName = basename($file["file"], ".php");
        foreach ($file["includes"] as $inc) {
            if (strpos($inc, "unified.php") !== false) {
                echo "| **$layoutName** | unified |\n";
                break;
            }
        }
    }
}

echo "\n## Component Usage Details\n\n";

// Count component usage
$componentUsage = [];
foreach ($layouts as $layout => $components) {
    foreach ($components as $component) {
        if (!isset($componentUsage[$component])) {
            $componentUsage[$component] = [];
        }
        $componentUsage[$component][] = $layout;
    }
}

// Sort by usage count
arsort($componentUsage);

echo "| Component | Used In Layouts | Usage Count |\n";
echo "|-----------|-----------------|-------------|\n";

foreach ($componentUsage as $component => $usedInLayouts) {
    $layoutList = implode(", ", $usedInLayouts);
    $count = count($usedInLayouts);
    echo "| **$component** | $layoutList | $count |\n";
}

// Summary statistics
echo "\n## Summary\n\n";
$totalLayouts = count($layouts);
$layoutsWithComponents = 0;
$totalComponents = 0;
foreach ($layouts as $layout => $components) {
    if (count($components) > 0) {
        $layoutsWithComponents++;
    }
    $totalComponents += count($components);
}
echo "- **Total Layouts:** $totalLayouts\n";
echo "- **Layouts with Components:** $layoutsWithComponents\n";
echo "- **Unique Components:** " . count($componentUsage) . "\n";
echo "- **Total Component Inclusions:** $totalComponents\n";
echo "- **Average Components per Layout:** " . round($totalComponents / $totalLayouts, 2) . "\n";