<?php
// Query feature count and range
$db = new SQLite3(__DIR__ . '/features.db');

echo "=== FEATURE DATABASE STATS ===" . PHP_EOL . PHP_EOL;

// Get total count
$result = $db->query('SELECT COUNT(*) as total FROM features');
$row = $result->fetchArray(SQLITE3_ASSOC);
echo "Total features: " . $row['total'] . PHP_EOL;

// Get passing count
$result = $db->query('SELECT COUNT(*) as total FROM features WHERE passes = 1');
$row = $result->fetchArray(SQLITE3_ASSOC);
echo "Passing: " . $row['total'] . PHP_EOL;

// Get in progress count
$result = $db->query('SELECT COUNT(*) as total FROM features WHERE in_progress = 1');
$row = $result->fetchArray(SQLITE3_ASSOC);
echo "In Progress: " . $row['total'] . PHP_EOL . PHP_EOL;

// Get ID range
$result = $db->query('SELECT MIN(id) as min_id, MAX(id) as max_id FROM features');
$row = $result->fetchArray(SQLITE3_ASSOC);
echo "ID Range: " . $row['min_id'] . " - " . $row['max_id'] . PHP_EOL . PHP_EOL;

// Show all in-progress features
echo "=== IN-PROGRESS FEATURES ===" . PHP_EOL;
$result = $db->query('SELECT id, name, category FROM features WHERE in_progress = 1 ORDER BY id');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo sprintf("#%d: %s (%s)\n", $row['id'], $row['name'], $row['category']);
}

echo PHP_EOL . "=== LAST 10 FEATURES BY ID ===" . PHP_EOL;
$result = $db->query('SELECT id, name, category, passes, in_progress FROM features ORDER BY id DESC LIMIT 10');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $status = $row['passes'] ? 'PASS' : ($row['in_progress'] ? 'IN PROG' : 'PENDING');
    echo sprintf("#%d [%s]: %s (%s)\n", $row['id'], $status, $row['name'], $row['category']);
}

$db->close();
