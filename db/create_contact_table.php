<?php
// Create contact_messages table if not exists
include_once("../../includes/db_connect.php");

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT DEFAULT 0
    )");
    echo "Contact messages table ready!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>