<?php
/**
 * Shared data layer backed by MySQL Database (quickbite) with fail-safe fallback.
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

$vendors = [];
$menu_items = [];
$menu_categories = [];
$offers = [];
$db = null;

try {
    $db = get_db();
    
    // Approved Vendors for storefront
    $stmt = $db->query("
        SELECT id, name, cuisine, rating, delivery_time as time, min_order, 
               COALESCE(NULLIF(image, ''), './images/pizza.png') as img,
               COALESCE(badge, 'Approved') as badge,
               description, status
        FROM vendors 
        WHERE status = 'approved'
        ORDER BY rating DESC, id ASC
    ");
    $vendors = $stmt->fetchAll();

    // Active Menu Items for storefront
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

    // Menu Categories
    $cStmt = $db->query("SELECT id, name FROM menu_categories ORDER BY id ASC");
    $menu_categories = $cStmt->fetchAll();

    // Promotional Offers
    $oStmt = $db->query("
        SELECT id, title, description, code, discount_percent, min_order, expires_at, is_active
        FROM offers 
        WHERE is_active = 1 AND (expires_at IS NULL OR expires_at >= NOW())
        ORDER BY id ASC
    ");
    $offers = $oStmt->fetchAll();
} catch (Exception $e) {
    // Database unavailable — will fallback to in-memory datasets below
}

// -------------------------------------------------------------
// Fallback Data Layer (Popular Vendors & Pathao-style items)
// -------------------------------------------------------------
if (empty($vendors)) {
    $vendors = [
        [
            'id' => 1,
            'name' => 'Dalle',
            'cuisine' => 'Nepali, Momos, Asian, Spicy',
            'rating' => 4.9,
            'time' => '15-25 mins',
            'min_order' => 150,
            'img' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=600&q=80',
            'badge' => 'Top Rated',
            'description' => 'Kathmandu\'s favorite destination for fiery Dalle chilies, juicy steam & fried momos, and spicy bowls.',
            'status' => 'approved'
        ],
        [
            'id' => 2,
            'name' => 'KKFC (Krunchy Fried Chicken)',
            'cuisine' => 'Fast Food, Fried Chicken, Burgers',
            'rating' => 4.8,
            'time' => '20-30 mins',
            'min_order' => 200,
            'img' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=600&q=80',
            'badge' => 'Popular',
            'description' => 'Crispy, crunchy fried chicken buckets, zinger burgers, and loaded fries made fresh to order.',
            'status' => 'approved'
        ],
        [
            'id' => 3,
            'name' => 'The Burger House & CFC',
            'cuisine' => 'Fast Food, Burgers, Wings',
            'rating' => 4.7,
            'time' => '25-35 mins',
            'min_order' => 200,
            'img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&q=80',
            'badge' => 'Value Favorite',
            'description' => 'Giant stacked burgers, crunchy chicken wings, seasoned fries, and refreshing cold drinks.',
            'status' => 'approved'
        ],
        [
            'id' => 4,
            'name' => 'Roadhouse Cafe',
            'cuisine' => 'Italian, Pizza, Pasta',
            'rating' => 4.8,
            'time' => '30-40 mins',
            'min_order' => 350,
            'img' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&q=80',
            'badge' => 'Pathao Select',
            'description' => 'Authentic wood-fired pizzas, handcrafted Italian pastas, and gourmet continental dishes.',
            'status' => 'approved'
        ],
        [
            'id' => 5,
            'name' => 'Himalayan Java Coffee',
            'cuisine' => 'Coffee & Bakery, Breakfast, Desserts',
            'rating' => 4.9,
            'time' => '15-25 mins',
            'min_order' => 180,
            'img' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=600&q=80',
            'badge' => 'Express Delivery',
            'description' => 'Handcrafted specialty coffees, fresh artisan muffins, bagels, and delicious pastries.',
            'status' => 'approved'
        ],
        [
            'id' => 6,
            'name' => 'Bota Momo',
            'cuisine' => 'Momos, Nepali, Fast Food',
            'rating' => 4.7,
            'time' => '20-30 mins',
            'min_order' => 150,
            'img' => 'https://images.unsplash.com/photo-1625220194771-7ebdea0b70b9?w=600&q=80',
            'badge' => 'Trending',
            'description' => 'Traditional and fusion momos served in eco-friendly bota with signature spicy chutneys.',
            'status' => 'approved'
        ],
        [
            'id' => 7,
            'name' => 'The Bakery Cafe',
            'cuisine' => 'Bakery, Continental, Momos',
            'rating' => 4.6,
            'time' => '25-35 mins',
            'min_order' => 200,
            'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&q=80',
            'badge' => 'Classic Choice',
            'description' => 'Famous for legendary momos, club sandwiches, sizzling chicken steaks, and fresh cakes.',
            'status' => 'approved'
        ],
        [
            'id' => 8,
            'name' => 'Hankook Sarang',
            'cuisine' => 'Korean & Asian, Ramen, BBQ',
            'rating' => 4.8,
            'time' => '30-45 mins',
            'min_order' => 400,
            'img' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=600&q=80',
            'badge' => 'Premium',
            'description' => 'Authentic Korean BBQ, Yangnyeom crispy chicken, Kimchi stew, and Korean rice cakes.',
            'status' => 'approved'
        ],
        [
            'id' => 9,
            'name' => 'Café ABC',
            'cuisine' => 'American, Beverages',
            'rating' => 4.5,
            'time' => '30-40 mins',
            'min_order' => 200,
            'img' => './images/pizza.png',
            'badge' => 'Verified Partner',
            'description' => 'Fresh burgers, golden fries, chilled coffees, and signature sandwiches.',
            'status' => 'approved'
        ]
    ];
}

if (empty($menu_categories)) {
    $menu_categories = [
        ['id' => 1, 'name' => 'Burgers'],
        ['id' => 2, 'name' => 'Pizza'],
        ['id' => 3, 'name' => 'Momos'],
        ['id' => 4, 'name' => 'Fast Food'],
        ['id' => 5, 'name' => 'Coffee & Bakery'],
        ['id' => 6, 'name' => 'Korean & Asian'],
        ['id' => 7, 'name' => 'Thali & Nepali'],
        ['id' => 8, 'name' => 'Pasta & Italian'],
        ['id' => 9, 'name' => 'Desserts'],
        ['id' => 10, 'name' => 'Drinks'],
    ];
}

if (empty($offers)) {
    $offers = [
        [
            'id' => 1,
            'title' => 'First Order Discount',
            'description' => 'Get 20% off your first QuickBite order across all restaurants.',
            'code' => 'WELCOME20',
            'discount_percent' => 20,
            'min_order' => 0,
            'is_active' => 1
        ],
        [
            'id' => 2,
            'title' => 'Pathao Eats Super Saver',
            'description' => 'Save 25% on popular Kathmandu restaurants including Dalle, KKFC & Java.',
            'code' => 'PATHAOEATS',
            'discount_percent' => 25,
            'min_order' => 400,
            'is_active' => 1
        ],
        [
            'id' => 3,
            'title' => 'Dalle Momo Craze',
            'description' => 'Flat Rs. 50 off on all Dalle momo orders over Rs. 250.',
            'code' => 'DALLE50',
            'discount_percent' => 15,
            'min_order' => 250,
            'is_active' => 1
        ],
        [
            'id' => 4,
            'title' => 'Free Express Delivery',
            'description' => 'Zero delivery fees on all restaurant orders above Rs. 500.',
            'code' => 'FREEFEES',
            'discount_percent' => 0,
            'min_order' => 500,
            'is_active' => 1
        ]
    ];
}

if (empty($menu_items)) {
    $menu_items = [
        // Dalle (id 1)
        [
            'id' => 1,
            'vendor' => 1,
            'vendor_name' => 'Dalle',
            'name' => 'Dalle Buff Steam Momo',
            'desc' => 'Juicy buff momos filled with fresh spices, served with signature spicy Dalle chutney.',
            'price' => 220,
            'was' => 250,
            'discount' => 12,
            'cat' => 'Momos',
            'img' => './images/momo.png',
            'is_active' => 1
        ],
        [
            'id' => 2,
            'vendor' => 1,
            'vendor_name' => 'Dalle',
            'name' => 'Dalle Chicken C-Momo',
            'desc' => 'Crispy fried chicken momos tossed in spicy Dalle chili & capsicum gravy.',
            'price' => 280,
            'was' => 320,
            'discount' => 13,
            'cat' => 'Momos',
            'img' => './images/momo.png',
            'is_active' => 1
        ],
        [
            'id' => 3,
            'vendor' => 1,
            'vendor_name' => 'Dalle',
            'name' => 'Dalle Pork Steam Momo',
            'desc' => 'Classic tender pork momos paired with house peanut & chili dipping sauce.',
            'price' => 260,
            'was' => null,
            'discount' => 0,
            'cat' => 'Momos',
            'img' => './images/momo.png',
            'is_active' => 1
        ],
        [
            'id' => 4,
            'vendor' => 1,
            'vendor_name' => 'Dalle',
            'name' => 'Crispy Dalle Potato Basket',
            'desc' => 'Golden potato wedges dusted with fiery Dalle chili powder.',
            'price' => 180,
            'was' => null,
            'discount' => 0,
            'cat' => 'Fast Food',
            'img' => 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG',
            'is_active' => 1
        ],

        // KKFC (id 2)
        [
            'id' => 5,
            'vendor' => 2,
            'vendor_name' => 'KKFC (Krunchy Fried Chicken)',
            'name' => '8 Pcs Hot & Crispy Bucket',
            'desc' => '8 pieces of signature golden crispy fried chicken with garlic mayo.',
            'price' => 850,
            'was' => 990,
            'discount' => 14,
            'cat' => 'Fast Food',
            'img' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=500&q=80',
            'is_active' => 1
        ],
        [
            'id' => 6,
            'vendor' => 2,
            'vendor_name' => 'KKFC (Krunchy Fried Chicken)',
            'name' => 'KKFC Krunchy Zinger Burger',
            'desc' => 'Extra crispy fried chicken breast with lettuce, cheese, and spicy mayo.',
            'price' => 320,
            'was' => 360,
            'discount' => 11,
            'cat' => 'Burgers',
            'img' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&q=80',
            'is_active' => 1
        ],
        [
            'id' => 7,
            'vendor' => 2,
            'vendor_name' => 'KKFC (Krunchy Fried Chicken)',
            'name' => 'Spicy Wings (6 Pcs)',
            'desc' => 'Tender chicken wings coated in hot krunchy batter.',
            'price' => 290,
            'was' => null,
            'discount' => 0,
            'cat' => 'Fast Food',
            'img' => 'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=500&q=80',
            'is_active' => 1
        ],

        // Burger House (id 3)
        [
            'id' => 8,
            'vendor' => 3,
            'vendor_name' => 'The Burger House & CFC',
            'name' => 'Double Cheese Crunch Burger',
            'desc' => 'Dual beef/chicken patties stacked with melted cheddar & house sauce.',
            'price' => 350,
            'was' => 390,
            'discount' => 10,
            'cat' => 'Burgers',
            'img' => 'https://images.unsplash.com/photo-1599474151439-9f3c4e972009?w=500&q=80',
            'is_active' => 1
        ],
        [
            'id' => 9,
            'vendor' => 3,
            'vendor_name' => 'The Burger House & CFC',
            'name' => 'CFC Spicy Chicken Wrap',
            'desc' => 'Crispy chicken strips, lettuce & chipotle wrap in warm tortilla.',
            'price' => 240,
            'was' => null,
            'discount' => 0,
            'cat' => 'Fast Food',
            'img' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=500&q=80',
            'is_active' => 1
        ],

        // Roadhouse Cafe (id 4)
        [
            'id' => 10,
            'vendor' => 4,
            'vendor_name' => 'Roadhouse Cafe',
            'name' => 'Wood-Fired Smoked Chicken Pizza',
            'desc' => 'Thin crust wood-fired pizza with smoked chicken, mozzarella & fresh basil.',
            'price' => 680,
            'was' => 750,
            'discount' => 9,
            'cat' => 'Pizza',
            'img' => './images/pizza.png',
            'is_active' => 1
        ],
        [
            'id' => 11,
            'vendor' => 4,
            'vendor_name' => 'Roadhouse Cafe',
            'name' => 'Creamy Fettuccine Alfredo',
            'desc' => 'Handmade pasta in rich garlic cream sauce with wild mushrooms.',
            'price' => 490,
            'was' => 550,
            'discount' => 11,
            'cat' => 'Pasta & Italian',
            'img' => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281729?w=500&q=80',
            'is_active' => 1
        ],

        // Himalayan Java (id 5)
        [
            'id' => 12,
            'vendor' => 5,
            'vendor_name' => 'Himalayan Java Coffee',
            'name' => 'Iced Caramel Latte',
            'desc' => 'Espresso shot blended with chilled milk and rich caramel syrup.',
            'price' => 260,
            'was' => null,
            'discount' => 0,
            'cat' => 'Coffee & Bakery',
            'img' => 'https://images.unsplash.com/photo-1676506739319-70bff65bfc48?w=500&q=80',
            'is_active' => 1
        ],
        [
            'id' => 13,
            'vendor' => 5,
            'vendor_name' => 'Himalayan Java Coffee',
            'name' => 'Fresh Blueberry Muffin',
            'desc' => 'Soft oven-baked muffin loaded with wild blueberries.',
            'price' => 160,
            'was' => 180,
            'discount' => 11,
            'cat' => 'Coffee & Bakery',
            'img' => './images/dessert.png',
            'is_active' => 1
        ],

        // Bota Momo (id 6)
        [
            'id' => 14,
            'vendor' => 6,
            'vendor_name' => 'Bota Momo',
            'name' => 'Bota Open Buff Momo',
            'desc' => 'Unique open-topped momos stuffed with spiced buff and fiery gravy.',
            'price' => 210,
            'was' => null,
            'discount' => 0,
            'cat' => 'Momos',
            'img' => './images/momo.png',
            'is_active' => 1
        ],

        // Bakery Cafe (id 7)
        [
            'id' => 15,
            'vendor' => 7,
            'vendor_name' => 'The Bakery Cafe',
            'name' => 'Classic Steam Chicken Momo',
            'desc' => 'The legendary Kathmandu chicken momo with rich spiced yellow achar.',
            'price' => 230,
            'was' => null,
            'discount' => 0,
            'cat' => 'Momos',
            'img' => './images/momo.png',
            'is_active' => 1
        ],

        // Hankook Sarang (id 8)
        [
            'id' => 16,
            'vendor' => 8,
            'vendor_name' => 'Hankook Sarang',
            'name' => 'Yangnyeom Korean Fried Chicken',
            'desc' => 'Sweet & spicy sticky glazed Korean fried chicken with sesame seeds.',
            'price' => 650,
            'was' => 720,
            'discount' => 10,
            'cat' => 'Korean & Asian',
            'img' => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=500&q=80',
            'is_active' => 1
        ],

        // Café ABC (id 9)
        [
            'id' => 17,
            'vendor' => 9,
            'vendor_name' => 'Café ABC',
            'name' => 'Cheese Burger',
            'desc' => 'Juicy grilled patty with cheese, fresh veggies and house sauce.',
            'price' => 270,
            'was' => 300,
            'discount' => 10,
            'cat' => 'Burgers',
            'img' => './images/pizza.png',
            'is_active' => 1
        ],
        [
            'id' => 18,
            'vendor' => 9,
            'vendor_name' => 'Café ABC',
            'name' => 'French Fries',
            'desc' => 'Crispy golden fries with special seasoning.',
            'price' => 150,
            'was' => null,
            'discount' => 0,
            'cat' => 'Fast Food',
            'img' => 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG',
            'is_active' => 1
        ]
    ];
}

// -------------------------------------------------------------
// Vendor and Admin scope variables for vendor/admin dashboards
// -------------------------------------------------------------
$currentVendorId = 1;
if (is_logged_in() && !empty($_SESSION['user']['vendor_id'])) {
    $currentVendorId = (int)$_SESSION['user']['vendor_id'];
}

$vendor_orders = [];
$vendor_menu_items = [];
$vendor_discounts = [];
$admin_vendors = [];
$admin_approvals = [];
$admin_activity = [];
$admin_users = [];

if ($db !== null) {
    try {
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

        // Vendor discounts
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

        // Admin vendors
        $avStmt = $db->query("
            SELECT v.id, v.name, u.email, v.status, 
                   DATE_FORMAT(v.created_at, '%d %b %Y') as joined,
                   v.cuisine, v.rating
            FROM vendors v
            JOIN users u ON v.user_id = u.id
            ORDER BY v.id DESC
        ");
        $admin_vendors = $avStmt->fetchAll();

        // Admin approvals
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
    } catch (Exception $ex) {
        // Ignore dashboard queries if DB offline
    }
}

if (empty($admin_vendors) && !empty($vendors)) {
    foreach ($vendors as $v) {
        $admin_vendors[] = [
            'id' => $v['id'],
            'name' => $v['name'],
            'email' => strtolower(preg_replace('/[^a-z0-9]/i', '', $v['name'])) . '@quickbite.test',
            'status' => $v['status'] ?? 'approved',
            'joined' => '01 Oct 2026',
            'cuisine' => $v['cuisine'],
            'rating' => $v['rating']
        ];
    }
}

/**
 * HTML status pill badge
 */
function status_pill(string $status): string
{
    $status = strtolower($status);
    $label = ucfirst($status);
    return "<span class=\"status-pill status-pill--{$status}\">{$label}</span>";
}

