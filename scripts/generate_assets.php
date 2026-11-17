<?php
/**
 * Script to generate high quality SVG graphics for EveryVeda products, categories, banners, and logos
 * Version 2.0: Clean white-background product photography packshots, multi-angle secondary views,
 * and high-end botanical hero banners with ZERO baked-in text to prevent slider overlap.
 */

declare(strict_types=1);

$imgDir = dirname(__DIR__) . '/public/assets/images';
@mkdir($imgDir . '/products', 0755, true);
@mkdir($imgDir . '/categories', 0755, true);
@mkdir($imgDir . '/banners', 0755, true);

// 1. Brand Logo SVG
$logoSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 60" width="240" height="60">
  <defs>
    <linearGradient id="leafGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#477A52"/>
      <stop offset="100%" stop-color="#214D36"/>
    </linearGradient>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#C6A45C"/>
      <stop offset="100%" stop-color="#9A7B39"/>
    </linearGradient>
  </defs>
  <!-- Leaf Emblem -->
  <circle cx="28" cy="30" r="24" fill="url(#leafGrad)"/>
  <path d="M 28 12 C 38 18 42 32 30 46 C 24 38 20 28 28 12 Z" fill="#FAF7EF" opacity="0.9"/>
  <path d="M 28 12 C 18 18 14 32 26 46 C 32 38 36 28 28 12 Z" fill="url(#goldGrad)" opacity="0.85"/>
  <circle cx="28" cy="28" r="4" fill="#FAF7EF"/>
  <!-- Typography -->
  <text x="62" y="34" font-family="Inter, -apple-system, Segoe UI, sans-serif" font-size="24" font-weight="800" fill="#214D36" letter-spacing="-0.5">Every<tspan fill="#C6A45C">Veda</tspan></text>
  <text x="63" y="48" font-family="Inter, -apple-system, Segoe UI, sans-serif" font-size="7.5" font-weight="700" fill="#477A52" letter-spacing="1.5">PURE AYURVEDIC LIVING</text>
</svg>';
file_put_contents($imgDir . '/logo.svg', $logoSvg);

// 2. Placeholder SVG (Clean studio look)
$placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">
  <rect width="500" height="500" fill="#ffffff"/>
  <ellipse cx="250" cy="430" rx="140" ry="20" fill="#e8ebe8" opacity="0.7"/>
  <circle cx="250" cy="240" r="100" fill="#f4f7f4" stroke="#d5dfd7" stroke-width="2"/>
  <path d="M 250 160 C 290 180 300 250 250 310 C 200 250 210 180 250 160 Z" fill="#214D36" opacity="0.85"/>
  <circle cx="250" cy="235" r="8" fill="#C6A45C"/>
  <text x="250" y="380" font-family="Inter, sans-serif" font-size="16" font-weight="700" fill="#214D36" text-anchor="middle">EveryVeda Botanical</text>
  <text x="250" y="405" font-family="Inter, sans-serif" font-size="12" font-weight="500" fill="#6c757d" text-anchor="middle">Authentic Classical Formulation</text>
</svg>';
file_put_contents($imgDir . '/products/placeholder.svg', $placeholderSvg);
file_put_contents($imgDir . '/placeholder.svg', $placeholderSvg);

