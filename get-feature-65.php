<?php
$db = new SQLite3('features.db');
$result = $db->query("SELECT * FROM features WHERE id = 65");
$feature = $result->fetchArray(SQLITE3_ASSOC);
echo json_encode($feature, JSON_PRETTY_PRINT);
