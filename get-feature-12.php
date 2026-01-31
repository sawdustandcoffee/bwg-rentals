<?php
$db = new SQLite3('features.db');
$result = $db->query('SELECT * FROM features WHERE id = 12');
$feature = $result->fetchArray(SQLITE3_ASSOC);
if ($feature) {
    echo json_encode($feature, JSON_PRETTY_PRINT);
} else {
    echo "Feature not found";
}