// 3. Helper for Premium Product Photography Packshots (Pure White Background #FFFFFF)
function makePremiumProductFrontSvg(
    string $title,
    string $hindiTitle,
    string $netQty,
    string $color,
    string $accent,
    string $type = 'jar',
    string $iconEmoji = '🌿'
): string {
    $gradId = 'grad_' . md5($title);
    $goldGrad = 'gold_' . md5($title);
    $lidGrad = 'lid_' . md5($title);

    // Realistic multi-tier shadow
    $shadow = '
      <ellipse cx="250" cy="435" rx="150" ry="18" fill="#d8dfd9" opacity="0.5"/>
      <ellipse cx="250" cy="433" rx="110" ry="12" fill="#b9c5bc" opacity="0.6"/>
      <ellipse cx="250" cy="430" rx="80" ry="8" fill="#8f9e93" opacity="0.4"/>
    ';

    if ($type === 'jar') {
        // Luxury Amber / Emerald Cosmetic & Rasayana Jar
        $container = '
          <!-- Jar Body -->
          <rect x="155" y="175" width="190" height="235" rx="24" fill="url(#' . $gradId . ')"/>
          <!-- Glass Specular Highlight -->
          <path d="M 170 185 L 185 185 L 185 395 L 170 395 Z" fill="#ffffff" opacity="0.12"/>
          <path d="M 195 185 L 200 185 L 200 395 L 195 395 Z" fill="#ffffff" opacity="0.06"/>
          <!-- Neck & Metallic Lid -->
          <rect x="180" y="152" width="140" height="26" rx="6" fill="url(#' . $lidGrad . ')"/>
          <rect x="170" y="125" width="160" height="32" rx="10" fill="url(#' . $goldGrad . ')"/>
          <line x1="172" y1="138" x2="328" y2="138" stroke="#ffffff" stroke-width="1.5" opacity="0.5"/>
          <line x1="172" y1="145" x2="328" y2="145" stroke="#9A7B39" stroke-width="1"/>
          <!-- Product Label -->
          <rect x="170" y="215" width="160" height="165" rx="12" fill="#FAF7EF" stroke="#C6A45C" stroke-width="2"/>
          <rect x="176" y="221" width="148" height="153" rx="8" fill="none" stroke="#214D36" stroke-width="0.8" stroke-dasharray="3,2"/>
          <!-- Label Brand -->
          <text x="250" y="246" font-family="Inter, sans-serif" font-size="11" font-weight="900" fill="#214D36" text-anchor="middle" letter-spacing="2">EVERYVEDA</text>
          <text x="250" y="258" font-family="Inter, sans-serif" font-size="7" font-weight="700" fill="#C6A45C" text-anchor="middle" letter-spacing="1.5">CLASSICAL AYURVEDA</text>
          <line x1="200" y1="264" x2="300" y2="264" stroke="#C6A45C" stroke-width="1"/>
          <!-- Title & Net Qty -->
          <text x="250" y="286" font-family="Inter, sans-serif" font-size="13" font-weight="800" fill="#26332B" text-anchor="middle">' . htmlspecialchars($title) . '</text>
          <text x="250" y="304" font-family="Noto Sans Devanagari, sans-serif" font-size="11" font-weight="700" fill="#477A52" text-anchor="middle">' . htmlspecialchars($hindiTitle) . '</text>
          <circle cx="250" cy="330" r="14" fill="#C6A45C" opacity="0.15"/>
          <text x="250" y="336" font-size="15" text-anchor="middle">' . $iconEmoji . '</text>
          <!-- Badge -->
          <rect x="205" y="352" width="90" height="18" rx="9" fill="#214D36"/>
          <text x="250" y="364" font-family="Inter, sans-serif" font-size="8.5" font-weight="800" fill="#FAF7EF" text-anchor="middle">NET WT. ' . htmlspecialchars($netQty) . '</text>
        ';
    } elseif ($type === 'bottle') {
        // Luxury Oil & Elixir Dropper / Flip-Cap Bottle
        $container = '
          <!-- Bottle Body -->
          <path d="M 215 130 L 285 130 L 285 160 L 320 205 L 320 410 C 320 418 314 425 305 425 L 195 425 C 186 425 180 418 180 410 L 180 205 L 215 160 Z" fill="url(#' . $gradId . ')"/>
          <!-- Glass Specular Highlight -->
          <path d="M 195 210 L 205 210 L 205 410 L 195 410 Z" fill="#ffffff" opacity="0.15"/>
          <!-- Cap / Dropper -->
          <rect x="220" y="100" width="60" height="32" rx="6" fill="url(#' . $goldGrad . ')"/>
          <rect x="235" y="70" width="30" height="32" rx="4" fill="#1e2d24"/>
          <!-- Label -->
          <rect x="195" y="235" width="110" height="160" rx="8" fill="#FAF7EF" stroke="#C6A45C" stroke-width="1.8"/>
          <rect x="200" y="240" width="100" height="150" rx="6" fill="none" stroke="#214D36" stroke-width="0.7" stroke-dasharray="2,2"/>
          <text x="250" y="260" font-family="Inter, sans-serif" font-size="9" font-weight="900" fill="#214D36" text-anchor="middle" letter-spacing="1.5">EVERYVEDA</text>
          <text x="250" y="271" font-family="Inter, sans-serif" font-size="6.5" font-weight="700" fill="#C6A45C" text-anchor="middle">SHASTRA FORMULA</text>
          <line x1="215" y1="276" x2="285" y2="276" stroke="#C6A45C" stroke-width="0.8"/>
          <text x="250" y="295" font-family="Inter, sans-serif" font-size="11" font-weight="800" fill="#26332B" text-anchor="middle">' . htmlspecialchars($title) . '</text>
          <text x="250" y="312" font-family="Noto Sans Devanagari, sans-serif" font-size="9.5" font-weight="700" fill="#477A52" text-anchor="middle">' . htmlspecialchars($hindiTitle) . '</text>
          <circle cx="250" cy="336" r="13" fill="#C6A45C" opacity="0.18"/>
          <text x="250" y="342" font-size="14" text-anchor="middle">' . $iconEmoji . '</text>
          <rect x="215" y="358" width="70" height="16" rx="8" fill="#214D36"/>
          <text x="250" y="369" font-family="Inter, sans-serif" font-size="8" font-weight="800" fill="#FAF7EF" text-anchor="middle">' . htmlspecialchars($netQty) . '</text>
        ';
    } else {
        // Juice & Health Drink Recyclable Bottle
        $container = '
          <rect x="180" y="150" width="140" height="265" rx="20" fill="url(#' . $gradId . ')"/>
          <path d="M 205 105 L 295 105 L 295 150 L 205 150 Z" fill="#2b4733"/>
          <rect x="215" y="80" width="70" height="28" rx="6" fill="url(#' . $goldGrad . ')"/>
          <rect x="195" y="190" width="110" height="185" rx="10" fill="#FAF7EF" stroke="#C6A45C" stroke-width="1.8"/>
          <text x="250" y="215" font-family="Inter, sans-serif" font-size="9.5" font-weight="900" fill="#214D36" text-anchor="middle" letter-spacing="1.5">EVERYVEDA</text>
          <text x="250" y="227" font-family="Inter, sans-serif" font-size="6.5" font-weight="700" fill="#C6A45C" text-anchor="middle">FOREST HARVEST</text>
          <line x1="215" y1="233" x2="285" y2="233" stroke="#C6A45C" stroke-width="0.8"/>
          <text x="250" y="255" font-family="Inter, sans-serif" font-size="11.5" font-weight="800" fill="#26332B" text-anchor="middle">' . htmlspecialchars($title) . '</text>
          <text x="250" y="273" font-family="Noto Sans Devanagari, sans-serif" font-size="10" font-weight="700" fill="#477A52" text-anchor="middle">' . htmlspecialchars($hindiTitle) . '</text>
          <circle cx="250" cy="302" r="16" fill="#477A52" opacity="0.15"/>
          <text x="250" y="309" font-size="18" text-anchor="middle">' . $iconEmoji . '</text>
          <text x="250" y="333" font-family="Inter, sans-serif" font-size="7.5" font-weight="700" fill="#214D36" text-anchor="middle">100% PURE JUICE</text>
          <rect x="215" y="344" width="70" height="17" rx="8" fill="#214D36"/>
          <text x="250" y="356" font-family="Inter, sans-serif" font-size="8.5" font-weight="800" fill="#FAF7EF" text-anchor="middle">' . htmlspecialchars($netQty) . '</text>
        ';
    }

    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">
      <defs>
        <linearGradient id="' . $gradId . '" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="' . $color . '"/>
          <stop offset="60%" stop-color="' . $accent . '"/>
          <stop offset="100%" stop-color="#122419"/>
        </linearGradient>
        <linearGradient id="' . $goldGrad . '" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#9A7B39"/>
          <stop offset="35%" stop-color="#E5CA85"/>
          <stop offset="70%" stop-color="#C6A45C"/>
          <stop offset="100%" stop-color="#806226"/>
        </linearGradient>
        <linearGradient id="' . $lidGrad . '" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="#1c2b20"/>
          <stop offset="50%" stop-color="#2d4233"/>
          <stop offset="100%" stop-color="#16221a"/>
        </linearGradient>
      </defs>
      <!-- Pure Crisp White E-Commerce Studio Background -->
      <rect width="500" height="500" fill="#ffffff"/>
      ' . $shadow . '
      ' . $container . '
    </svg>';
}

