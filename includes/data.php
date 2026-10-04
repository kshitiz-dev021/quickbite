<?php
/**
 * Shared data layer backed by MySQL Database (quickbite).
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$photo = [
    'burger' => 'https://images.unsplash.com/photo-1599474151439-9f3c4e972009?w=400&q=80&auto=format&fit=crop',
    'momo'   => './images/momo.png',
    'pizza'  => './images/pizza.png',
    'fries'  => 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG',
    'coffee' => 'https://images.unsplash.com/photo-1676506739319-70bff65bfc48?w=400&q=80&auto=format&fit=crop',
    'thali'  => './images/Thali.png',
    'dessert'=> './images/dessert.png',
];

$db = get_db();

// -------------------------------------------------------------
// Approved Vendors for storefront
// -------------------------------------------------------------
$stmt = $db->query("
    SELECT id, name, cuisine, rating, delivery_time as time, min_order, 
           COALESCE(NULLIF(image, ''), './images/pizza.png') as img,
           description, status
    FROM vendors 
    WHERE status = 'approved'
    ORDER BY rating DESC, id ASC
");
$vendors = $stmt->fetchAll();

// If no approved vendors exist, populate fallback array to prevent crashes
if (empty($vendors)) {
    $vendors = [
        ['id' => 1, 'name' => 'Café ABC', 'cuisine' => 'American, Beverages', 'rating' => 4.5, 'time' => '30-40 mins', 'min_order' => 200, 'img' => $photo['burger']]
    ];
}

// -------------------------------------------------------------
// Active Menu Items for storefront
// -------------------------------------------------------------
$mStmt = $db->query("
    SELECT m.id, m.vendor_id as vendor, v.name as vendor_name, m.name, 
           COALESCE(m.description, '') as `desc`, 
           CAST(m.price AS SIGNED) as price, 
           CASE WHEN m.original_price IS NOT NULL THEN CAST(m.original_price AS SIGNED) ELSE NULL END as was, 
           CASE WHEN m.original_price > m.price THEN ROUND(((m.original_price - m.price) / m.original_price) * 100) ELSE 0 END as discount,
           COALESCE(c.name, 'Other') as cat,
           COALESCE(NULLIF(m.image, ''), './images/pizza.png') as img,
           m.is_active
    FROM menu_items m
    JOIN vendors v ON m.vendor_id = v.id
    LEFT JOIN menu_categories c ON m.category_id = c.id
    WHERE m.is_active = 1
    ORDER BY m.id ASC
");
$menu_items = $mStmt->fetchAll();

// -------------------------------------------------------------
// Menu Categories
// -------------------------------------------------------------
$cStmt = $db->query("SELECT id, name FROM menu_categories ORDER BY id ASC");
$menu_categories = $cStmt->fetchAll();

// -------------------------------------------------------------
// Promotional Offers
// -------------------------------------------------------------
$oStmt = $db->query("
    SELECT id, title, description, code, discount_percent, min_order, expires_at, is_active
    FROM offers 
    WHERE is_active = 1 AND (expires_at IS NULL OR expires_at >= NOW())
    ORDER BY id ASC
");
$offers = $oStmt->fetchAll();

// -------------------------------------------------------------
// Current Vendor Scope (for vendor dashboard pages)
// -------------------------------------------------------------
$currentVendorId = 1;
if (is_logged_in() && !empty($_SESSION['user']['vendor_id'])) {
    $currentVendorId = (int)$_SESSION['user']['vendor_id'];
}

// Vendor orders
$voStmt = $db->prepare("
    SELECT o.id, o.customer_name as customer, 
           COUNT(oi.id) as items, 
           o.total as amount, 
           o.status, o.created_at, o.address, o.phone
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.vendor_id = ?
    GROUP BY o.id
    ORDER BY o.id DESC
");
$voStmt->execute([$currentVendorId]);
$vendor_orders = $voStmt->fetchAll();

// Vendor menu items
$vmiStmt = $db->prepare("
    SELECT m.id, m.name, CAST(m.price AS SIGNED) as price, 
           CASE WHEN m.original_price > m.price THEN CONCAT(ROUND(((m.original_price - m.price) / m.original_price) * 100), '%') ELSE '—' END as discount,
           CASE WHEN m.is_active = 1 THEN 'active' ELSE 'inactive' END as status,
           m.category_id, c.name as category_name, m.original_price, m.image, m.description
    FROM menu_items m
    LEFT JOIN menu_categories c ON m.category_id = c.id
    WHERE m.vendor_id = ?
    ORDER BY m.id DESC
");
$vmiStmt->execute([$currentVendorId]);
$vendor_menu_items = $vmiStmt->fetchAll();

// Vendor discounts list
$vdStmt = $db->prepare("
    SELECT id, name, CAST(original_price AS SIGNED) as original, 
           CONCAT(ROUND(((original_price - price) / original_price) * 100), '%') as pct, 
           CAST(price AS SIGNED) as net
    FROM menu_items
    WHERE vendor_id = ? AND original_price IS NOT NULL AND original_price > price
    ORDER BY id DESC
");
$vdStmt->execute([$currentVendorId]);
$vendor_discounts = $vdStmt->fetchAll();

// -------------------------------------------------------------
// Admin Data Layer
// -------------------------------------------------------------
// All registered vendors
$avStmt = $db->query("
    SELECT v.id, v.name, u.email, v.status, 
           DATE_FORMAT(v.created_at, '%d %b %Y') as joined,
           v.cuisine, v.rating
    FROM vendors v
    JOIN users u ON v.user_id = u.id
    ORDER BY v.id DESC
");
$admin_vendors = $avStmt->fetchAll();

// Pending vendor approvals
$aaStmt = $db->query("
    SELECT v.id, u.name as vendor, u.email, v.name as shop, 
           DATE_FORMAT(v.created_at, '%d %b %Y') as joined,
           v.cuisine
    FROM vendors v
    JOIN users u ON v.user_id = u.id
    WHERE v.status = 'pending'
    ORDER BY v.id DESC
");
$admin_approvals = $aaStmt->fetchAll();

// Admin recent activity
$admin_activity = [];
$actOrders = $db->query("
    SELECT CONCAT('Order #ORD-', id, ' placed for Rs. ', total, '.') as text, 
           DATE_FORMAT(created_at, '%H:%i %p') as `time`
    FROM orders 
    ORDER BY id DESC LIMIT 3
")->fetchAll();
foreach ($actOrders as $ao) {
    $admin_activity[] = $ao;
}

$actVendors = $db->query("
    SELECT CONCAT('New vendor \"', name, '\" registered.') as text, 
           DATE_FORMAT(created_at, '%d %b') as `time`
    FROM vendors 
    ORDER BY id DESC LIMIT 2
")->fetchAll();
foreach ($actVendors as $av) {
    $admin_activity[] = $av;
}

$actUsers = $db->query("
    SELECT CONCAT('New user \"', name, '\" signed up.') as text, 
           DATE_FORMAT(created_at, '%d %b') as `time`
    FROM users 
    WHERE role = 'customer'
    ORDER BY id DESC LIMIT 2
")->fetchAll();
foreach ($actUsers as $au) {
    $admin_activity[] = $au;
}

if (empty($admin_activity)) {
    $admin_activity = [
        ['text' => 'QuickBite platform running normally.', 'time' => 'Just now']
    ];
}

// Admin Users
$auStmt = $db->query("
    SELECT u.id, u.name, u.email, 
           COUNT(o.id) as orders, 
           DATE_FORMAT(u.created_at, '%d %b %Y') as joined,
           u.status
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.role = 'customer'
    GROUP BY u.id
    ORDER BY u.id DESC
");
$admin_users = $auStmt->fetchAll();

/**
 * HTML status pill badge
 */
function status_pill(string $status): string
{
    $status = strtolower($status);
    $label = ucfirst($status);
    return "<span class=\"status-pill status-pill--{$status}\">{$label}</span>";
}
