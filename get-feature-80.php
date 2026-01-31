<?php
$db = new PDO('sqlite:features.db');
$stmt = $db->prepare('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 80');
$stmt->execute();
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
if ($feature) {
    echo "Feature #{$feature['id']}\n";
    echo "Category: {$feature['category']}\n";
    echo "Name: {$feature['name']}\n";
    echo "Description: {$feature['description']}\n";
    echo "Passes: " . ($feature['passes'] ? 'true' : 'false') . "\n";
    echo "In Progress: " . ($feature['in_progress'] ? 'true' : 'false') . "\n";
    echo "Dependencies: " . ($feature['dependencies'] ?: 'null') . "\n";
    echo "Steps:\n";
    $steps = json_decode($feature['steps'], true);
    foreach ($steps as $i => $step) {
        echo "  " . ($i + 1) . ". $step\n";
    }
}
