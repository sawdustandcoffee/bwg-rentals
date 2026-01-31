<?php
// Query Feature #101 from the local database (including WAL changes)
$db = new SQLite3(__DIR__ . '/features.db');

// Get feature #101
$stmt = $db->prepare('SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 101');
$result = $stmt->execute();

if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "=== FEATURE #101 ===" . PHP_EOL;
    echo "ID: " . $row['id'] . PHP_EOL;
    echo "Priority: " . $row['priority'] . PHP_EOL;
    echo "Category: " . $row['category'] . PHP_EOL;
    echo "Name: " . $row['name'] . PHP_EOL;
    echo "Description: " . $row['description'] . PHP_EOL;
    echo "Passes: " . ($row['passes'] ? 'true' : 'false') . PHP_EOL;
    echo "In Progress: " . ($row['in_progress'] ? 'true' : 'false') . PHP_EOL;
    echo "Dependencies: " . ($row['dependencies'] ?: 'none') . PHP_EOL;
    echo PHP_EOL;
    echo "Steps:" . PHP_EOL;
    $steps = json_decode($row['steps'], true);
    if (is_array($steps)) {
        foreach ($steps as $i => $step) {
            echo "  " . ($i + 1) . ". " . $step . PHP_EOL;
        }
    } else {
        echo $row['steps'] . PHP_EOL;
    }
} else {
    echo "Feature #101 not found" . PHP_EOL;

    // Show what features DO exist
    echo PHP_EOL . "Available features:" . PHP_EOL;
    $result2 = $db->query('SELECT id, name, in_progress FROM features WHERE id >= 98 AND id <= 105 ORDER BY id');
    while ($row2 = $result2->fetchArray(SQLITE3_ASSOC)) {
        $status = $row2['in_progress'] ? ' [IN PROGRESS]' : '';
        echo "  #" . $row2['id'] . ": " . $row2['name'] . $status . PHP_EOL;
    }
}

$db->close();
