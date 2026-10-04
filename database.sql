CREATE DATABASE IF NOT EXISTS quickbite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quickbite;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS otps, order_items, orders, offers, menu_items, menu_categories, vendors, users, settings;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE settings (
  `key` VARCHAR(80) PRIMARY KEY,
  `value` VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT INTO settings(`key`,`value`) VALUES
('commission_percent','10'),
('delivery_fee','40'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_secure', 'tls'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_from_name', 'QuickBite');

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','vendor','admin') NOT NULL DEFAULT 'customer',
  status ENUM('active','blocked','pending') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE vendors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  cuisine VARCHAR(255) NOT NULL,
  description TEXT NULL,
  image VARCHAR(500) NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 0,
  delivery_time VARCHAR(50) NOT NULL DEFAULT '30-40 mins',
  min_order DECIMAL(10,2) NOT NULL DEFAULT 0,
  badge VARCHAR(80) NULL,
  status ENUM('pending','approved','rejected','blocked') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE menu_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  vendor_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  price DECIMAL(10,2) NOT NULL,
  original_price DECIMAL(10,2) NULL,
  image VARCHAR(500) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
  FOREIGN KEY(category_id) REFERENCES menu_categories(id) ON DELETE SET NULL,
  INDEX(vendor_id),
  INDEX(category_id)
) ENGINE=InnoDB;

