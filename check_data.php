<?php
// Quick database checker
include_once('includes/db_connect.php');

echo "=== DATABASE CHECK ===\n\n";

// Check products
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
$count = $stmt->fetch()['count'];
echo "Total Products: $count\n";

$stmt = $pdo->query("SELECT id, name, image_url, price FROM products WHERE is_active = 1 LIMIT 5");
$products = $stmt->fetchAll();
echo "\nSample Products:\n";
foreach ($products as $p) {
    echo "- #{$p['id']}: {$p['name']} (\${$p['price']}) - {$p['image_url']}\n";
}

// Check categories
$stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
$count = $stmt->fetch()['count'];
echo "\nTotal Categories: $count\n";

// Check images
echo "\n=== IMAGE FILES CHECK ===\n";
$image_dir = 'assets/images/';
$images = glob($image_dir . '*');
echo "Total images in assets/images/: " . count($images) . "\n";
echo "First 10 images:\n";
foreach (array_slice($images, 0, 10) as $img) {
    echo "- " . basename($img) . "\n";
}
?>