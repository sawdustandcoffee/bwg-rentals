<?php
$db = new SQLite3('/tmp/features.db');

// Get highest feature ID
$result = $db->query('SELECT MAX(id) as max_id, COUNT(*) as total FROM features');
$row = $result->fetchArray(SQLITE3_ASSOC);
echo "Total features: " . $row['total'] . "\n";
echo "Highest feature ID: " . $row['max_id'] . "\n\n";

// Get features in progress
echo "Features IN PROGRESS:\n";
echo "=====================\n";
$result = $db->query('SELECT id, name, category FROM features WHERE in_progress = 1 ORDER BY id');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "Feature #" . $row['id'] . ": " . $row['name'] . " (" . $row['category'] . ")\n";
}

// Check features around 101
echo "\n\nFeatures near ID 101:\n";
echo "=====================\n";
$result = $db->query('SELECT id, name, passes, in_progress FROM features WHERE id >= 98 AND id <= 103 ORDER BY id');
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $status = $row['passes'] ? 'PASSED' : ($row['in_progress'] ? 'IN_PROGRESS' : 'PENDING');
    echo "Feature #" . $row['id'] . ": " . $row['name'] . " - " . $status . "\n";
}

$db->close();
