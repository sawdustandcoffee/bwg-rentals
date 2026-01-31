<?php
// Quick script to get Feature #101 details
$db = new SQLite3('/tmp/features.db');

$result = $db->query('SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 101');

if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "Feature #101 Details:\n";
    echo "====================\n";
    echo "ID: " . $row['id'] . "\n";
    echo "Priority: " . $row['priority'] . "\n";
    echo "Category: " . $row['category'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Description: " . $row['description'] . "\n";
    echo "Steps: " . $row['steps'] . "\n";
    echo "Passes: " . ($row['passes'] ? 'true' : 'false') . "\n";
    echo "In Progress: " . ($row['in_progress'] ? 'true' : 'false') . "\n";
    echo "Dependencies: " . $row['dependencies'] . "\n";
} else {
    echo "Feature #101 not found\n";
}

$db->close();
