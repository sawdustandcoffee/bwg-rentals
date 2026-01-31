<?php
$db = new PDO('sqlite:features.db');
$stmt = $db->prepare('SELECT * FROM features WHERE id = 86');
$stmt->execute();
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($feature, JSON_PRETTY_PRINT);
