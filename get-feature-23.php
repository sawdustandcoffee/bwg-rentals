<?php
// Quick script to get Feature #23 details
$db = new SQLite3('features.db');

$result = $db->query('SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 23');

if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "Feature #23 Details:\n";
    echo "====================\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Priority: " . $row['priority'] . "\n";
    echo "Category: " . $row['category'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Description: " . $row['description'] . "\n";
    echo "\nSteps:\n";
    $steps = json_decode($row['steps'], true);
    if ($steps) {
        foreach ($steps as $i => $step) {
            echo ($i + 1) . ". " . $step . "\n";
        }
    }
    echo "\nPasses: " . ($row['passes'] ? 'true' : 'false') . "\n";
    echo "In Progress: " . ($row['in_progress'] ? 'true' : 'false') . "\n";
    echo "Dependencies: " . $row['dependencies'] . "\n";
} else {
    echo "Feature #23 not found\n";
}
