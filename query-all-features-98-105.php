<?php
// Query all features from 98 to 105
$db = new SQLite3(__DIR__ . '/features.db');

echo "=== FEATURES 98-105 ===" . PHP_EOL . PHP_EOL;

$result = $db->query('SELECT id, priority, category, name, passes, in_progress FROM features WHERE id >= 98 AND id <= 105 ORDER BY id');

$found = false;
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $found = true;
    $status = '';
    if ($row['passes']) {
        $status = '[PASSING]';
    } elseif ($row['in_progress']) {
        $status = '[IN PROGRESS]';
    } else {
        $status = '[PENDING]';
    }

    echo sprintf("#%d (Priority %d) %s - %s - %s\n",
        $row['id'],
        $row['priority'],
        $status,
        $row['category'],
        $row['name']
    );
}

if (!$found) {
    echo "No features found in range 98-105\n";
}

$db->close();
