<?php
/**
 * Integrate Real EveryVeda Product Imagery, Official Logo, and Real Product Catalog
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$imgDir = dirname(__DIR__) . '/public/assets/images';
$prodDir = $imgDir . '/products';

echo "1. Processing Real Images...\n";

// Map raw image files to clean filenames
$imageMap = [
    '1 (13).png' => $imgDir . '/logo.png', // Official Brand Logo
    '1 (1).png'  => $prodDir . '/height-rise.png',
    '1 (2).png'  => $prodDir . '/veerya-plus.png',
    '1 (3).png'  => $prodDir . '/poshan-veda.png',
    '1 (4).png'  => $prodDir . '/slim-core.png',
    '1 (5).png'  => $prodDir . '/liva-shield.png',
    '1 (6).png'  => $prodDir . '/urja-max.png',
    '1 (7).png'  => $prodDir . '/flexi-relief.png',
    '1 (8).png'  => $prodDir . '/thyro-care.png',
    '1 (9).png'  => $prodDir . '/pile-care.png',
    '1 (10).png' => $prodDir . '/bust-veda.png',
    '1 (11).png' => $prodDir . '/root-revive.png',
    '1 (12).png' => $prodDir . '/fem-balance.png',
    '1 (14).png' => $prodDir . '/prime-man.png',
];

foreach ($imageMap as $srcName => $destPath) {
    $srcPath = $prodDir . '/' . $srcName;
    if (file_exists($srcPath)) {
        copy($srcPath, $destPath);
        echo "  [OK] Copied {$srcName} -> " . basename($destPath) . "\n";
    } else {
        echo "  [WARN] Source file not found: {$srcPath}\n";
    }
}

echo "\n2. Updating Official Company Settings...\n";
$companySettings = [
    'store_name' => 'EveryVeda',
    'company_name' => 'EVERYVEDA WELLNESS PVT. LTD.',
    'support_phone' => '9580251422',
    'support_email' => 'care@everyveda.com',
    'store_address' => 'B-32, Sector-63, Noida, Uttar Pradesh - 201301, India',
    'website_url' => 'https://www.everyveda.com'
];

foreach ($companySettings as $k => $v) {
    Database::query(
        "INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)",
        ['k' => $k, 'v' => $v]
    );
}

echo "\n3. Ensuring Product Categories...\n";
$categories = [
    [
        'slug' => 'mens-vitality-health',
        'name_en' => "Men's Health & Vitality",
        'name_hi' => 'पुरुष स्वास्थ्य एवं जीवन शक्ति',
        'desc_en' => "Classical Ayurvedic formulations for men's stamina, strength, and vitality.",
        'desc_hi' => 'पुरुषों के पौरुष बल, शारीरिक क्षमता और ऊर्जा के लिए प्रमाणित आयुर्वेदिक योग।',
        'sort_order' => 1
    ],
    [
        'slug' => 'growth-fitness',
        'name_en' => 'Growth & Body Fitness',
        'name_hi' => 'शारीरिक विकास एवं पोषण',
        'desc_en' => 'Ayurvedic nutrition, bone density, and natural weight management.',
        'desc_hi' => 'हड्डियों की मजबूती, शारीरिक लंबाई और संतुलित पोषण के प्राकृतिक सूत्र।',
        'sort_order' => 2
    ],
    [
        'slug' => 'womens-wellness',
        'name_en' => "Women's Wellness & Care",
        'name_hi' => 'स्त्री स्वास्थ्य एवं संतुलन',
        'desc_en' => 'Herbal formulations for hormonal harmony, feminine vitality, and wellness.',
        'desc_hi' => 'महिलाओं के हार्मोनल संतुलन और आंतरिक स्वास्थ्य के लिए शास्त्रीय औषधियां।',
        'sort_order' => 3
    ],
    [
        'slug' => 'organ-digestive-care',
        'name_en' => 'Organ & Digestive Care',
        'name_hi' => 'अंग एवं उदर स्वास्थ्य',
        'desc_en' => 'Liver detoxification, digestive balance, piles relief, and thyroid care.',
        'desc_hi' => 'लिवर सुरक्षा, पाचन संतुलन, बवासीर निवारण और थायरॉयड स्वास्थ्य।',
        'sort_order' => 4
    ],
    [
        'slug' => 'pain-hair-care',
        'name_en' => 'Pain Relief & Hair Therapy',
        'name_hi' => 'दर्द निवारण एवं केश देखभाल',
        'desc_en' => 'Joint mobility, muscle recovery, and follicle hair revitalizers.',
        'desc_hi' => 'मांसपेशियों के दर्द से आराम और घने, मजबूत बालों के लिए आयुर्वेदिक देखभाल।',
        'sort_order' => 5
    ]
];

$catIds = [];
foreach ($categories as $cat) {
    $existing = Database::fetchOne("SELECT id FROM categories WHERE slug = :s", ['s' => $cat['slug']]);
    if ($existing) {
        $catIds[$cat['slug']] = (int)$existing['id'];
    } else {
        $newId = Database::insert('categories', [
            'slug' => $cat['slug'],
            'name_en' => $cat['name_en'],
            'name_hi' => $cat['name_hi'],
            'description_en' => $cat['desc_en'],
            'description_hi' => $cat['desc_hi'],
            'sort_order' => $cat['sort_order'],
            'is_featured' => 1,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $catIds[$cat['slug']] = (int)$newId;
    }
}

echo "\n4. Adding / Updating Real EveryVeda Products...\n";

$realProducts = [
    [
        'sku' => 'EV-HTR-060',
        'slug' => 'everyveda-height-rise-tablets',
        'name_en' => 'EveryVeda HeightRise™ Tablets (60 Tablets)',
        'name_hi' => 'एवरीवेदा हाइटराइज™ आयुर्वेदिक ग्रोथ फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'growth-fitness',
        'price' => 899.00,
        'sale_price' => 699.00,
        'stock' => 150,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'height-rise.png',
        'short_en' => 'Ayurvedic Growth & Bone Strength Formula with Ashwagandha, Shatavari, Hadjod, and Shankh Bhasma.',
        'short_hi' => 'अश्वगंधा, शतावरी, हड़जोड़ और शंख भस्म से युक्त हड्डियों की मजबूती और प्राकृतिक शारीरिक विकास का आयुर्वेदिक फॉर्मूला।',
        'desc_en' => '<p>EveryVeda HeightRise™ is formulated to reach your potential naturally. It supports healthy growth, enhances bone density, improves nutritional absorption, and provides comprehensive physical vitality.</p>',
        'desc_hi' => '<p>एवरीवेदा हाइटराइज™ शरीर के स्वाभाविक विकास को सहारा देता है। यह हड्डियों के घनत्व (bone density) को मजबूत करता है और प्राकृतिक ऊर्जा का संचार करता है।</p>',
        'ingredients_en' => 'Ashwagandha, Shatavari, Hadjod (Cissus quadrangularis), Amla, Shankh Bhasma, Giloy.',
        'ingredients_hi' => 'अश्वगंधा, शतावरी, हड़जोड़, आंवला, शंख भस्म, गिलोय।',
        'directions_en' => 'Take 1 to 2 tablets twice daily with warm milk or water, or as directed by an Ayurvedic physician.',
        'directions_hi' => 'प्रतिदिन 1 से 2 टैबलेट सुबह और शाम गुनगुने दूध या पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-VP-060',
        'slug' => 'everyveda-veerya-plus-tablets',
        'name_en' => 'EveryVeda VeeryaPlus™ (60 Tablets)',
        'name_hi' => 'एवरीवेदा वीर्यप्लस™ मेन्स वाइटेलिटी फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'mens-vitality-health',
        'price' => 1199.00,
        'sale_price' => 899.00,
        'stock' => 120,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'veerya-plus.png',
        'short_en' => "Ayurvedic Men's Vitality & Self-Control Formula. Master your mind and empower your life.",
        'short_hi' => 'पुरुषों के मानसिक फोकस, आत्म-नियंत्रण, शक्ति और ऊर्जा के लिए शुद्ध आयुर्वेदिक फॉर्मूला।',
        'desc_en' => "<p>EveryVeda VeeryaPlus™ is crafted for the modern man seeking equilibrium, mental clarity, and physical strength. Enhances focus, supports discipline, boosts stamina, and promotes overall confidence.</p>",
        'desc_hi' => '<p>एवरीवेदा वीर्यप्लस™ मानसिक एकाग्रता, आंतरिक अनुशासन और पौरुष बल को सुदृढ़ करने के लिए शुद्ध जड़ी-बूटियों से तैयार किया गया है।</p>',
        'ingredients_en' => 'Ashwagandha, Brahmi, Shankhpushpi, Jatamansi, Safed Musli, Pure Shilajit.',
        'ingredients_hi' => 'अश्वगंधा, ब्राह्मी, शंखपुष्पी, जटामांसी, सफेद मूसली, शुद्ध शिलाजीत।',
        'directions_en' => '1 tablet twice daily post meals with warm milk or lukewarm water.',
        'directions_hi' => '1 टैबलेट सुबह और शाम भोजन के बाद गुनगुने दूध के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-PV-060',
        'slug' => 'everyveda-poshan-veda-tablets',
        'name_en' => 'EveryVeda PoshanVeda Weight & Strength (60 Tablets)',
        'name_hi' => 'एवरीवेदा पोषणवेदा वजन एवं शक्ति फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'growth-fitness',
        'price' => 999.00,
        'sale_price' => 749.00,
        'stock' => 140,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'poshan-veda.png',
        'short_en' => 'Complete Nutrition for Healthy Weight Gain, Muscle Building, Appetite & Stamina.',
        'short_hi' => 'प्राकृतिक और स्वस्थ वजन बढ़ाने, भूख सुधारने और मांसपेशियों की मजबूती के लिए उत्तम पोषण।',
        'desc_en' => '<p>PoshanVeda provides complete Ayurvedic nourishment to build lean strength, boost natural appetite, improve digestion, and support healthy, steady weight gain naturally.</p>',
        'desc_hi' => '<p>पोषणवेदा शरीर की पाचन अग्नि को सुधारकर भोजन के पोषक तत्वों को अवशोषित करने में मदद करता है और प्राकृतिक वजन बढ़ाता है।</p>',
        'ingredients_en' => 'Amla, Ashwagandha, Pure Milk Protein Extracts, Shatavari, Vidarikand.',
        'ingredients_hi' => 'आंवला, अश्वगंधा, मिल्क प्रोटीन, शतावरी, विदारीकंद।',
        'directions_en' => 'Take 1 to 2 tablets twice a day with banana shake or warm milk.',
        'directions_hi' => '1 से 2 गोली प्रतिदिन दो बार दूध या केले के शेक के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-SC-060',
        'slug' => 'everyveda-slim-core-tablets',
        'name_en' => 'EveryVeda SlimCore Belly Fat Burner (60 Tablets)',
        'name_hi' => 'एवरीवेदा स्लिमकोर बेली फैट बर्नर (60 टैबलेट)',
        'cat_slug' => 'growth-fitness',
        'price' => 1099.00,
        'sale_price' => 799.00,
        'stock' => 110,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'slim-core.png',
        'short_en' => 'Ayurvedic Belly Fat Burner. Supports Healthy Weight Management, Metabolism & Natural Fat Burn.',
        'short_hi' => 'पेट की जिद्दी चर्बी घटाने, मेटाबॉलिज्म तेज करने और प्राकृतिक वजन नियंत्रण का आयुर्वेदिक योग।',
        'desc_en' => '<p>EveryVeda SlimCore combines Garcinia, Green Tea, Ginger, and Guggulu to boost metabolic fire, encourage lipid breakdown, and promote a leaner, more toned waistline.</p>',
        'desc_hi' => '<p>स्लिमकोर पेट की अतिरिक्त चर्बी को कम करने और पाचन क्रिया को सक्रिय रखने में अत्यंत प्रभावी है।</p>',
        'ingredients_en' => 'Vrikshamla (Garcinia cambogia), Shunti (Ginger), Medohar Guggulu, Green Coffee, Triphala.',
        'ingredients_hi' => 'वृक्षाम्ल (गार्सिनिया), सोंठ, मेदोहर गुग्गुलु, ग्रीन कॉफी, त्रिफला।',
        'directions_en' => '1 tablet twice daily with warm water 30 minutes before meals.',
        'directions_hi' => 'भोजन से 30 मिनट पहले 1 टैबलेट गुनगुने पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 0,
        'new_arrival' => 1
    ],
    [
        'sku' => 'EV-LS-060',
        'slug' => 'everyveda-liva-shield-tablets',
        'name_en' => 'EveryVeda LivaShield Liver Support (60 Tablets)',
        'name_hi' => 'एवरीवेदा लिवरशील्ड लिवर सुरक्षा फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'organ-digestive-care',
        'price' => 799.00,
        'sale_price' => 599.00,
        'stock' => 160,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'liva-shield.png',
        'short_en' => 'Ayurvedic Liver Support Formula. Supports Liver Health, Detoxification & Digestion.',
        'short_hi' => 'लिवर को डिटॉक्स करने, फैटी लिवर के लक्षणों में राहत और पाचन तंत्र को दुरुस्त रखने का फॉर्मूला।',
        'desc_en' => '<p>LivaShield detoxifies the liver naturally, protects against toxic byproducts, improves bile flow, and promotes optimum metabolic vitality.</p>',
        'desc_hi' => '<p>लिवरशील्ड लिवर की कोशिकाओं को सुरक्षा प्रदान करता है और शरीर से हानिकारक टॉक्सिन्स को बाहर निकालता है।</p>',
        'ingredients_en' => 'Kalmegh, Bhumi Amla, Punarnava, Kutki, Kasani, Amla.',
        'ingredients_hi' => 'कालमेघ, भूमि आंवला, पुनर्नवा, कुटकी, कासनी, आंवला।',
        'directions_en' => '1 tablet twice daily after meals with water.',
        'directions_hi' => 'भोजन के बाद 1 टैबलेट सुबह-शाम सादे पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-UM-060',
        'slug' => 'everyveda-urja-max-tablets',
        'name_en' => 'EveryVeda UrjaMax™ Vitality Formula (60 Tablets)',
        'name_hi' => 'एवरीवेदा ऊर्जा मैक्स™ मेन्स वाइटेलिटी फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'mens-vitality-health',
        'price' => 1299.00,
        'sale_price' => 999.00,
        'stock' => 130,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'urja-max.png',
        'short_en' => "Supports Strength, Stamina & Performance. Helps Improve Energy, Confidence & Endurance.",
        'short_hi' => 'शारीरिक शक्ति, स्टैमिना, आत्मविश्वास और सहनशक्ति बढ़ाने के लिए प्रीमियम आयुर्वेदिक योग।',
        'desc_en' => '<p>EveryVeda UrjaMax™ is infused with pure Himalayan Shilajit, Ashwagandha, and Gokshura to rekindle natural vigor, eliminate daily lethargy, and sustain peak endurance.</p>',
        'desc_hi' => '<p>ऊर्जा मैक्स शरीर में स्फूर्ति का संचार करता है और थकान को दूर कर कार्यक्षमता बढ़ाता है।</p>',
        'ingredients_en' => 'Shilajit Resin Extract, Ashwagandha Root, Gokshura, Kaunch Beej, Safed Musli.',
        'ingredients_hi' => 'शिलाजीत, अश्वगंधा, गोक्षुर, कौंच बीज, सफेद मूसली।',
        'directions_en' => '1 tablet twice daily with lukewarm milk at night.',
        'directions_hi' => '1 टैबलेट रात को सोते समय गुनगुने दूध के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-PM-060',
        'slug' => 'everyveda-prime-man-tablets',
        'name_en' => 'EveryVeda PrimeMan™ Performance Formula (60 Tablets)',
        'name_hi' => 'एवरीवेदा प्राइममैन™ पुरुष पौरुष शक्ति फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'mens-vitality-health',
        'price' => 1499.00,
        'sale_price' => 1099.00,
        'stock' => 100,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'prime-man.png',
        'short_en' => 'Power, Stamina, Strength & Confidence. Powered by Time-Tested Ayurvedic Herbs.',
        'short_hi' => 'पुरुषों के पौरुष बल, शक्ति और आत्मविश्वास के लिए 6 शक्तिशाली जड़ी-बूटियों का सम्मिश्रण।',
        'desc_en' => '<p>PrimeMan™ delivers clinical-grade Ayurvedic support with Ashwagandha, Shilajit, Safed Musli, Gokshura, Kapikachhu, and Jaiphal for unparalleled masculine vitality.</p>',
        'desc_hi' => '<p>प्राइममैन™ पुरुषों के संपूर्ण शारीरिक और मानसिक सामर्थ्य को नई ऊर्जा देता है।</p>',
        'ingredients_en' => 'Ashwagandha, Shilajit, Safed Musli, Gokshura, Kapikachhu, Jaiphal.',
        'ingredients_hi' => 'अश्वगंधा, शुद्ध शिलाजीत, सफेद मूसली, गोखरु, कपिकच्छू, जायफल।',
        'directions_en' => '1 tablet daily with warm milk at bedtime.',
        'directions_hi' => 'प्रतिदिन रात में 1 टैबलेट गुनगुने दूध के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 1
    ],
    [
        'sku' => 'EV-FR-060',
        'slug' => 'everyveda-flexi-relief-tablets',
        'name_en' => 'EveryVeda FlexiRelief™ Joint & Muscle Pain Support (60 Tablets)',
        'name_hi' => 'एवरीवेदा फ्लेक्सीरिलीफ™ दर्द निवारक फॉर्मूला (60 टैबलेट)',
        'cat_slug' => 'pain-hair-care',
        'price' => 849.00,
        'sale_price' => 649.00,
        'stock' => 140,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'flexi-relief.png',
        'short_en' => 'Ayurvedic Muscle & Body Pain Support Formula. Supports Comfort, Flexibility & Recovery.',
        'short_hi' => 'जोड़ों के दर्द, मांसपेशियों की जकड़न और शारीरिक थकान से राहत देने वाला प्राकृतिक फॉर्मूला।',
        'desc_en' => '<p>FlexiRelief™ eases stiffness, promotes synovial lubrication in joints, accelerates physical recovery, and relieves muscular fatigue.</p>',
        'desc_hi' => '<p>घुटनों, कमर और कंधों के दर्द में राहत देने और जोड़ों के लचीलेपन को बनाए रखने में सहायक।</p>',
        'ingredients_en' => 'Shallaki (Boswellia), Guggulu, Rasna, Hadjod, Ashwagandha, Nirgundi.',
        'ingredients_hi' => 'शल्लकी (लोबान), गुग्गुलु, रास्ना, हड़जोड़, अश्वगंधा, निर्गुण्डी।',
        'directions_en' => '1 tablet twice daily after meals with lukewarm water.',
        'directions_hi' => 'भोजन के बाद 1 टैबलेट सुबह-शाम गुनगुने पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 0,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-TC-060',
        'slug' => 'everyveda-thyro-care-tablets',
        'name_en' => 'EveryVeda ThyroCare Thyroid Support (60 Tablets)',
        'name_hi' => 'एवरीवेदा थायरोकेयर थायरॉयड एवं मेटाबॉलिज्म केयर (60 टैबलेट)',
        'cat_slug' => 'organ-digestive-care',
        'price' => 899.00,
        'sale_price' => 699.00,
        'stock' => 110,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'thyro-care.png',
        'short_en' => 'Supports Healthy Thyroid Function, Metabolism & Energy. Helps Maintain Hormonal Balance.',
        'short_hi' => 'थायरॉयड ग्रंथि के स्वस्थ कार्य, मेटाबॉलिज्म और हार्मोनल संतुलन को बनाए रखने का फॉर्मूला।',
        'desc_en' => '<p>ThyroCare provides classical botanical extracts to balance TSH levels naturally, relieve sluggishness, boost energy metabolism, and promote endocrine balance.</p>',
        'desc_hi' => '<p>थायरोकेयर प्राकृतिक रूप से थायरॉयड संतुलन और ऊर्जा स्तर को सुधारने में मदद करता है।</p>',
        'ingredients_en' => 'Kanchnar Guggulu, Varuna, Ashwagandha, Guggulu, Amla, Pippali.',
        'ingredients_hi' => 'कांचनार गुग्गुलु, वरुण, अश्वगंधा, गुग्गुलु, आंवला, पिप्पली।',
        'directions_en' => '1 tablet twice daily 30 minutes before meals with water.',
        'directions_hi' => 'भोजन से 30 मिनट पहले 1 टैबलेट सुबह-शाम सादे पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 0,
        'new_arrival' => 1
    ],
    [
        'sku' => 'EV-PC-060',
        'slug' => 'everyveda-pile-care-tablets',
        'name_en' => 'EveryVeda PileCare Piles & Bowel Comfort (60 Tablets)',
        'name_hi' => 'एवरीवेदा पाइलकेयर बवासीर एवं उदर राहत (60 टैबलेट)',
        'cat_slug' => 'organ-digestive-care',
        'price' => 899.00,
        'sale_price' => 699.00,
        'stock' => 125,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'pile-care.png',
        'short_en' => 'Ayurvedic Care for Piles. Relief, Comfort & Healthy Smooth Bowel Movement.',
        'short_hi' => 'बवासीर, मस्सों की सूजन, जलन और कब्ज में तुरंत राहत और आसान शौच का हर्बल फॉर्मूला।',
        'desc_en' => '<p>PileCare relieves pain, swelling, and burning sensations, controls minor bleeding, softens stool, and improves colon health naturally.</p>',
        'desc_hi' => '<p>पाइलकेयर शौच को सुगम बनाता है, सूजन और दर्द को कम करता है और आंतों को शक्ति देता है।</p>',
        'ingredients_en' => 'Arshoghni Vati, Triphala, Haritaki, Neem, Suran (Amorphophallus), Nagkesar.',
        'ingredients_hi' => 'अर्शोघ्नी वटी, त्रिफला, हरड़, नीम, सूरन, नागकेसर।',
        'directions_en' => '1 tablet twice daily with lukewarm water after meals.',
        'directions_hi' => 'भोजन के बाद 1 टैबलेट सुबह-शाम गुनगुने पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-BV-060',
        'slug' => 'everyveda-bust-veda-tablets',
        'name_en' => 'EveryVeda BustVeda™ Feminine Firmness Formula (60 Tablets)',
        'name_hi' => 'एवरीवेदा बस्टवेदा™ फेमिनिन फर्मनेस एवं कॉन्फिडेंस (60 टैबलेट)',
        'cat_slug' => 'womens-wellness',
        'price' => 1199.00,
        'sale_price' => 899.00,
        'stock' => 95,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'bust-veda.png',
        'short_en' => 'Ayurvedic Breast Enhancement & Firmness Formula. Supports Female Confidence & Natural Contours.',
        'short_hi' => 'महिलाओं के शारीरिक आकार, सुडौलता और त्वचा के लचीलेपन को प्राकृतिक पोषण देने वाला योग।',
        'desc_en' => '<p>BustVeda™ nourishes female tissues, balances estrogenic vitality naturally, enhances elasticity, and boosts female confidence.</p>',
        'desc_hi' => '<p>बस्टवेदा महिलाओं में हार्मोनल संतुलन और प्राकृतिक आकर्षण को बढ़ाने में सहायक है।</p>',
        'ingredients_en' => 'Shatavari, Fennel, Fenugreek, Lodhra, Ashoka, Gokhru.',
        'ingredients_hi' => 'शतावरी, सौंफ, मेथी, लोध्र, अशोक, गोखरू।',
        'directions_en' => '1 tablet twice daily after meals with milk or water.',
        'directions_hi' => 'भोजन के बाद 1 टैबलेट सुबह-शाम दूध या पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 0,
        'new_arrival' => 1
    ],
    [
        'sku' => 'EV-RR-060',
        'slug' => 'everyveda-root-revive-tablets',
        'name_en' => 'EveryVeda RootRevive Hair Growth & Strength (60 Tablets)',
        'name_hi' => 'एवरीवेदा रूटरिवाइव हेयर ग्रोथ एवं रूट स्ट्रेंथ (60 टैबलेट)',
        'cat_slug' => 'pain-hair-care',
        'price' => 849.00,
        'sale_price' => 649.00,
        'stock' => 150,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'root-revive.png',
        'short_en' => 'Ayurvedic Hair Growth & Strength Formula. Supports Stronger, Thicker & Healthier Hair.',
        'short_hi' => 'बालों को जड़ों से मजबूत बनाने, बालों का झड़ना रोकने और नए बाल उगाने में सहायक टैबलेट।',
        'desc_en' => '<p>RootRevive nourishes hair follicles from within with Bhringraj, Amla, and Brahmi, promoting scalp blood circulation and reducing premature hair fall.</p>',
        'desc_hi' => '<p>रूटरिवाइव बालों की जड़ों को आवश्यक पोषण देकर उन्हें घना, मजबूत और चमकदार बनाता है।</p>',
        'ingredients_en' => 'Bhringraj, Amla, Brahmi, Hibiscus, Shankhpushpi, Gunja.',
        'ingredients_hi' => 'भृंगराज, आंवला, ब्राह्मी, गुड़हल, शंखपुष्पी, गुंजा।',
        'directions_en' => '1 tablet twice daily with water after meals.',
        'directions_hi' => '1 टैबलेट सुबह और शाम भोजन के बाद पानी से लें।',
        'featured' => 1,
        'best_seller' => 1,
        'new_arrival' => 0
    ],
    [
        'sku' => 'EV-FB-060',
        'slug' => 'everyveda-fem-balance-capsules',
        'name_en' => 'EveryVeda FemBalance Intimate Wellness (60 Capsules)',
        'name_hi' => 'एवरीवेदा फेमबैलेंस स्त्री इंटिमेट वेलनेस (60 कैप्सूल)',
        'cat_slug' => 'womens-wellness',
        'price' => 899.00,
        'sale_price' => 699.00,
        'stock' => 120,
        'tax_rate' => 12.00,
        'hsn_code' => '30049011',
        'weight' => 150,
        'image' => 'fem-balance.png',
        'short_en' => 'Ayurvedic Care for Leucorrhea & Intimate Wellness. Supports Feminine Hygiene & Comfort.',
        'short_hi' => 'सफेद पानी (ल्यूकोरिया), कमजोरी और महिलाओं की आंतरिक स्वच्छता के लिए सिद्ध आयुर्वेदिक औषधि।',
        'desc_en' => '<p>FemBalance provides relief from irregular white discharge, soothes intimate discomfort, maintains healthy vaginal flora, and restores female vitality.</p>',
        'desc_hi' => '<p>फेमबैलेंस महिलाओं में ल्यूकोरिया की समस्या को दूर कर कमजोरी और कमर दर्द में राहत देता है।</p>',
        'ingredients_en' => 'Ashoka, Lodhra, Shatavari, Mochras, Supari, Yashad Bhasma.',
        'ingredients_hi' => 'अशोक, लोध्र, शतावरी, मोचरस, सुपारी, यशद भस्म।',
        'directions_en' => '1 capsule twice daily with milk or water after meals.',
        'directions_hi' => '1 कैप्सूल सुबह-शाम भोजन के बाद दूध या पानी के साथ लें।',
        'featured' => 1,
        'best_seller' => 0,
        'new_arrival' => 1
    ]
];

foreach ($realProducts as $p) {
    $catId = $catIds[$p['cat_slug']] ?? null;

    $existing = Database::fetchOne("SELECT id FROM products WHERE sku = :sku OR slug = :slug", [
        'sku' => $p['sku'],
        'slug' => $p['slug']
    ]);

    if ($existing) {
        $prodId = (int)$existing['id'];
        Database::update('products', [
            'category_id' => $catId,
            'price' => $p['price'],
            'sale_price' => $p['sale_price'],
            'stock_quantity' => $p['stock'],
            'tax_rate' => $p['tax_rate'],
            'hsn_code' => $p['hsn_code'],
            'weight_grams' => $p['weight'],
            'primary_image' => $p['image'],
            'is_featured' => $p['featured'],
            'is_best_seller' => $p['best_seller'],
            'is_new_arrival' => $p['new_arrival'],
            'status' => 'published',
            'updated_at' => date('Y-m-d H:i:s')
        ], "id = :id", ['id' => $prodId]);
        echo "  [UPDATED] {$p['name_en']} (ID: {$prodId})\n";
    } else {
        $prodId = Database::insert('products', [
            'category_id' => $catId,
            'sku' => $p['sku'],
            'slug' => $p['slug'],
            'price' => $p['price'],
            'sale_price' => $p['sale_price'],
            'stock_quantity' => $p['stock'],
            'low_stock_threshold' => 10,
            'manage_stock' => 1,
            'tax_rate' => $p['tax_rate'],
            'hsn_code' => $p['hsn_code'],
            'weight_grams' => $p['weight'],
            'primary_image' => $p['image'],
            'is_featured' => $p['featured'],
            'is_best_seller' => $p['best_seller'],
            'is_new_arrival' => $p['new_arrival'],
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        echo "  [INSERTED] {$p['name_en']} (ID: {$prodId})\n";
    }

    // English Translation
    Database::query(
        "INSERT INTO product_translations (product_id, locale, name, short_description, description, ingredients, directions, storage_info, warnings, meta_title, meta_description)
         VALUES (:pid, 'en', :name, :sdesc, :desc, :ingr, :dir, :storage, :warn, :mtitle, :mdesc)
         ON DUPLICATE KEY UPDATE name = VALUES(name), short_description = VALUES(short_description), description = VALUES(description), ingredients = VALUES(ingredients), directions = VALUES(directions), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
        [
            'pid' => $prodId,
            'name' => $p['name_en'],
            'sdesc' => $p['short_en'],
            'desc' => $p['desc_en'],
            'ingr' => $p['ingredients_en'],
            'dir' => $p['directions_en'],
            'storage' => 'Store in a cool, dry place away from direct sunlight.',
            'warn' => 'Consult an Ayurvedic Vaidya or physician before use if pregnant or nursing.',
            'mtitle' => $p['name_en'] . ' | EveryVeda India',
            'mdesc' => strip_tags($p['short_en'])
        ]
    );

    // Hindi Translation
    Database::query(
        "INSERT INTO product_translations (product_id, locale, name, short_description, description, ingredients, directions, storage_info, warnings, meta_title, meta_description)
         VALUES (:pid, 'hi', :name, :sdesc, :desc, :ingr, :dir, :storage, :warn, :mtitle, :mdesc)
         ON DUPLICATE KEY UPDATE name = VALUES(name), short_description = VALUES(short_description), description = VALUES(description), ingredients = VALUES(ingredients), directions = VALUES(directions), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description)",
        [
            'pid' => $prodId,
            'name' => $p['name_hi'],
            'sdesc' => $p['short_hi'],
            'desc' => $p['desc_hi'],
            'ingr' => $p['ingredients_hi'],
            'dir' => $p['directions_hi'],
            'storage' => 'सीधी धूप से दूर, ठंडी और सूखी जगह पर रखें।',
            'warn' => 'गर्भावस्था या किसी बीमारी की स्थिति में चिकित्सक से परामर्श लें।',
            'mtitle' => $p['name_hi'] . ' | एवरीवेदा',
            'mdesc' => strip_tags($p['short_hi'])
        ]
    );
}

echo "\nReal EveryVeda product catalogue and imagery integrated successfully!\n";
