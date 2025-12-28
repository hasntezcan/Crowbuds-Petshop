-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 28 Ara 2025, 01:37:31
-- Sunucu sürümü: 10.4.28-MariaDB
-- PHP Sürümü: 8.2.4

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
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$EBrMMWzoGRJ2FiquKgt9QurPzhzrGnS72NgB6j8MwvEWWw1woTCGi', 'SUPER_ADMIN', '2025-11-30 11:22:20'),
(2, 'Order Manager', 'orders@example.com', '$2y$10$NrhI/4H53vNMgIQV2sCWkeLPpXXBpSH9NxaHuCaAono2Z0xtqtAV6', 'ORDER_MANAGER', '2025-11-30 11:22:20');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `cart_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `item_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `item_total`) VALUES
(10, 1, 20, 1, 189.00),
(11, 1, 12, 1, 89.00),
(12, 1, 18, 2, 218.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Dog Food', 'Premium dry and wet food for dogs of all ages'),
(2, 'Dog Toys', 'Fun and durable toys for dogs'),
(3, 'Dog Accessories', 'Collars, leashes, bowls, and essentials for dogs'),
(4, 'Cat Food', 'Nutritious food for cats and kittens'),
(5, 'Cat Toys', 'Interactive toys and scratchers for cats'),
(6, 'Cat Accessories', 'Litter boxes, carriers, and cat essentials'),
(7, 'Grooming', 'Shampoos, brushes, and grooming supplies'),
(8, 'Health & Wellness', 'Vitamins, supplements, and health products'),
(9, 'Small Pets', 'Food and supplies for rabbits, hamsters, birds');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `is_read`) VALUES
(1, 'asdasdasd', 'hasan@example.com', 'deneme', 'of hadi be site bit yav', '2025-12-27 19:14:55', 0),
(2, 'asd', 'admin@example.com', 'deneme', 'asdasd', '2025-12-27 22:07:42', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `coupons`
--

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `max_usage` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `times_used` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_amount`, `min_order_amount`, `start_date`, `end_date`, `max_usage`, `times_used`, `is_active`, `created_by_admin_id`, `created_at`) VALUES
(2, 'FREESHIP30', 'Free shipping for orders over 150 TL', 30.00, 150.00, '2025-03-01', '2025-09-30', 0, 0, 1, 2, '2025-11-30 11:22:20'),
(4, 'WELCOME50', 'Welcome coupon for new users', 50.00, 200.00, '2025-01-01', '2026-12-31', 100, 0, 1, 1, '2025-12-01 13:36:29');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `coupon_id` int(10) UNSIGNED DEFAULT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(30) NOT NULL,
  `shipping_full_name` varchar(50) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `coupon_id`, `order_date`, `payment_method`, `shipping_full_name`, `shipping_address`, `shipping_phone`, `order_total`, `order_status`, `created_at`) VALUES
(1, 1, NULL, '2025-12-27 23:49:14', 'Cash on Delivery', 'hasan tezcan', 'cumhuriyey mah.', '5531092919', 385.00, 'pending', '2025-12-27 20:49:14'),
(2, 1, NULL, '2025-12-28 02:30:08', 'Cash on Delivery', 'hasan tezcan', 'kupon yazma yeri yok la HAHAHAHHAA', '5531092919', 527.00, 'pending', '2025-12-27 23:30:08');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `line_total`) VALUES
(1, 1, 20, 1, 160.00, 160.00),
(2, 1, 13, 1, 85.00, 85.00),
(3, 1, 19, 1, 140.00, 140.00),
(4, 2, 20, 1, 189.00, 189.00),
(5, 2, 19, 2, 169.00, 338.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image_url`, `is_active`, `created_at`) VALUES
(1, 1, 'Royal Canin Medium Adult 10kg', 'Premium dry dog food for adult dogs (1-7 years, 11-25kg). Balanced nutrition with high-quality proteins', 549.00, 50, 'assets/images/royal_canin_dog.png', 1, '2025-12-27 18:48:50'),
(2, 1, 'Pedigree Puppy Food 5kg', 'Complete nutrition for growing puppies. DHA for brain development, calcium for strong bones', 389.00, 35, 'assets/images/pedigree_puppy.png', 1, '2025-12-27 18:48:50'),
(3, 1, 'Purina Pro Plan Senior 8kg', 'Senior dog formula (7+ years) with joint support. Easy to digest, enhanced mobility', 479.00, 25, 'assets/images/purina_senior.png', 1, '2025-12-27 18:48:50'),
(4, 2, 'KONG Classic Red - Large', 'Ultra-durable rubber toy for aggressive chewers. Can be stuffed with treats. Made in USA', 179.00, 60, 'assets/images/kong_toy.png', 1, '2025-12-27 18:48:50'),
(5, 2, 'Heavy Duty Rope Tug Toy', 'Multi-colored braided rope for interactive tug-of-war. Dental benefits, suitable for medium-large dogs', 129.00, 45, 'assets/images/rope_toy.png', 1, '2025-12-27 18:48:50'),
(6, 2, 'Chuckit! Tennis Ball 3-Pack', 'High-bounce rubber tennis balls for fetch. Compatible with Chuckit! launchers. Medium size', 119.00, 80, 'assets/images/tennis_balls.png', 1, '2025-12-27 18:48:50'),
(7, 3, 'Premium Padded Dog Leash 1.5m', 'Heavy-duty nylon leash with padded foam handle for comfort. Reflective stitching for safety', 229.00, 40, 'assets/images/dog_leash.png', 1, '2025-12-27 18:48:50'),
(8, 3, 'Genuine Leather Dog Collar', 'Premium full-grain leather collar with rust-proof metal buckle. For medium to large dogs', 199.00, 30, 'assets/images/dog_collar.png', 1, '2025-12-27 18:48:50'),
(9, 3, 'Stainless Steel Pet Bowl 1.5L', 'Heavy-duty stainless steel bowl with non-slip rubber base. Dishwasher safe, rust resistant', 149.00, 50, 'assets/images/dog_bowl.png', 1, '2025-12-27 18:48:50'),
(10, 4, 'Whiskas Adult Cat Food 5kg', 'Complete dry food for adult cats with real fish. Omega 6 for healthy skin and shiny coat', 369.00, 40, 'assets/images/whiskas_cat.png', 1, '2025-12-27 18:48:50'),
(11, 4, 'Royal Canin Kitten Food 3kg', 'Specially formulated for kittens up to 12 months. Supports immune system and digestive health', 329.00, 30, 'assets/images/royal_canin_kitten.png', 1, '2025-12-27 18:48:50'),
(12, 4, 'Temptations Cat Treats 200g', 'Irresistible crunchy treats with salmon flavor. Less than 2 calories per treat. No artificial colors', 89.00, 100, 'assets/images/cat_treats.png', 1, '2025-12-27 18:48:50'),
(13, 5, 'Interactive Feather Wand Toy', 'Retractable wand with colorful feathers and bell. Encourages natural hunting instincts', 95.00, 55, 'assets/images/cat_wand.png', 1, '2025-12-27 18:48:50'),
(14, 5, 'Catnip Mouse Toy - 2 Pack', 'Soft felt mice filled with 100% organic catnip. Perfect size for batting and carrying', 69.00, 70, 'assets/images/catnip_mouse.png', 1, '2025-12-27 18:48:50'),
(15, 5, 'Natural Sisal Scratching Post 50cm', 'Tall scratching post with natural sisal rope. Stable base, protects furniture from damage', 299.00, 20, 'assets/images/scratching_post.png', 1, '2025-12-27 18:48:50'),
(16, 6, 'Covered Cat Litter Box with Filter', 'Large hooded litter box with carbon filter for odor control. Easy-clean removable top', 349.00, 25, 'assets/images/litter_box.png', 1, '2025-12-27 18:48:50'),
(17, 6, 'Hard-Sided Pet Travel Carrier', 'Airline-approved cat carrier with metal door and top opening. Ventilation on 3 sides', 469.00, 15, 'assets/images/cat_carrier.png', 1, '2025-12-27 18:48:50'),
(18, 7, 'Natural Pet Shampoo 500ml', 'Gentle oatmeal shampoo for sensitive skin. Hypoallergenic, pH-balanced for dogs and cats', 109.00, 60, 'assets/images/pet_shampoo.png', 1, '2025-12-27 18:48:50'),
(19, 7, 'Professional Grooming Set', 'Complete brush and comb set with stainless steel pins. Removes tangles and loose fur', 169.00, 35, 'assets/images/grooming_set.png', 1, '2025-12-27 18:48:50'),
(20, 8, 'Daily Multivitamin Soft Chews', 'Complete vitamin supplement for dogs of all sizes. Supports immune system, joints, and coat', 189.00, 45, 'assets/images/multivitamin.png', 1, '2025-12-27 18:48:50');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `shopping_carts`
--

