<?php
// Simple script to get Feature #85 details
$db = new SQLite3('features.db');
$stmt = $db->prepare('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = :id');
$stmt->bindValue(':id', 85, SQLITE3_INTEGER);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row) {
    echo "ID: " . $row['id'] . "\n";
    echo "Category: " . $row['category'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Description: " . $row['description'] . "\n";
    echo "\nSteps:\n" . $row['steps'] . "\n";
    echo "\nPasses: " . ($row['passes'] ? 'true' : 'false') . "\n";
    echo "In Progress: " . ($row['in_progress'] ? 'true' : 'false') . "\n";
    echo "Dependencies: " . ($row['dependencies'] ?? 'null') . "\n";
} else {
    echo "Feature #85 not found\n";
}

$db->close();
