#!/usr/bin/env php
<?php
$db = new SQLite3('features.db');
$result = $db->query("SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 50");
$row = $result->fetchArray(SQLITE3_ASSOC);
if ($row) {
    $row['steps'] = json_decode($row['steps'], true);
    $row['dependencies'] = json_decode($row['dependencies'], true);
    echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Feature #50 not found\n";
}
$db->close();
