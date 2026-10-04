<?php
/**
 * Shared "fake data" layer.
 *
 * Everything in this file is a plain PHP array. When the real backend is
 * ready, each array below should be replaced with a query against the
 * database (e.g. $vendors = $db->query('SELECT * FROM vendors')->fetchAll())
 * — the pages that consume these arrays don't need to change, since they
 * just loop over whatever comes back.
 */

$photo = [
    'burger' => 'https://images.unsplash.com/photo-1599474151439-9f3c4e972009?w=400&q=80&auto=format&fit=crop',
    'momo'   => './images/momo.png',
    'pizza'  => './images/pizza.png',
    'fries'  => 'https://upload.wikimedia.org/wikipedia/commons/8/83/French_Fries.JPG',
    'coffee' => 'https://images.unsplash.com/photo-1676506739319-70bff65bfc48?w=400&q=80&auto=format&fit=crop',
];

$vendors = [
    ['id' => 'cafe-abc',      'name' => 'Café ABC',      'cuisine' => 'American, Beverages', 'rating' => 4.5, 'time' => '30-40 mins', 'min_order' => 200, 'img' => $photo['burger']],
    ['id' => 'momo-house',    'name' => 'Momo House',    'cuisine' => 'Momos, Snacks',        'rating' => 4.2, 'time' => '20-30 mins', 'min_order' => 150, 'img' => $photo['momo']],
    ['id' => 'pizza-corner',  'name' => 'Pizza Corner',  'cuisine' => 'Pizza, Italian',       'rating' => 4.3, 'time' => '30-40 mins', 'min_order' => 300, 'img' => $photo['pizza']],
    ['id' => 'bite-fry',      'name' => 'Bite & Fry',    'cuisine' => 'Fast Food, Snacks',    'rating' => 4.1, 'time' => '25-30 mins', 'min_order' => 150, 'img' => $photo['fries']],
    ['id' => 'drink-station', 'name' => 'Drink Station', 'cuisine' => 'Drinks, Juices',       'rating' => 4.0, 'time' => '15-25 mins', 'min_order' => 100, 'img' => $photo['coffee']],
];

$menu_items = [
    ['id' => 'cheese-burger', 'vendor' => 'cafe-abc', 'vendor_name' => 'Café ABC', 'name' => 'Cheese Burger', 'desc' => 'Juicy grilled patty with cheese, fresh veggies and house sauce.', 'price' => 270, 'was' => 300, 'discount' => 10, 'cat' => 'Burgers', 'img' => $photo['burger']],
    ['id' => 'veg-burger',    'vendor' => 'cafe-abc', 'vendor_name' => 'Café ABC', 'name' => 'Veg Burger',    'desc' => 'Crispy veg patty with lettuce and mayo.',                        'price' => 220, 'was' => null, 'discount' => 0,  'cat' => 'Burgers', 'img' => $photo['burger']],
    ['id' => 'french-fries',  'vendor' => 'cafe-abc', 'vendor_name' => 'Café ABC', 'name' => 'French Fries',  'desc' => 'Crispy golden fries with special seasoning.',                    'price' => 150, 'was' => null, 'discount' => 0,  'cat' => 'Burgers', 'img' => $photo['fries']],
    ['id' => 'cold-coffee',   'vendor' => 'cafe-abc', 'vendor_name' => 'Café ABC', 'name' => 'Cold Coffee',   'desc' => 'Chilled coffee with ice cream.',                                 'price' => 180, 'was' => null, 'discount' => 0,  'cat' => 'Drinks',  'img' => $photo['coffee']],
];

$vendor_orders = [
    ['id' => '#ORD-1012', 'customer' => 'Sagar K.',  'items' => 2, 'amount' => 540, 'status' => 'pending'],
    ['id' => '#ORD-1011', 'customer' => 'Anisha R.', 'items' => 3, 'amount' => 820, 'status' => 'preparing'],
    ['id' => '#ORD-1010', 'customer' => 'Rohan M.',  'items' => 1, 'amount' => 270, 'status' => 'completed'],
    ['id' => '#ORD-1009', 'customer' => 'Pooja S.',  'items' => 2, 'amount' => 620, 'status' => 'preparing'],
];

$vendor_menu_items = [
    ['name' => 'Cheese Burger',  'price' => 270, 'discount' => '10%', 'status' => 'active'],
    ['name' => 'Veg Burger',     'price' => 220, 'discount' => '—',   'status' => 'active'],
    ['name' => 'Chicken Pasta',  'price' => 350, 'discount' => '5%',  'status' => 'active'],
    ['name' => 'French Fries',   'price' => 150, 'discount' => '—',   'status' => 'active'],
    ['name' => 'Cold Coffee',    'price' => 180, 'discount' => '—',   'status' => 'inactive'],
];

$vendor_discounts = [
    ['name' => 'Cheese Burger', 'original' => 300, 'pct' => '10%', 'net' => 270],
    ['name' => 'Veg Pizza',     'original' => 500, 'pct' => '10%', 'net' => 450],
    ['name' => 'Chicken Momo',  'original' => 250, 'pct' => '0%',  'net' => 250],
    ['name' => 'Cold Coffee',   'original' => 180, 'pct' => '5%',  'net' => 171],
];

$admin_vendors = [
    ['name' => 'Café ABC',     'email' => 'cafeabc@mail.com',     'status' => 'approved', 'joined' => '01 Jul 2026'],
    ['name' => 'Momo House',   'email' => 'momohouse@mail.com',   'status' => 'approved', 'joined' => '28 Jun 2026'],
    ['name' => 'Pizza Corner', 'email' => 'pizzacorner@mail.com', 'status' => 'pending',  'joined' => '26 Jun 2026'],
    ['name' => 'Bite & Fry',   'email' => 'bitefry@mail.com',     'status' => 'approved', 'joined' => '20 Jun 2026'],
];

$admin_approvals = [
    ['vendor' => 'Tasty Bites', 'email' => 'tastybites@mail.com', 'shop' => 'Tasty Bites', 'joined' => '08 Jul 2026'],
    ['vendor' => 'Foodie Hub',  'email' => 'foodiehub@mail.com',  'shop' => 'Foodie Hub',  'joined' => '05 Jul 2026'],
    ['vendor' => 'Good Eats',   'email' => 'goodeats@mail.com',   'shop' => 'Good Eats',   'joined' => '05 Jul 2026'],
];

$admin_activity = [
    ['text' => 'New vendor "Tasty Bites" registered.',       'time' => '4 mins ago'],
    ['text' => 'Order #ORD-1012 placed.',                    'time' => '5 mins ago'],
    ['text' => 'Vendor "Café ABC" updated their menu.',       'time' => '20 mins ago'],
    ['text' => 'New user "Pratik K." signed up.',             'time' => '32 mins ago'],
];

$admin_users = [
    ['name' => 'Sagar Karki',  'email' => 'sagar.k@mail.com',  'orders' => 14, 'joined' => '12 Mar 2026'],
    ['name' => 'Anisha Rai',   'email' => 'anisha.r@mail.com', 'orders' => 9,  'joined' => '02 Apr 2026'],
    ['name' => 'Rohan Malla',  'email' => 'rohan.m@mail.com',  'orders' => 21, 'joined' => '18 Jan 2026'],
];

/** Small helper used on several admin/vendor tables. */
function status_pill(string $status): string
{
    $label = ucfirst($status);
    return "<span class=\"status-pill status-pill--{$status}\">{$label}</span>";
}
