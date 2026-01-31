<?php
$db = new PDO('sqlite:features.db');
$stmt = $db->prepare('SELECT * FROM features WHERE id = ?');
$stmt->execute([9]);
$feature = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($feature, JSON_PRETTY_PRINT);
