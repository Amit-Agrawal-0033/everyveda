<?php
/**
 * Script to generate high quality SVG graphics for EveryVeda products, categories, banners, and logos
 */

declare(strict_types=1);

$imgDir = dirname(__DIR__) . '/public/assets/images';
@mkdir($imgDir . '/products', 0755, true);
@mkdir($imgDir . '/categories', 0755, true);
@mkdir($imgDir . '/banners', 0755, true);

// 1. Logo SVG
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
  <text x="62" y="34" font-family="Inter, Segoe UI, sans-serif" font-size="24" font-weight="800" fill="#214D36" letter-spacing="-0.5">Every<tspan fill="#C6A45C">Veda</tspan></text>
  <text x="63" y="48" font-family="Inter, Segoe UI, sans-serif" font-size="7.5" font-weight="700" fill="#477A52" letter-spacing="1.5">PURE AYURVEDIC LIVING</text>
</svg>';
file_put_contents($imgDir . '/logo.svg', $logoSvg);

// 2. Placeholder SVG
$placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
  <rect width="400" height="400" fill="#FAF7EF"/>
  <circle cx="200" cy="200" r="90" fill="#e8e2d1"/>
  <path d="M 200 130 C 240 150 250 210 200 270 C 150 210 160 150 200 130 Z" fill="#214D36" opacity="0.8"/>
  <text x="200" y="320" font-family="Inter, sans-serif" font-size="16" font-weight="700" fill="#214D36" text-anchor="middle">EveryVeda Botanical</text>
</svg>';
file_put_contents($imgDir . '/products/placeholder.svg', $placeholderSvg);
file_put_contents($imgDir . '/placeholder.svg', $placeholderSvg);

// Helper for product jar/bottle SVGs
function makeProductSvg(string $title, string $color, string $type = 'jar'): string {
    $gradId = 'g_' . md5($title);
    $accentColor = ($color === '#214D36') ? '#C6A45C' : '#214D36';

    $bodyPath = ($type === 'jar')
        ? '<rect x="130" y="140" width="140" height="180" rx="16" fill="url(#' . $gradId . ')"/>
           <rect x="145" y="115" width="110" height="25" rx="6" fill="#C6A45C"/>
           <rect x="140" y="180" width="120" height="100" rx="6" fill="#FAF7EF" stroke="#C6A45C" stroke-width="1.5"/>'
        : '<path d="M 170 120 L 230 120 L 230 150 L 255 180 L 255 320 L 145 320 L 145 180 L 170 150 Z" fill="url(#' . $gradId . ')"/>
           <rect x="175" y="95" width="50" height="25" rx="4" fill="#C6A45C"/>
           <rect x="155" y="195" width="90" height="95" rx="4" fill="#FAF7EF" stroke="#C6A45C" stroke-width="1.5"/>';

    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
      <defs>
        <linearGradient id="' . $gradId . '" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="' . $color . '"/>
          <stop offset="100%" stop-color="#142e20"/>
        </linearGradient>
      </defs>
      <rect width="400" height="400" fill="#FAF7EF"/>
      <ellipse cx="200" cy="340" rx="90" ry="16" fill="#e0dacb"/>
      ' . $bodyPath . '
      <text x="200" y="215" font-family="Inter, sans-serif" font-size="10" font-weight="800" fill="#214D36" text-anchor="middle" letter-spacing="1">EVERYVEDA</text>
      <text x="200" y="235" font-family="Inter, sans-serif" font-size="9" font-weight="700" fill="#26332B" text-anchor="middle">' . htmlspecialchars($title) . '</text>
      <circle cx="200" cy="255" r="8" fill="#C6A45C" opacity="0.3"/>
      <text x="200" y="258" font-family="sans-serif" font-size="9" font-weight="bold" fill="#214D36" text-anchor="middle">🌿</text>
      <text x="200" y="272" font-family="Inter, sans-serif" font-size="7" font-weight="600" fill="#477A52" text-anchor="middle">100% PURE HERBAL</text>
    </svg>';
}

// 3. Products
$products = [
    'product-chyawanprash.svg' => ['Royal Gold Chyawanprash', '#214D36', 'jar'],
    'product-shilajit.svg' => ['Himalayan Shilajit Resin', '#1b1b1b', 'jar'],
    'product-amla-juice.svg' => ['Organic Wild Amla Juice', '#477A52', 'bottle'],
    'product-pain-oil.svg' => ['Maha Narayan Joint Oil', '#9A7B39', 'bottle'],
    'product-hair-oil.svg' => ['Bhringraj Brahmi Hair Oil', '#214D36', 'bottle'],
    'product-triphala.svg' => ['Triphala Churna Cleanse', '#8B5A2B', 'jar'],
    'product-ashwagandha.svg' => ['Ashwagandha Vitality Caps', '#3D5A45', 'jar'],
    'product-kumkumadi.svg' => ['Kumkumadi Radiance Tailam', '#C6A45C', 'bottle']
];

foreach ($products as $fn => $info) {
    file_put_contents($imgDir . '/products/' . $fn, makeProductSvg($info[0], $info[1], $info[2]));
}

// 4. Categories
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

// 5. Banners
function makeBannerSvg(string $headline, string $sub): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 450" width="1200" height="450">
      <defs>
        <linearGradient id="bgG" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#163524"/>
          <stop offset="50%" stop-color="#214D36"/>
          <stop offset="100%" stop-color="#366345"/>
        </linearGradient>
      </defs>
      <rect width="1200" height="450" fill="url(#bgG)"/>
      <circle cx="1050" cy="225" r="280" fill="#477A52" opacity="0.25"/>
      <circle cx="1100" cy="225" r="180" fill="#C6A45C" opacity="0.15"/>
      <!-- Leaves Decor -->
      <path d="M 980 120 C 1040 160 1060 260 990 340 C 940 280 920 200 980 120 Z" fill="#C6A45C" opacity="0.3"/>
      <text x="80" y="150" font-family="Inter, sans-serif" font-size="16" font-weight="700" fill="#C6A45C" letter-spacing="2">AUTHENTIC AYURVEDIC WELLNESS</text>
      <text x="80" y="215" font-family="Inter, sans-serif" font-size="38" font-weight="800" fill="#FAF7EF">' . htmlspecialchars($headline) . '</text>
      <text x="80" y="270" font-family="Inter, sans-serif" font-size="18" fill="#e2ddcf">' . htmlspecialchars($sub) . '</text>
      <rect x="80" y="320" width="190" height="50" rx="25" fill="#C6A45C"/>
      <text x="175" y="352" font-family="Inter, sans-serif" font-size="16" font-weight="700" fill="#214D36" text-anchor="middle">SHOP NOW →</text>
    </svg>';
}

$banners = [
    'banner-hero-1.svg' => ['Pure Ayurvedic Living Delivered', 'Handcrafted classical formulations with zero chemical additives.'],
    'banner-hero-2.svg' => ['Himalayan Gold Grade-A Shilajit', 'Purified with classical Triphala Shodhana. 80%+ Fulvic Acid.'],
    'banner-hero-3.svg' => ['Royal Gold Chyawanprash For Immunity', 'Enriched with fresh forest Amla, pure Saffron & Desi Cow Ghee.']
];

foreach ($banners as $fn => $info) {
    file_put_contents($imgDir . '/banners/' . $fn, makeBannerSvg($info[0], $info[1]));
}

// 6. Blog Images
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

echo "All SVG visual assets generated successfully!\n";
