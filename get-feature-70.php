<?php
$db = new SQLite3("features.db");
$result = $db->query("SELECT * FROM features WHERE id = 70");
$feature = $result->fetchArray(SQLITE3_ASSOC);
if ($feature) {
    echo "ID: " . $feature["id"] . "\n";
    echo "Priority: " . $feature["priority"] . "\n";
    echo "Category: " . $feature["category"] . "\n";
    echo "Name: " . $feature["name"] . "\n";
    echo "Description: " . $feature["description"] . "\n";
    echo "Steps: " . $feature["steps"] . "\n";
    echo "Passes: " . $feature["passes"] . "\n";
    echo "In Progress: " . $feature["in_progress"] . "\n";
    if (!empty($feature["dependencies"])) {
        echo "Dependencies: " . $feature["dependencies"] . "\n";
    }
}
$db->close();
?>