CREATE TABLE `shopping_carts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `cart_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `shopping_carts`
--

INSERT INTO `shopping_carts` (`id`, `user_id`, `cart_total`, `created_at`) VALUES
(1, 1, 496.00, '2025-12-27 18:51:27');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone_number`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Hasan Tezcan', 'hasan@example.com', '$2y$10$7jRFWk9lcsbzOD4/MrKFg.LO65dLtr78ZgKJDNyotr5YSMyq6kRiW', '905551112233', 'Istanbul, Turkey', '2025-11-30 11:22:20', '2025-12-27 13:46:31'),
(2, 'Ayse Yilmaz', 'ayse@example.com', '$2y$10$1OnScOMKKdYF4YGam4H5gOi/j8yu0XLiedifGrO9hX8rpx6VXbMuK', '905552223344', 'Kadikoy, Istanbul', '2025-11-30 11:22:20', '2025-12-27 13:46:31'),
(3, 'Mehmet Demir', 'mehmet@example.com', '$2y$10$qvDA0HZSmOC/Ce/Jt1K5X.p/AZNgqhKj/nZfNh.thOa4rp9QyfckO', '905553334455', 'Besiktas, Istanbul', '2025-11-30 11:22:20', '2025-12-27 13:46:32'),
(4, 'Sezai Araplarlı', 'sezai@example.com', '$2y$10$T/X1k9CmqKaZ5zCpbj0ifOCiLXslVWvydZ47mnker22Hw5huzy9Ke', '905551112233', 'Istanbul, Turkey', '2025-12-01 06:26:26', '2025-12-27 13:46:32');

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
-- Tablo için indeksler `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Tablo için AUTO_INCREMENT değeri `shopping_carts`
--
ALTER TABLE `shopping_carts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `shopping_carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `shopping_carts`
--
ALTER TABLE `shopping_carts`
  ADD CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
