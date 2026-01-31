<?php
$db = new PDO('sqlite:features.db');
$stmt = $db->prepare('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = ?');
$stmt->execute([43]);
$feature = $stmt->fetch(PDO::FETCH_ASSOC);

if ($feature) {
    echo "ID: " . $feature['id'] . "\n";
    echo "Category: " . $feature['category'] . "\n";
    echo "Name: " . $feature['name'] . "\n";
    echo "Description: " . $feature['description'] . "\n";
    echo "Steps: " . $feature['steps'] . "\n";
    echo "Passes: " . $feature['passes'] . "\n";
    echo "In Progress: " . $feature['in_progress'] . "\n";
    echo "Dependencies: " . $feature['dependencies'] . "\n";
} else {
    echo "Feature not found\n";
}