// 4. Helper for Secondary Angle Packshot (Back of pack: Facts, Directions, License, Batch)
function makeSecondaryAngleSvg(string $title, string $hsn, string $dosage, string $mfgInfo): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" width="500" height="500">
      <rect width="500" height="500" fill="#ffffff"/>
      <ellipse cx="250" cy="435" rx="140" ry="16" fill="#d8dfd9" opacity="0.6"/>
      <rect x="160" y="150" width="180" height="260" rx="18" fill="#1e3426"/>
      <!-- Label Canvas -->
      <rect x="175" y="165" width="150" height="230" rx="8" fill="#FAF7EF" stroke="#C6A45C" stroke-width="1.5"/>
      <text x="250" y="185" font-family="Inter, sans-serif" font-size="9" font-weight="800" fill="#214D36" text-anchor="middle">PRODUCT SPECIFICATIONS</text>
      <line x1="185" y1="192" x2="315" y2="192" stroke="#C6A45C" stroke-width="1"/>
      
      <!-- Supplement / Shastra Table -->
      <text x="185" y="210" font-family="Inter, sans-serif" font-size="8" font-weight="700" fill="#26332B">Formulation:</text>
      <text x="315" y="210" font-family="Inter, sans-serif" font-size="8" fill="#477A52" text-anchor="end">Classical Ayush</text>
      
      <text x="185" y="226" font-family="Inter, sans-serif" font-size="8" font-weight="700" fill="#26332B">HSN Code:</text>
      <text x="315" y="226" font-family="Inter, sans-serif" font-size="8" fill="#214D36" text-anchor="end">' . htmlspecialchars($hsn) . '</text>
      
      <text x="185" y="242" font-family="Inter, sans-serif" font-size="8" font-weight="700" fill="#26332B">Daily Usage:</text>
      <text x="185" y="258" font-family="Inter, sans-serif" font-size="7.5" fill="#555">' . htmlspecialchars($dosage) . '</text>
      
      <line x1="185" y1="272" x2="315" y2="272" stroke="#e0d7c3" stroke-width="1"/>
      
      <text x="185" y="288" font-family="Inter, sans-serif" font-size="8" font-weight="700" fill="#214D36">Quality & Safety Standards:</text>
      <text x="185" y="302" font-family="Inter, sans-serif" font-size="7" fill="#477A52">✓ 100% Heavy-Metal Tested (NABL)</text>
      <text x="185" y="314" font-family="Inter, sans-serif" font-size="7" fill="#477A52">✓ Zero Chemical Additives & Parabens</text>
      <text x="185" y="326" font-family="Inter, sans-serif" font-size="7" fill="#477A52">✓ GMP Certified Clean Facility</text>
      
      <line x1="185" y1="336" x2="315" y2="336" stroke="#e0d7c3" stroke-width="1"/>
      
      <text x="185" y="350" font-family="Inter, sans-serif" font-size="7.5" font-weight="700" fill="#26332B">Mfg Lic No: AYU-UP-2024-884</text>
      <text x="185" y="362" font-family="Inter, sans-serif" font-size="7" fill="#666">' . htmlspecialchars($mfgInfo) . '</text>
      
      <!-- Barcode simulation -->
      <rect x="205" y="372" width="90" height="16" fill="#26332B"/>
      <line x1="210" y1="372" x2="210" y2="388" stroke="#FAF7EF" stroke-width="2"/>
      <line x1="216" y1="372" x2="216" y2="388" stroke="#FAF7EF" stroke-width="1"/>
      <line x1="222" y1="372" x2="222" y2="388" stroke="#FAF7EF" stroke-width="3"/>
      <line x1="230" y1="372" x2="230" y2="388" stroke="#FAF7EF" stroke-width="1.5"/>
      <line x1="238" y1="372" x2="238" y2="388" stroke="#FAF7EF" stroke-width="2"/>
      <line x1="245" y1="372" x2="245" y2="388" stroke="#FAF7EF" stroke-width="1"/>
      <line x1="254" y1="372" x2="254" y2="388" stroke="#FAF7EF" stroke-width="2.5"/>
      <line x1="262" y1="372" x2="262" y2="388" stroke="#FAF7EF" stroke-width="1"/>
      <line x1="270" y1="372" x2="270" y2="388" stroke="#FAF7EF" stroke-width="2"/>
      <line x1="280" y1="372" x2="280" y2="388" stroke="#FAF7EF" stroke-width="1.5"/>
      <line x1="288" y1="372" x2="288" y2="388" stroke="#FAF7EF" stroke-width="2"/>
    </svg>';
}

