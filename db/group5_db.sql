-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 21 Ara 2025, 13:18:12
-- Sunucu sürümü: 8.0.44-0ubuntu0.22.04.1
-- PHP Sürümü: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `group5_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admins`
--

CREATE TABLE `admins` (
  `id` int UNSIGNED NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'System Admin', 'admin@example.com', 'admin', 'SUPER_ADMIN', '2025-11-30 11:22:20'),
(2, 'Order Manager', 'orders@example.com', 'manager', 'ORDER_MANAGER', '2025-11-30 11:22:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int UNSIGNED NOT NULL,
  `cart_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `item_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `item_total`) VALUES
(4, 2, 4, 1, 350.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Dog Food', 'Dry and wet dog food products'),
(2, 'Cat Toys', 'Toys and accessories for cats'),
(3, 'Accessories', 'Leashes, collars, and other accessories');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `coupons`
--

CREATE TABLE `coupons` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_usage` int UNSIGNED NOT NULL DEFAULT '0',
  `times_used` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by_admin_id` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_amount`, `min_order_amount`, `start_date`, `end_date`, `max_usage`, `times_used`, `is_active`, `created_by_admin_id`, `created_at`) VALUES
(2, 'FREESHIP30', 'Free shipping for orders over 150 TL', 30.00, 150.00, '2025-03-01', '2025-09-30', 0, 0, 1, 2, '2025-11-30 11:22:20'),
(4, 'WELCOME50', 'Welcome coupon for new users', 50.00, 200.00, '2025-01-01', '2025-12-31', 100, 0, 1, 1, '2025-12-01 13:36:29');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `coupon_id` int UNSIGNED DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(30) NOT NULL,
  `shipping_full_name` varchar(50) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `coupon_id`, `order_date`, `payment_method`, `shipping_full_name`, `shipping_address`, `shipping_phone`, `order_total`, `order_status`, `created_at`) VALUES
(1, 1, NULL, '2025-11-30 14:22:20', 'Credit Card', 'Hasan Tezcan', 'Istanbul, Turkey', '+90-555-111-2233', 720.00, 'confirmed', '2025-11-30 11:22:20'),
(2, 2, NULL, '2025-11-30 14:22:20', 'Cash on Delivery', 'Ayse Yilmaz', 'Kadikoy, Istanbul', '+90-555-222-3344', 350.00, 'cancelled', '2025-11-30 11:22:20'),
(3, 1, NULL, '2025-12-01 11:26:05', 'Credit Card', 'Hasan Tezcan', 'Istanbul, Turkey', '+90-555-111-2233', 720.00, 'pending', '2025-12-01 08:26:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `line_total`) VALUES
(1, 1, 1, 1, 450.00, 450.00),
(2, 1, 2, 1, 120.00, 120.00),
(3, 1, 3, 1, 200.00, 200.00),
(4, 2, 4, 1, 350.00, 350.00),
(5, 1, 2, 1, 120.00, 120.00),
(6, 1, 3, 1, 200.00, 200.00),
(7, 1, 5, 1, 200.00, 200.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image_url`, `is_active`, `created_at`) VALUES
(1, 1, 'Premium Dog Kibble 10kg', 'High quality dry dog food for adult dogs.', 450.00, 51, 'assets/images/product_kibble.png', 1, '2025-11-30 11:22:20'),
(2, 1, 'Salmon Cat Treats 200g', 'Soft salmon treats for cats.', 120.00, 101, 'assets/images/product_organic_treats.png', 1, '2025-11-30 11:22:20'),
(3, 3, 'Adjustable Dog Leash', 'Durable leash suitable for small and medium dogs.', 200.00, 31, 'assets/images/admin_product_leash.png', 1, '2025-11-30 11:22:20'),
(4, 2, 'Cat Scratching Post', 'Sturdy scratching post with sisal rope.', 350.00, 20, 'assets/images/product_feather_wand.png', 0, '2025-11-30 11:22:20'),
(5, 2, 'Bone Toy', 'Elastic toy for dogs to chew on', 200.00, 1, 'assets/images/product_chew_toy.png', 1, '2025-11-30 11:22:20'),
(6, 1, 'Premium Dog Kibble 12kg', 'Durable toy for dogs', 500.00, 60, 'assets/images/product_dog_food_large.png', 1, '2025-12-01 08:37:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `cart_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `shopping_carts`
--

INSERT INTO `shopping_carts` (`id`, `user_id`, `cart_total`, `created_at`) VALUES
(1, 1, 770.00, '2025-11-30 11:22:20'),
(2, 3, 350.00, '2025-11-30 11:22:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone_number`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Hasan Tezcan', 'hasan@example.com', '12345', '905551112233', 'Istanbul, Turkey', '2025-11-30 11:22:20', '2025-11-30 11:35:18'),
(2, 'Ayse Yilmaz', 'ayse@example.com', 'abcd', '905552223344', 'Kadikoy, Istanbul', '2025-11-30 11:22:20', '2025-11-30 11:35:22'),
(3, 'Mehmet Demir', 'mehmet@example.com', 'hasantezcan123', '905553334455', 'Besiktas, Istanbul', '2025-11-30 11:22:20', '2025-11-30 11:35:24'),
(4, 'Sezai Araplarlı', 'sezai@example.com', 'sezo123', '905551112233', 'Istanbul, Turkey', '2025-12-01 06:26:26', NULL);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `idx_cart_items_cart` (`cart_id`),
  ADD KEY `idx_cart_items_product` (`product_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Tablo için indeksler `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_coupons_admin` (`created_by_admin_id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_coupon` (`coupon_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`order_status`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_name` (`name`);

--
-- Tablo için indeksler `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_user` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `shopping_carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `fk_coupons_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
