<?php
/**
 * Vision Exim — Complete Database Setup
 * Run this once: http://localhost/vision_exim/admin/setup_db.php
 * Creates the database and all required tables, then seeds harvest, categories, products, inquiries, and users.
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

// Connect without selecting a database
$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

echo "<pre style='font-family:monospace;padding:30px;background:#1e293b;color:#e2e8f0;border-radius:16px;max-width:800px;margin:40px auto;box-shadow:0 10px 30px rgba(0,0,0,0.25);'>";
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║          Vision Exim — Database Setup Tool           ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// 1. Create database
$conn->query("CREATE DATABASE IF NOT EXISTS `vision_exim` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "✅  Database 'vision_exim' created/verified.\n";

$conn->select_db('vision_exim');

// 2. Create admin_users table
$sql_users = "CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_users);
echo "✅  Table 'admin_users' created/verified.\n";

// 3. Create categories table
$sql_categories = "CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `parent_id` INT DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_categories);
echo "✅  Table 'categories' created/verified.\n";

// 4. Create products table
$sql_products = "CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `category_id` INT NOT NULL,
    `hs_code` VARCHAR(20) DEFAULT NULL,
    `short_description` TEXT DEFAULT NULL,
    `full_description` TEXT DEFAULT NULL,
    `moq` VARCHAR(50) DEFAULT NULL,
    `packaging` VARCHAR(100) DEFAULT NULL,
    `quality_standard` VARCHAR(100) DEFAULT NULL,
    `origin_state` VARCHAR(100) DEFAULT NULL,
    `origin_country` VARCHAR(100) DEFAULT 'India',
    `image` VARCHAR(255) DEFAULT NULL,
    `gallery_images` TEXT DEFAULT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'draft',
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `seo_title` VARCHAR(255) DEFAULT NULL,
    `seo_description` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_products);
echo "✅  Table 'products' created/verified.\n";

// 5. Create inquiries table
$sql_inquiries = "CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_name` VARCHAR(150) NOT NULL,
    `contact_name` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `country_flag` VARCHAR(10) DEFAULT NULL,
    `country_name` VARCHAR(100) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `requested_product` VARCHAR(150) DEFAULT NULL,
    `quantity` VARCHAR(50) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `source` VARCHAR(30) NOT NULL DEFAULT 'website',
    `status` VARCHAR(30) NOT NULL DEFAULT 'new',
    `internal_notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_inquiries);
echo "✅  Table 'inquiries' created/verified.\n";

// 6. Create harvest_calendar table
$sql_harvest = "CREATE TABLE IF NOT EXISTS `harvest_calendar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `spice_name` VARCHAR(100) NOT NULL,
    `jan` TINYINT(1) NOT NULL DEFAULT 0,
    `feb` TINYINT(1) NOT NULL DEFAULT 0,
    `mar` TINYINT(1) NOT NULL DEFAULT 0,
    `apr` TINYINT(1) NOT NULL DEFAULT 0,
    `may` TINYINT(1) NOT NULL DEFAULT 0,
    `jun` TINYINT(1) NOT NULL DEFAULT 0,
    `jul` TINYINT(1) NOT NULL DEFAULT 0,
    `aug` TINYINT(1) NOT NULL DEFAULT 0,
    `sep` TINYINT(1) NOT NULL DEFAULT 0,
    `oct` TINYINT(1) NOT NULL DEFAULT 0,
    `nov` TINYINT(1) NOT NULL DEFAULT 0,
    `dec_month` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_spice` (`spice_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_harvest);
echo "✅  Table 'harvest_calendar' created/verified.\n";

// 7. Seed Admin User
$res = $conn->query("SELECT COUNT(*) as cnt FROM admin_users");
$row = $res->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    $name = 'Vision Exim Admin';
    $email = 'admin@visionexim.com';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admin_users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $password);
    $stmt->execute();
    $stmt->close();
    echo "👥  Seeded default admin user: <b>admin@visionexim.com</b> (password: admin123)\n";
}

// 8. Seed Categories
$res = $conn->query("SELECT COUNT(*) as cnt FROM categories");
$row = $res->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    $categories = [
        ['Whole Spices', 'whole-spices', 'Premium dried whole spices direct from organic fields.', 'active', 1],
        ['Ground Spices', 'ground-spices', 'Fine, pure ground spices processed under strict hygienic conditions.', 'active', 2],
        ['Blended Spices', 'blended-spices', 'Curated spice blends for traditional international dishes.', 'active', 3],
        ['Dehydrated Spices', 'dehydrated-spices', 'Dehydrated vegetables and herb flakes with maximum flavor retention.', 'active', 4],
        ['Spice Seeds', 'spice-seeds', 'High-purity spice seeds cleaned and graded to export standards.', 'active', 5]
    ];
    $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, status, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->bind_param('ssssi', $cat[0], $cat[1], $cat[2], $cat[3], $cat[4]);
        $stmt->execute();
    }
    $stmt->close();
    echo "🌿  Seeded " . count($categories) . " spice categories.\n";
}

// Get category IDs for product seeding
$cat_ids = [];
$res = $conn->query("SELECT id, name FROM categories");
while ($r = $res->fetch_assoc()) {
    $cat_ids[$r['name']] = $r['id'];
}

// 9. Seed Products
$res = $conn->query("SELECT COUNT(*) as cnt FROM products");
$row = $res->fetch_assoc();
if ((int)$row['cnt'] === 0 && !empty($cat_ids)) {
    $products = [
        [
            'Green Cardamom', 'green-cardamom', $cat_ids['Whole Spices'], '0908.1100',
            'Premium 8mm bold green cardamom pods sourced from the high ranges of Idukki, Kerala.',
            'Our export-grade Green Cardamom is meticulously handpicked and graded. Known globally for its rich aroma, deep green color, and high oil content. Ideal for culinary, medicinal, and flavoring applications worldwide.',
            '500 KG', '25 KG double-layered PP bags / Jute bags', '8mm Bold, Grade A, Organic', 'Kerala', 'India', 'active', 1, 1,
            'Premium Green Cardamom Exporters India | Vision Exim', 'Buy organic 8mm bold green cardamom pods directly from Kerala, India. Premium quality packaging, fast shipping.'
        ],
        [
            'Black Pepper Bold', 'black-pepper-bold', $cat_ids['Whole Spices'], '0904.1100',
            'Malabar Bold black pepper with high piperine content, standard moisture levels.',
            'Sourced directly from certified farmers in the Western Ghats, our Malabar Bold black pepper is cleaned, sorted, and graded to perfection. Guaranteed free of mold, high density, and strong spicy aroma.',
            '1 Ton', '25 KG multi-wall paper bags / Jute bags', 'Malabar Grade 1, 550g/l density', 'Kerala', 'India', 'active', 2, 1,
            'Malabar Bold Black Pepper Export Quality | Vision Exim', 'High piperine export quality black pepper whole from Malabar coast India. Safe packaging, best wholesale prices.'
        ],
        [
            'Turmeric Powder', 'turmeric-powder', $cat_ids['Ground Spices'], '0910.3030',
            'Salem Turmeric Powder with a guaranteed curcumin content of 4.5% or above.',
            'Made from handpicked Salem turmeric fingers, our powder features a bright golden-yellow color and standard purity. Processed in an ISO certified facility with low moisture and absolutely zero artificial colorants.',
            '500 KG', '25 KG HDPE bags with inner liner', '4.5%+ Curcumin, Ultra-fine mesh', 'Tamil Nadu', 'India', 'active', 3, 0,
            'Salem Turmeric Powder Exporters India | Vision Exim', 'Premium Salem golden turmeric powder with high curcumin content. Free of artificial colors, export certified.'
        ],
        [
            'Red Chili Powder', 'red-chili-powder', $cat_ids['Ground Spices'], '0904.2200',
            'Stemless Guntur red chili powder featuring intense heat and rich red coloring.',
            'Produced using selected Guntur Sannam and Teja chilis. We clean and grind under temperature-controlled environments to retain natural pungency, capsaicin levels, and vibrant natural color.',
            '1 Ton', '25 KG Kraft paper bags with polythene liner', 'Stemless Guntur Sannam, 30000+ SHU', 'Andhra Pradesh', 'India', 'active', 4, 0,
            'High Pungency Guntur Red Chili Powder | Vision Exim', 'Export hot red chili powder from Teja and Sannam dry chilis. Tested for aflatoxin, custom mesh sizes.'
        ],
        [
            'Cumin Seeds', 'cumin-seeds', $cat_ids['Spice Seeds'], '0909.3120',
            'Machine-cleaned premium cumin seeds (Jeera) from Gujarat, 99% purity level.',
            'Sourced from the fertile Saurashtra peninsula, Gujarat. Cleaned using modern sortex machinery to ensure high purity and low moisture. Rich in volatile oil, offering warm earthy flavor.',
            '2 Tons', '25 KG / 50 KG PP bags', 'Singapore Quality 99% Purity', 'Gujarat', 'India', 'active', 5, 0,
            'Premium Indian Cumin Seeds (Jeera) Wholesale | Vision Exim', 'Machine cleaned Sortex premium cumin seeds with high volatile oil. Premium packaging, bulk global exports.'
        ],
        [
            'Fenugreek Seeds', 'fenugreek-seeds', $cat_ids['Spice Seeds'], '0910.9912',
            'Golden-amber fenugreek seeds cleaned, graded, and packed to preserve freshness.',
            'Highly nutritional and aromatic fenugreek seeds sourced from Rajasthan. Thoroughly cleaned and graded to remove foreign matter, ensuring top-tier export purity.',
            '800 KG', '25 KG PP bags', 'Grade A, 99.5% Purity', 'Rajasthan', 'India', 'active', 6, 0,
            'Organic Fenugreek Seeds Export India | Vision Exim', 'Aromatic golden-amber fenugreek seeds from Rajasthan, India. Cleaned to 99.5% purity, high culinary grade.'
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO products (name, slug, category_id, hs_code, short_description, full_description, moq, packaging, quality_standard, origin_state, origin_country, status, sort_order, is_featured, seo_title, seo_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->bind_param('ssisssssssssiiss',
            $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10], $p[11], $p[12], $p[13], $p[14], $p[15]
        );
        $stmt->execute();
    }
    $stmt->close();
    echo "📦  Seeded " . count($products) . " premium spice products.\n";
}

// 10. Seed Inquiries
$res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries");
$row = $res->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    $inquiries = [
        [
            'Ahmed Trading Co.', 'Ahmed Al-Mansoori', 'ahmed@trading.ae', '+971 50 123 4567', '🇦🇪', 'UAE', 'Deira, Dubai',
            'Turmeric Powder', '500 KG',
            'Dear Vision Exim Team, we are interested in importing organic Salem Turmeric Powder with minimum 4.5% curcumin content. Please quote your best FOB Mundra Port price for a 500 KG trial shipment. Also, let us know about the packaging options available. Thank you.',
            'website', 'new', 'Buyer wants to pay 30% advance and 70% against BL scan. Curcumin content report must be shared before container loading.'
        ],
        [
            'Global Foods Inc.', 'Johnathan Smith', 'procurement@globalfoods.com', '+1 212 987 6543', '🇺🇸', 'USA', 'New York',
            'Red Chili Powder', '1 Ton',
            'We require 1 Ton of high heat Guntur Red Chili Powder (Teja quality, stemless) for spice blending in the US. Please provide CIF New York port pricing and specify if certificate of analysis is provided with each batch.',
            'whatsapp', 'progress', 'Requested Teja chili specs. Looking to close pricing next week.'
        ],
        [
            'Spice Paradise LLC', 'Elena Rostova', 'info@spiceparadise.co.uk', '+44 20 7946 0958', '🇬🇧', 'UK', 'London',
            'Cumin Seeds', '2 Tons',
            'Hello, could you please quote machine cleaned cumin seeds Singapore Quality 99% purity. We need 2 Tons trial shipment shipped to London Gateway Port. Please share your product catalog too.',
            'email', 'replied', 'Quotation sent via email. Volatile oil specification shared.'
        ],
        [
            'Middle East Spices FZE', 'Mohammed bin Rashid', 'purchase@mespices.ae', '+971 6 544 3322', '🇦🇪', 'UAE', 'Sharjah',
            'Fenugreek Seeds', '800 KG',
            'Looking for fresh harvest premium Rajasthan Fenugreek Seeds. Trial order of 800 KG needed for UAE market. Quote FOB Mumbai.',
            'website', 'new', 'Requires grade A certificate.'
        ],
        [
            'EuroSpice Import', 'Hans Schmidt', 'contact@eurospice.de', '+49 89 2345 6789', '🇩🇪', 'Germany', 'Hamburg',
            'Black Pepper Whole', '5 Tons',
            'Dear sales, please send quotation for Malabar bold black pepper 550g/l density. Trial of 5 Tons CIF Hamburg. We need standard organic certifications.',
            'email', 'closed', 'Lost deal due to competitor providing cheaper logistics options.'
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO inquiries (company_name, contact_name, email, phone, country_flag, country_name, city, requested_product, quantity, message, source, status, internal_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($inquiries as $inq) {
        $stmt->bind_param('sssssssssssss',
            $inq[0], $inq[1], $inq[2], $inq[3], $inq[4], $inq[5], $inq[6], $inq[7], $inq[8], $inq[9], $inq[10], $inq[11], $inq[12]
        );
        $stmt->execute();
    }
    $stmt->close();
    echo "📨  Seeded " . count($inquiries) . " export inquiries.\n";
}

// 11. Seed harvest_calendar data (if empty)
$res = $conn->query("SELECT COUNT(*) as cnt FROM harvest_calendar");
$row = $res->fetch_assoc();
if ((int)$row['cnt'] === 0) {
    $seeds = [
        ['Black Pepper',  1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1,  1],
        ['Cardamom',      1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1,  2],
        ['Chillies',      1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1,  3],
        ['Coriander',     0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,  4],
        ['Cumin',         0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,  5],
        ['Fennel',        0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,  6],
        ['Fenugreek',     0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,  7],
        ['Turmeric',      1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,  8],
        ['Mustard',       0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0,  9],
        ['Garlic',        0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 10],
        ['Ginger',        1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 11],
    ];

    $stmt = $conn->prepare("INSERT INTO harvest_calendar (spice_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec_month, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($seeds as $s) {
        $stmt->bind_param('siiiiiiiiiiiii', $s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $s[11], $s[12], $s[13]);
        $stmt->execute();
    }
    $stmt->close();
    echo "📅  Seeded " . count($seeds) . " spice harvest calendar records.\n";
} else {
    echo "ℹ️  Harvest calendar already contains records.\n";
}

echo "\n── Database Setup Complete! ──────────────────────────────────\n";
echo "🌿 Live database created with comprehensive mock data.\n";
echo "🔑 admin login: <b>admin@visionexim.com</b> (password: <b>admin123</b>).\n";
echo "🔗 <a href='/vision_exim/admin/' style='color:#3D9B53;font-weight:bold;text-decoration:underline;'>Go to Admin Login →</a>\n";
echo "</pre>";

$conn->close();