// 5. Generate Products Primary and Secondary Visuals
$catalog = [
    'product-chyawanprash.svg' => [
        'title' => 'Chyawanprash',
        'hindi' => 'शाही च्यवनप्राश',
        'qty' => '500g',
        'color' => '#1e3827',
        'accent' => '#325d3f',
        'type' => 'jar',
        'emoji' => '🍯',
        'hsn' => '30049011',
        'dosage' => '1-2 tbsp twice daily with warm milk',
        'mfg' => 'Marketed by EveryVeda Organics Pvt Ltd'
    ],
    'product-shilajit.svg' => [
        'title' => 'Himalayan Shilajit',
        'hindi' => 'शुद्ध हिमालयी शिलाजीत',
        'qty' => '20g',
        'color' => '#181b18',
        'accent' => '#2e332e',
        'type' => 'jar',
        'emoji' => '🏔️',
        'hsn' => '30049011',
        'dosage' => '300-500mg dissolved in warm liquid',
        'mfg' => 'Purified via Triphala Shodhana'
    ],
    'product-amla-juice.svg' => [
        'title' => 'Forest Amla Juice',
        'hindi' => 'जंगली आंवला रस',
        'qty' => '1000ml',
        'color' => '#2b5435',
        'accent' => '#477A52',
        'type' => 'juice',
        'emoji' => '🌿',
        'hsn' => '20098990',
        'dosage' => '30ml with equal lukewarm water on empty stomach',
        'mfg' => 'Wild-Harvested Central Indian Flora'
    ],
    'product-pain-oil.svg' => [
        'title' => 'Maha Narayan Oil',
        'hindi' => 'महानारायण तैल',
        'qty' => '200ml',
        'color' => '#6e511e',
        'accent' => '#9A7B39',
        'type' => 'bottle',
        'emoji' => '💆‍♂️',
        'hsn' => '30049011',
        'dosage' => 'Gentle massage over joints for 10-15 mins',
        'mfg' => 'Classical Sahasrayogam formulation'
    ],
    'product-hair-oil.svg' => [
        'title' => 'Bhringraj Hair Oil',
        'hindi' => 'भृंगराज केश तैल',
        'qty' => '200ml',
        'color' => '#183321',
        'accent' => '#2d5438',
        'type' => 'bottle',
        'emoji' => '✨',
        'hsn' => '33059010',
        'dosage' => 'Apply to scalp 2 hours prior to bath',
        'mfg' => 'Simmered in pure virgin coconut oil'
    ],
    'product-triphala.svg' => [
        'title' => 'Triphala Churna',
        'hindi' => 'त्रिफला सूक्ष्म चूर्ण',
        'qty' => '250g',
        'color' => '#4a3319',
        'accent' => '#7a572c',
        'type' => 'jar',
        'emoji' => '🍵',
        'hsn' => '12119029',
        'dosage' => '1 tsp (3-5g) with lukewarm water at bedtime',
        'mfg' => 'Equal 1:1:1 Amalaki, Haritaki, Bibhitaki'
    ],
    'product-ashwagandha.svg' => [
        'title' => 'Ashwagandha Caps',
        'hindi' => 'अश्वगंधा कैप्सूल',
        'qty' => '60 Caps',
        'color' => '#273d2d',
        'accent' => '#42614a',
        'type' => 'jar',
        'emoji' => '🌱',
        'hsn' => '30049011',
        'dosage' => '1-2 capsules daily post meals',
        'mfg' => 'Standardized Withanolides extract'
    ],
    'product-kumkumadi.svg' => [
        'title' => 'Kumkumadi Tailam',
        'hindi' => 'शुद्ध कुमकुमादि तैलम',
        'qty' => '30ml',
        'color' => '#876924',
        'accent' => '#C6A45C',
        'type' => 'bottle',
        'emoji' => '🌸',
        'hsn' => '33049990',
        'dosage' => '3-4 drops gently smoothed onto clean face at night',
        'mfg' => 'Pure Kashmiri Mogra Saffron base'
    ]
];

