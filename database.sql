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

INSERT INTO menu_categories(name) VALUES
('Burgers'),('Pizza'),('Momos'),('Fast Food'),('Drinks'),('Desserts'),('Thali'),('Pasta');

INSERT INTO users(name,email,phone,password_hash,role,status) VALUES
('System Admin','admin@quickbite.test','9800000000','$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i','admin','active'),
('Café ABC Owner','vendor@quickbite.test','9811111111','$2y$12$BTlXE/Sh4KmDnWHzXuoD9uf41J.ED0IacNVWwb.TzJ46HrPFd/b/i','vendor','active');

INSERT INTO vendors(user_id,name,cuisine,description,image,rating,delivery_time,min_order,status) VALUES
(2,'Café ABC','American, Beverages','Fresh burgers, fries and drinks.','./images/pizza.png',4.5,'30-40 mins',200,'approved');

INSERT INTO menu_items(vendor_id,category_id,name,description,price,original_price,image) VALUES
(1,1,'Cheese Burger','Juicy grilled patty with cheese, fresh veggies and house sauce.',270,300,'./images/pizza.png'),
(1,1,'Veg Burger','Crispy veg patty with lettuce and mayo.',220,NULL,'./images/pizza.png'),
(1,4,'French Fries','Crispy golden fries with special seasoning.',150,NULL,'./images/momo.png'),
(1,5,'Cold Coffee','Chilled coffee with ice cream.',180,NULL,'./images/dessert.png');

INSERT INTO offers(title,description,code,discount_percent,min_order,expires_at) VALUES
('First Order','Get 20% off your first QuickBite order.','WELCOME20',20,0,DATE_ADD(NOW(),INTERVAL 90 DAY)),
('Lunch Break','Free delivery on selected lunch orders.','LUNCHFREE',0,0,DATE_ADD(NOW(),INTERVAL 60 DAY)),
('Weekend Treat','Save on selected dishes during the weekend.','WEEKEND10',10,500,DATE_ADD(NOW(),INTERVAL 90 DAY));
