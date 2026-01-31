<?php
$db = new SQLite3('features.db');
$result = $db->query('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 47');
$row = $result->fetchArray(SQLITE3_ASSOC);

echo "Feature #47:\n";
echo "===========\n";
echo "ID: " . $row['id'] . "\n";
echo "Category: " . $row['category'] . "\n";
echo "Name: " . $row['name'] . "\n";
echo "Description: " . $row['description'] . "\n";
echo "Passes: " . ($row['passes'] ? 'true' : 'false') . "\n";
echo "In Progress: " . ($row['in_progress'] ? 'true' : 'false') . "\n";
if($row['dependencies']) {
    echo "Dependencies: " . $row['dependencies'] . "\n";
}
echo "\nSteps:\n";
echo $row['steps'] . "\n";

$db->close();
