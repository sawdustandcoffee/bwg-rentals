<?php
$db = new PDO('sqlite:features.db');
$stmt = $db->prepare('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 40');
$stmt->execute();
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
if ($feature) {
    $feature['steps'] = json_decode($feature['steps'], true);
    $feature['dependencies'] = json_decode($feature['dependencies'], true);
    echo json_encode($feature, JSON_PRETTY_PRINT);
} else {
    echo "Feature #40 not found\n";
}