CREATE TABLE offers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  description TEXT NULL,
  code VARCHAR(50) NOT NULL UNIQUE,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  min_order DECIMAL(10,2) NOT NULL DEFAULT 0,
  expires_at DATETIME NULL,
  is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  vendor_id INT UNSIGNED NOT NULL,
  customer_name VARCHAR(120) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  discount DECIMAL(10,2) NOT NULL DEFAULT 0,
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  payment_method ENUM('cod') NOT NULL DEFAULT 'cod',
  status ENUM('pending','preparing','completed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id),
  FOREIGN KEY(vendor_id) REFERENCES vendors(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  menu_item_id INT UNSIGNED NOT NULL,
  item_name VARCHAR(160) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  line_total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(menu_item_id) REFERENCES menu_items(id)
) ENGINE=InnoDB;

CREATE TABLE otps (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  otp VARCHAR(10) NOT NULL,
  purpose ENUM('register', 'forgot_password') NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(email),
  INDEX(purpose)
) ENGINE=InnoDB;

INSERT INTO menu_categories(id, name) VALUES
(1, 'Burgers'),
(2, 'Pizza'),
(3, 'Momos'),
(4, 'Fast Food'),
(5, 'Coffee & Bakery'),
(6, 'Korean & Asian'),
(7, 'Thali & Nepali'),
(8, 'Pasta & Italian'),
(9, 'Desserts'),
(10, 'Drinks');

INSERT INTO users(id, name, email, phone, password_hash, role, status) VALUES
(1, 'System Admin', 'admin@quickbite.test', '9800000000', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'admin', 'active'),
(2, 'Café ABC Owner', 'vendor@quickbite.test', '9811111111', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(3, 'Dalle Owner', 'dalle@quickbite.test', '9822222222', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(4, 'KKFC Owner', 'kkfc@quickbite.test', '9833333333', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(5, 'Burger House Owner', 'burgerhouse@quickbite.test', '9844444444', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(6, 'Roadhouse Owner', 'roadhouse@quickbite.test', '9855555555', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(7, 'Himalayan Java Owner', 'java@quickbite.test', '9866666666', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(8, 'Bota Momo Owner', 'bota@quickbite.test', '9877777777', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(9, 'Bakery Cafe Owner', 'bakerycafe@quickbite.test', '9888888888', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active'),
(10, 'Hankook Sarang Owner', 'hankook@quickbite.test', '9899999999', '$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i', 'vendor', 'active');

INSERT INTO vendors(id, user_id, name, cuisine, description, image, rating, delivery_time, min_order, badge, status) VALUES
(1, 3, 'Dalle', 'Nepali, Momos, Asian, Spicy', 'Kathmandu\'s favorite destination for fiery Dalle chilies, juicy steam & fried momos, and spicy bowls.', 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=600&q=80', 4.9, '15-25 mins', 150, 'Top Rated', 'approved'),
(2, 4, 'KKFC (Krunchy Fried Chicken)', 'Fast Food, Fried Chicken, Burgers', 'Crispy, crunchy fried chicken buckets, zinger burgers, and loaded fries made fresh to order.', 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=600&q=80', 4.8, '20-30 mins', 200, 'Popular', 'approved'),
(3, 5, 'The Burger House & CFC', 'Fast Food, Burgers, Wings', 'Giant stacked burgers, crunchy chicken wings, seasoned fries, and refreshing cold drinks.', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80', 4.7, '25-35 mins', 200, 'Value Favorite', 'approved'),
(4, 6, 'Roadhouse Cafe', 'Italian, Pizza, Pasta', 'Authentic wood-fired pizzas, handcrafted Italian pastas, and gourmet continental dishes.', 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&q=80', 4.8, '30-40 mins', 350, 'Pathao Select', 'approved'),
(5, 7, 'Himalayan Java Coffee', 'Coffee & Bakery, Breakfast, Desserts', 'Handcrafted specialty coffees, fresh artisan muffins, bagels, and delicious pastries.', 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&q=80', 4.9, '15-25 mins', 180, 'Express Delivery', 'approved'),
(6, 8, 'Bota Momo', 'Momos, Nepali, Fast Food', 'Traditional and fusion momos served in eco-friendly bota with signature spicy chutneys.', 'https://images.unsplash.com/photo-1625220194771-7ebdea0b70b9?w=600&q=80', 4.7, '20-30 mins', 150, 'Trending', 'approved'),
(7, 9, 'The Bakery Cafe', 'Bakery, Continental, Momos', 'Famous for legendary momos, club sandwiches, sizzling chicken steaks, and fresh cakes.', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80', 4.6, '25-35 mins', 200, 'Classic Choice', 'approved'),
(8, 10, 'Hankook Sarang', 'Korean & Asian, Ramen, BBQ', 'Authentic Korean BBQ, Yangnyeom crispy chicken, Kimchi stew, and Korean rice cakes.', 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=600&q=80', 4.8, '30-45 mins', 400, 'Premium', 'approved'),
(9, 2, 'Café ABC', 'American, Beverages', 'Fresh burgers, golden fries, chilled coffees, and signature sandwiches.', './images/pizza.png', 4.5, '30-40 mins', 200, 'Verified Partner', 'approved');

-- Menu Items
INSERT INTO menu_items(vendor_id, category_id, name, description, price, original_price, image) VALUES
-- Dalle
(1, 3, 'Dalle Buff Steam Momo', 'Juicy buff momos filled with fresh spices, served with signature spicy Dalle chutney.', 220, 250, './images/momo.png'),
(1, 3, 'Dalle Chicken C-Momo', 'Crispy fried chicken momos tossed in spicy Dalle chili & capsicum gravy.', 280, 320, './images/momo.png'),
(1, 3, 'Dalle Pork Steam Momo', 'Classic tender pork momos paired with house peanut & chili dipping sauce.', 260, NULL, './images/momo.png'),
(1, 4, 'Crispy Dalle Potato Basket', 'Golden potato wedges dusted with fiery Dalle chili powder.', 180, NULL, 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG'),

-- KKFC
(2, 4, '8 Pcs Hot & Crispy Bucket', '8 pieces of signature golden crispy fried chicken with garlic mayo.', 850, 990, 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=500&q=80'),
(2, 1, 'KKFC Krunchy Zinger Burger', 'Extra crispy fried chicken breast with lettuce, cheese, and spicy mayo.', 320, 360, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80'),
(2, 4, 'Spicy Wings (6 Pcs)', 'Tender chicken wings coated in hot krunchy batter.', 290, NULL, 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=500&q=80'),

-- Burger House
(3, 1, 'Double Cheese Crunch Burger', 'Dual beef/chicken patties stacked with melted cheddar & house sauce.', 350, 390, 'https://images.unsplash.com/photo-1599474151439-9f3c4e972009?w=500&q=80'),
(3, 4, 'CFC Spicy Chicken Wrap', 'Crispy chicken strips, lettuce & chipotle wrap in warm tortilla.', 240, NULL, 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=500&q=80'),
(3, 4, 'Loaded Cheesy Fries', 'French fries topped with melted cheese, jalapenos and crispy bacon bits.', 220, 250, 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG'),

-- Roadhouse Cafe
(4, 2, 'Wood-Fired Smoked Chicken Pizza', 'Thin crust wood-fired pizza with smoked chicken, mozzarella & fresh basil.', 680, 750, './images/pizza.png'),
(4, 2, 'Margherita Supreme Pizza', 'Classic Italian pizza with rich tomato sauce, fresh mozzarella & oregano.', 540, NULL, './images/pizza.png'),
(4, 8, 'Creamy Fettuccine Alfredo', 'Handmade pasta in rich garlic cream sauce with wild mushrooms.', 490, 550, 'https://images.unsplash.com/photo-1621996346565-e3d5d6281729?w=500&q=80'),

-- Himalayan Java
(5, 5, 'Iced Caramel Latte', 'Espresso shot blended with chilled milk and rich caramel syrup.', 260, NULL, 'https://images.unsplash.com/photo-1676506739319-70bff65bfc48?w=500&q=80'),
(5, 5, 'Java Signature Cappuccino', 'Rich dark roast espresso with silky hot steamed milk froth.', 210, NULL, 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500&q=80'),
(5, 9, 'Fresh Blueberry Muffin', 'Soft oven-baked muffin loaded with wild blueberries.', 160, 180, './images/dessert.png'),

-- Bota Momo
(6, 3, 'Bota Open Buff Momo', 'Unique open-topped momos stuffed with spiced buff and fiery gravy.', 210, NULL, './images/momo.png'),
(6, 3, 'Chicken Sadeko Momo', 'Steamed chicken momos tossed with onion, coriander, lime & mustard oil.', 240, 270, './images/momo.png'),

-- Bakery Cafe
(7, 3, 'Classic Steam Chicken Momo', 'The legendary Kathmandu chicken momo with rich spiced yellow achar.', 230, NULL, './images/momo.png'),
(7, 4, 'Club Sandwich with Fries', 'Triple-decker sandwich loaded with grilled chicken, egg, & bacon.', 380, 420, 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=500&q=80'),

-- Hankook Sarang
(8, 6, 'Yangnyeom Korean Fried Chicken', 'Sweet & spicy sticky glazed Korean fried chicken with sesame seeds.', 650, 720, 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=500&q=80'),
(8, 6, 'Spicy Kimchi Jjigae Stew', 'Traditional fermented kimchi stew served with tofu & steamed rice.', 480, NULL, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80'),

-- Café ABC
(9, 1, 'Cheese Burger', 'Juicy grilled patty with cheese, fresh veggies and house sauce.', 270, 300, './images/pizza.png'),
(9, 4, 'French Fries', 'Crispy golden fries with special seasoning.', 150, NULL, 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG');

INSERT INTO offers(title, description, code, discount_percent, min_order, expires_at) VALUES
('First Order', 'Get 20% off your first QuickBite order.', 'WELCOME20', 20, 0, DATE_ADD(NOW(), INTERVAL 90 DAY)),
('Pathao Eats Super Saver', 'Save 25% on popular Kathmandu restaurants.', 'PATHAOEATS', 25, 400, DATE_ADD(NOW(), INTERVAL 60 DAY)),
('Dalle Momo Craze', 'Flat Rs. 50 off on all Dalle orders.', 'DALLE50', 15, 250, DATE_ADD(NOW(), INTERVAL 60 DAY)),
('Free Delivery', 'Zero delivery fee on orders over Rs. 500.', 'FREEFEES', 0, 500, DATE_ADD(NOW(), INTERVAL 90 DAY));
