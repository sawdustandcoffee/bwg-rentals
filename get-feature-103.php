<?php
$db = new SQLite3('features.db');
$result = $db->query('SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 103');
$row = $result->fetchArray(SQLITE3_ASSOC);
if ($row) {
    $row['steps'] = json_decode($row['steps'], true);
    $row['dependencies'] = json_decode($row['dependencies'], true);
    $row['passes'] = (bool)$row['passes'];
    $row['in_progress'] = (bool)$row['in_progress'];
    echo json_encode($row, JSON_PRETTY_PRINT);
} else {
    echo 'Feature #103 not found';
}