foreach ($catalog as $fn => $data) {
    // Primary front shot
    $frontSvg = makePremiumProductFrontSvg(
        $data['title'],
        $data['hindi'],
        $data['qty'],
        $data['color'],
        $data['accent'],
        $data['type'],
        $data['emoji']
    );
    file_put_contents($imgDir . '/products/' . $fn, $frontSvg);

    // Secondary back info shot
    $backFn = str_replace('.svg', '-back.svg', $fn);
    $backSvg = makeSecondaryAngleSvg($data['title'], $data['hsn'], $data['dosage'], $data['mfg']);
    file_put_contents($imgDir . '/products/' . $backFn, $backSvg);
}

// 6. Category Icons
function makeCategorySvg(string $emoji, string $bg): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
      <circle cx="50" cy="50" r="46" fill="' . $bg . '" stroke="#C6A45C" stroke-width="2"/>
      <text x="50" y="62" font-size="38" text-anchor="middle">' . $emoji . '</text>
    </svg>';
}

$cats = [
    'cat-chyawanprash.svg' => ['🍯', '#FAF7EF'],
    'cat-shilajit.svg' => ['🏔️', '#FAF7EF'],
    'cat-juices.svg' => ['🌱', '#FAF7EF'],
    'cat-jointpain.svg' => ['💆‍♂️', '#FAF7EF'],
    'cat-hairskin.svg' => ['✨', '#FAF7EF'],
    'cat-digestive.svg' => ['🍵', '#FAF7EF']
];

