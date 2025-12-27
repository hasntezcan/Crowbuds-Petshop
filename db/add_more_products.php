<?php
// Add more products to database
include_once('../includes/db_connect.php');

$products = [
    ['name' => 'Cat Food Premium 5kg', 'cat_id' => 2, 'desc' => 'High quality cat food with real fish', 'price' => 350.00, 'stock' => 15, 'img' => 'assets/images/product_cat_food.png'],
    ['name' => 'Dog Collar Leather', 'cat_id' => 3, 'desc' => 'Durable leather collar for medium/large dogs', 'price' => 150.00, 'stock' => 25, 'img' => 'assets/images/admin_product_leash.png'],
    ['name' => 'Cat Scratching Post', 'cat_id' => 2, 'desc' => 'Natural sisal rope scratching post', 'price' => 280.00, 'stock' => 10, 'img' => 'assets/images/product_cat_toy.png'],
    ['name' => 'Pet Shampoo', 'cat_id' => 3, 'desc' => 'Gentle shampoo for dogs and cats', 'price' => 80.00, 'stock' => 30, 'img' => 'assets/images/product_shampoo.png'],
    ['name' => 'Dog Water Bowl', 'cat_id' => 3, 'desc' => 'Stainless steel non-slip water bowl', 'price' => 120.00, 'stock' => 20, 'img' => 'assets/images/product_bowl.png'],
    ['name' => 'Cat Treats Variety Pack', 'cat_id' => 2, 'desc' => 'Assorted flavors cat treats', 'price' => 95.00, 'stock' => 40, 'img' => 'assets/images/product_organic_treats.png'],
];

try {
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock_quantity, image_url, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");

    foreach ($products as $p) {
        $stmt->execute([$p['cat_id'], $p['name'], $p['desc'], $p['price'], $p['stock'], $p['img']]);
        echo "Added: " . $p['name'] . "\n";
    }

    echo "\n✅ Successfully added " . count($products) . " products!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>