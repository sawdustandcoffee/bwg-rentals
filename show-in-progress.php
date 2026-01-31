<?php
// Show all features currently in progress
$db = new SQLite3(__DIR__ . '/features.db');

echo "=== FEATURES CURRENTLY IN PROGRESS ===" . PHP_EOL . PHP_EOL;

$result = $db->query('SELECT id, priority, category, name, description FROM features WHERE in_progress = 1 ORDER BY priority');

$count = 0;
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $count++;
    echo "Feature #{$row['id']}: {$row['name']}" . PHP_EOL;
    echo "  Category: {$row['category']}" . PHP_EOL;
    echo "  Priority: {$row['priority']}" . PHP_EOL;
    echo "  Description: {$row['description']}" . PHP_EOL;
    echo PHP_EOL;
}

if ($count === 0) {
    echo "No features currently in progress." . PHP_EOL;
} else {
    echo "Total in progress: $count" . PHP_EOL;
}

$db->close();