foreach ($cats as $fn => $info) {
    file_put_contents($imgDir . '/categories/' . $fn, makeCategorySvg($info[0], $info[1]));
}

// 7. Hero Banners — CRITICAL: ZERO BAKED-IN TEXT TO PREVENT SLIDER OVERLAP!
// Only rich, ambient botanical artwork, Himalayan peaks, and golden aura gradients.
$heroBanners = [
    'banner-hero-1.svg' => '
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 520" width="1400" height="520">
        <defs>
          <radialGradient id="sunGlow" cx="75%" cy="35%" r="60%">
            <stop offset="0%" stop-color="#C6A45C" stop-opacity="0.35"/>
            <stop offset="50%" stop-color="#3d6b49" stop-opacity="0.15"/>
            <stop offset="100%" stop-color="#142E20" stop-opacity="0"/>
          </radialGradient>
          <linearGradient id="forestBase" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#0f2418"/>
            <stop offset="50%" stop-color="#1a3d2b"/>
            <stop offset="100%" stop-color="#244d37"/>
          </linearGradient>
        </defs>
        <rect width="1400" height="520" fill="url(#forestBase)"/>
        <rect width="1400" height="520" fill="url(#sunGlow)"/>
        <!-- Sacred Geometry / Mandala Motif -->
        <circle cx="1150" cy="260" r="220" fill="none" stroke="#C6A45C" stroke-width="1.5" opacity="0.25"/>
        <circle cx="1150" cy="260" r="160" fill="none" stroke="#C6A45C" stroke-width="1.2" stroke-dasharray="6,4" opacity="0.2"/>
        <circle cx="1150" cy="260" r="100" fill="none" stroke="#C6A45C" stroke-width="1" opacity="0.3"/>
        <!-- Botanical Leaf Silhouettes -->
        <path d="M 1050 480 C 1120 380 1200 320 1340 300 C 1260 400 1180 460 1050 480 Z" fill="#C6A45C" opacity="0.12"/>
        <path d="M 1150 140 C 1220 200 1280 260 1380 320 C 1320 220 1240 160 1150 140 Z" fill="#477A52" opacity="0.25"/>
        <path d="M 980 360 C 1040 310 1120 280 1220 300 C 1140 350 1060 380 980 360 Z" fill="#C6A45C" opacity="0.18"/>
        <!-- Soft glowing dust particles -->
        <circle cx="950" cy="180" r="3" fill="#FAF7EF" opacity="0.4"/>
        <circle cx="1080" cy="120" r="4" fill="#C6A45C" opacity="0.5"/>
        <circle cx="1220" cy="200" r="2.5" fill="#FAF7EF" opacity="0.35"/>
        <circle cx="1300" cy="150" r="3" fill="#C6A45C" opacity="0.4"/>
        <circle cx="1020" cy="400" r="2" fill="#FAF7EF" opacity="0.3"/>
      </svg>',

    'banner-hero-2.svg' => '
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 520" width="1400" height="520">
        <defs>
          <linearGradient id="shilajitSky" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#141816"/>
            <stop offset="45%" stop-color="#1d2621"/>
            <stop offset="100%" stop-color="#121814"/>
          </linearGradient>
          <linearGradient id="peakGlow" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#C6A45C" stop-opacity="0.35"/>
            <stop offset="100%" stop-color="#E5CA85" stop-opacity="0.05"/>
          </linearGradient>
        </defs>
        <rect width="1400" height="520" fill="url(#shilajitSky)"/>
        <!-- Golden Dawn Glow on Mountain Horizon -->
        <ellipse cx="1100" cy="220" rx="360" ry="180" fill="url(#peakGlow)"/>
        <!-- Himalayan Mountain Silhouettes -->
        <polygon points="750,520 950,260 1150,520" fill="#18241d" opacity="0.7"/>
        <polygon points="900,520 1120,180 1350,520" fill="#223328" opacity="0.85"/>
        <polygon points="1080,520 1260,240 1400,480 1400,520" fill="#1b2a21" opacity="0.75"/>
        <!-- Snowcap Highlights -->
        <polygon points="1120,180 1085,230 1155,230" fill="#FAF7EF" opacity="0.3"/>
        <polygon points="950,260 925,295 975,295" fill="#FAF7EF" opacity="0.25"/>
        <polygon points="1260,240 1238,275 1282,275" fill="#FAF7EF" opacity="0.2"/>
        <!-- Mineral Gold Vein Aura -->
        <path d="M 850 480 Q 1020 360 1220 440 T 1400 380" fill="none" stroke="#C6A45C" stroke-width="2" opacity="0.4" stroke-dasharray="8,6"/>
      </svg>',

    'banner-hero-3.svg' => '
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 520" width="1400" height="520">
        <defs>
          <radialGradient id="amberGlow" cx="70%" cy="40%" r="55%">
            <stop offset="0%" stop-color="#D9A84E" stop-opacity="0.38"/>
            <stop offset="60%" stop-color="#2d5238" stop-opacity="0.12"/>
            <stop offset="100%" stop-color="#142e20" stop-opacity="0"/>
          </radialGradient>
          <linearGradient id="chyawanBg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#152f21"/>
            <stop offset="50%" stop-color="#1f422e"/>
            <stop offset="100%" stop-color="#2b593f"/>
          </linearGradient>
        </defs>
        <rect width="1400" height="520" fill="url(#chyawanBg)"/>
        <rect width="1400" height="520" fill="url(#amberGlow)"/>
        <!-- Traditional Ayurvedic Herbs & Gold Flakes Artwork -->
        <circle cx="1120" cy="250" r="190" fill="none" stroke="#C6A45C" stroke-width="1.8" opacity="0.3"/>
        <circle cx="1120" cy="250" r="130" fill="#C6A45C" opacity="0.08"/>
        <!-- Stylized Amla Fruit & Leaves -->
        <circle cx="1120" cy="250" r="55" fill="#477A52" opacity="0.65"/>
        <circle cx="1120" cy="250" r="45" fill="#60946b" opacity="0.5"/>
        <path d="M 1120 195 C 1145 170 1195 185 1190 220 C 1150 215 1130 205 1120 195 Z" fill="#C6A45C" opacity="0.5"/>
        <path d="M 1070 280 C 1020 300 1010 340 1040 370 C 1070 330 1075 300 1070 280 Z" fill="#C6A45C" opacity="0.4"/>
        <!-- Saffron Thread Spirals -->
        <path d="M 1220 180 Q 1260 210 1240 260 T 1280 320" fill="none" stroke="#D97724" stroke-width="2.5" opacity="0.6"/>
        <path d="M 1040 180 Q 1000 230 1030 280" fill="none" stroke="#D97724" stroke-width="2" opacity="0.5"/>
      </svg>'
];

