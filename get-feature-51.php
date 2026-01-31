<?php
/**
 * Get Feature #51 details from features.db
 */

$db_path = __DIR__ . '/features.db';

if (!file_exists($db_path)) {
    die("ERROR: features.db not found\n");
}

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare('SELECT * FROM features WHERE id = 51');
    $stmt->execute();
    $feature = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$feature) {
        die("ERROR: Feature #51 not found\n");
    }

    echo "=== FEATURE #51 DETAILS ===\n\n";
    echo "ID: " . $feature['id'] . "\n";
    echo "Category: " . $feature['category'] . "\n";
    echo "Name: " . $feature['name'] . "\n";
    echo "Description: " . $feature['description'] . "\n";
    echo "Steps: " . $feature['steps'] . "\n";
    echo "Passes: " . ($feature['passes'] ? 'true' : 'false') . "\n";
    echo "In Progress: " . ($feature['in_progress'] ? 'true' : 'false') . "\n";
    echo "Dependencies: " . ($feature['dependencies'] ?? 'null') . "\n";
    echo "Priority: " . $feature['priority'] . "\n";

} catch (PDOException $e) {
    die("ERROR: " . $e->getMessage() . "\n");
}