foreach ($heroBanners as $fn => $svgContent) {
    file_put_contents($imgDir . '/banners/' . $fn, trim($svgContent));
}

// 8. Blog Graphics
function makeBlogSvg(string $title): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 350" width="600" height="350">
      <rect width="600" height="350" fill="#FAF7EF"/>
      <rect x="20" y="20" width="560" height="310" rx="8" fill="#214D36" opacity="0.06"/>
      <circle cx="300" cy="150" r="60" fill="#477A52" opacity="0.15"/>
      <text x="300" y="165" font-size="45" text-anchor="middle">🌿</text>
      <text x="300" y="250" font-family="Inter, sans-serif" font-size="18" font-weight="700" fill="#214D36" text-anchor="middle">' . htmlspecialchars($title) . '</text>
      <text x="300" y="275" font-family="Inter, sans-serif" font-size="12" font-weight="600" fill="#C6A45C" text-anchor="middle">EVERYVEDA WELLNESS JOURNAL</text>
    </svg>';
}

$blogs = [
    'blog-triphala.svg' => 'Daily Triphala for Gut Balance',
    'blog-shilajit.svg' => 'The Science of Himalayan Shilajit',
    'blog-hairoil.svg' => 'Shiro Abhyanga Hair Care Rituals'
];

foreach ($blogs as $fn => $title) {
    file_put_contents($imgDir . '/' . $fn, makeBlogSvg($title));
}

echo "All SVG visual assets generated successfully (clean white products, back shots, text-free banners)!\n";
