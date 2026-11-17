-- EveryVeda.com Database Seed Data
-- Core Roles, Permissions, Admin User, Categories, Products, Variants, Coupons, Settings, Banners, CMS Pages

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Roles
INSERT INTO `admin_roles` (`id`, `slug`, `name`, `description`) VALUES
(1, 'super_admin', 'Super Administrator', 'Unrestricted administrative access to all system features'),
(2, 'order_manager', 'Order Manager', 'Access to view and process orders, update shipping and handle refunds'),
(3, 'product_manager', 'Product Manager', 'Manage catalog, categories, variants, inventory and pricing'),
(4, 'content_manager', 'Content Manager', 'Manage blog posts, banners, CMS pages, FAQs and reviews');

-- 2. Role Permissions
INSERT INTO `admin_permissions` (`role_id`, `permission_key`) VALUES
(1, 'all'),
(2, 'orders.view'), (2, 'orders.edit'), (2, 'shipments.manage'), (2, 'reports.orders'),
(3, 'products.view'), (3, 'products.create'), (3, 'products.edit'), (3, 'categories.manage'), (3, 'inventory.manage'),
(4, 'content.banners'), (4, 'content.blog'), (4, 'content.pages'), (4, 'content.reviews');

-- 3. Default Super Admin
-- Login: admin@everyveda.com / Admin@12345
INSERT INTO `admin_users` (`id`, `role_id`, `name`, `email`, `password_hash`, `status`, `created_at`) VALUES
(1, 1, 'EveryVeda Super Admin', 'admin@everyveda.com', '$2y$10$lUPiS/fPNCJd576Dt6hAwu6Zw1BCJQULvB2dtIkYbSkZUVScI5/12', 'active', NOW());

-- 4. Categories
INSERT INTO `categories` (`id`, `parent_id`, `slug`, `name_en`, `name_hi`, `description_en`, `description_hi`, `image`, `sort_order`, `is_featured`, `is_active`) VALUES
(1, NULL, 'chyawanprash-immunity', 'Immunity & Chyawanprash', 'च्यवनप्राश और रोग प्रतिरोधक क्षमता', 'Traditional Rasayana formulations crafted with fresh organic amla and potent forest herbs to bolster seasonal vitality.', 'ताजे आंवले और दुर्लभ जड़ी-बूटियों से निर्मित पारंपरिक रसायन जो प्राकृतिक रोग प्रतिरोधक क्षमता को मजबूत करता है।', 'cat-chyawanprash.svg', 1, 1, 1),
(2, NULL, 'shilajit-vitality', 'Pure Shilajit & Vitality', 'शुद्ध शिलाजीत और पौरुष बल', 'Sourced from the pristine high-altitude Himalayan ranges, purified through traditional Shodhana procedures.', 'हिमालय की उच्च चोटियों से प्राप्त और पारंपरिक शोधन विधि द्वारा शुद्ध किया गया ऊर्जा और पौरुष वर्धक शिलाजीत।', 'cat-shilajit.svg', 2, 1, 1),
(3, NULL, 'herbal-juices', 'Herbal Juices & Syrups', 'हर्बल स्वरस और जूस', 'Cold-pressed natural plant juices preserving vital bio-enzymes with zero added synthetic sugar or artificial colors.', 'बिना किसी कृत्रिम रंग या चीनी के कोल्ड-प्रेस्ड प्राकृतिक वनस्पति स्वरस।', 'cat-juices.svg', 3, 1, 1),
(4, NULL, 'joint-pain-relief', 'Joint Care & Pain Relief', 'जोड़ों का दर्द और तेल', 'Classical taila (oils) and herbal churnas prepared according to Sahasrayogam and Charaka Samhita.', 'सहस्रयोगम और चरक संहिता की पारंपरिक विधियों से तैयार औषधीय तैल और चूर्ण।', 'cat-jointpain.svg', 4, 1, 1),
(5, NULL, 'hair-skin-care', 'Hair & Skin Natural Care', 'केश और त्वचा प्राकृतिक देखभाल', 'Herbal hair oils, face lepa, and kumkumadi elixirs made from wild saffron and handpicked botanicals.', 'केसर, भृंगराज और प्राकृतिक औषधियों से तैयार सौम्य और शुद्ध केश एवं त्वचा प्रसाधन।', 'cat-hairskin.svg', 5, 1, 1),
(6, NULL, 'digestive-wellness', 'Digestive Wellness', 'पाचन एवं उदर स्वास्थ्य', 'Time-tested herbal carminatives and digestive powders for optimum Agni balance and gentle natural cleansing.', 'पेट की गैस, अपच और कब्ज से राहत देने वाले पारंपरिक पाचक चूर्ण और जड़ी-बूटियाँ।', 'cat-digestive.svg', 6, 1, 1);

-- 5. Products
INSERT INTO `products` (`id`, `category_id`, `sku`, `slug`, `price`, `sale_price`, `stock_quantity`, `low_stock_threshold`, `manage_stock`, `tax_rate`, `hsn_code`, `weight_grams`, `is_featured`, `is_best_seller`, `is_new_arrival`, `status`, `primary_image`) VALUES
(1, 1, 'EV-CHY-001', 'everyveda-royal-chyawanprash-gold', 599.00, 499.00, 150, 10, 1, 12.00, '30049011', 500, 1, 1, 0, 'published', 'product-chyawanprash.svg'),
(2, 2, 'EV-SHL-001', 'everyveda-pure-himalayan-shilajit-resin', 1299.00, 999.00, 85, 10, 1, 12.00, '30049011', 20, 1, 1, 0, 'published', 'product-shilajit.svg'),
(3, 3, 'EV-AML-001', 'wild-harvested-forest-amla-juice', 349.00, 280.00, 200, 15, 1, 12.00, '20098990', 1000, 1, 0, 1, 'published', 'product-amla-juice.svg'),
(4, 4, 'EV-OIL-001', 'maha-narayan-joint-relief-oil', 499.00, 399.00, 120, 10, 1, 12.00, '30049011', 200, 1, 1, 0, 'published', 'product-pain-oil.svg'),
(5, 5, 'EV-BHR-001', 'bhringraj-brahmi-herbal-hair-oil', 449.00, 360.00, 95, 10, 1, 18.00, '33059010', 200, 1, 1, 0, 'published', 'product-hair-oil.svg'),
(6, 6, 'EV-TRP-001', 'triphala-churna-digestive-cleanse', 249.00, 199.00, 180, 20, 1, 5.00, '12119029', 250, 1, 0, 0, 'published', 'product-triphala.svg'),
(7, 2, 'EV-ASH-001', 'ashwagandha-ksheerpak-vitality-capsules', 699.00, 549.00, 110, 12, 1, 12.00, '30049011', 100, 0, 1, 1, 'published', 'product-ashwagandha.svg'),
(8, 5, 'EV-KUM-001', 'pure-kumkumadi-radiance-tailam', 899.00, 749.00, 70, 8, 1, 18.00, '33049990', 30, 1, 0, 1, 'published', 'product-kumkumadi.svg');

-- 6. Product Translations
INSERT INTO `product_translations` (`product_id`, `locale`, `name`, `short_description`, `description`, `ingredients`, `directions`, `storage_info`, `warnings`, `meta_title`, `meta_description`) VALUES
(1, 'en', 'EveryVeda Royal Gold Chyawanprash (500g)', 
 'Traditional Ayurvedic rasayana prepared with fresh forest Amla, Desi Cow Ghee, Saffron, and 40+ rejuvenating herbs.',
 '<p>EveryVeda Royal Gold Chyawanprash is handcrafted using time-honored Ayurvedic principles described in the Charaka Samhita. We use wild forest amla simmered gently with Desi Gir cow bilona ghee and raw forest honey.</p><p>It provides natural stamina, strengthens natural body defense, and promotes overall respiratory comfort during seasonal shifts.</p>',
 'Fresh Wild Amla, Desi Cow Ghee, Pure Honey, Kesar (Saffron), Ashwagandha, Shatavari, Pippali, Cardamom, Guduchi, Dashmoola.',
 'Consume 1 to 2 tablespoons twice daily followed by warm cow milk or warm water. Ideal for both adults and children above 5 years.',
 'Store in a cool, dry place away from direct sunlight. Do not refrigerate. Always use a dry spoon.',
 'If you are pregnant, nursing, or have diabetic conditions, please consult your healthcare practitioner before use.',
 'Buy Royal Gold Chyawanprash Online | EveryVeda India',
 'Handcrafted authentic Ayurvedic Chyawanprash with fresh forest amla and saffron for daily natural immunity. Fast shipping across India.'),

(1, 'hi', 'एवरीवेदा रॉयल गोल्ड च्यवनप्राश (500 ग्राम)',
 'ताजे जंगली आंवले, शुद्ध देसी गाय के घी, कश्मीरी केसर और 40 से अधिक दुर्लभ जड़ी-बूटियों से निर्मित पारंपरिक रसायन।',
 '<p>एवरीवेदा रॉयल गोल्ड च्यवनप्राश चरक संहिता के प्राचीन सूत्रों के अनुसार तैयार किया जाता है। इसमें किसी भी प्रकार का कृत्रिम स्वाद या रिफाइंड चीनी नहीं मिलाई जाती।</p><p>यह प्राकृतिक जीवन शक्ति को बढ़ाता है, शरीर की स्वाभाविक रोग प्रतिरोधक क्षमता को सुदृढ़ करता है और मौसमी बदलावों में श्वसन प्रणाली को सहारा देता है।</p>',
 'ताजा जंगली आंवला, देसी गाय का घी, शुद्ध शहद, कश्मीरी केसर, अश्वगंधा, शतावरी, पिप्पली, इलायची, गिलोय, दशमूल।',
 'प्रतिदिन 1 से 2 चम्मच सुबह और शाम गुनगुने दूध या गुनगुने पानी के साथ लें। 5 वर्ष से अधिक उम्र के बच्चों और वयस्कों दोनों के लिए उपयुक्त।',
 'सीधे धूप से दूर, ठंडी और सूखी जगह पर रखें। फ्रिज में न रखें। हमेशा सूखे चम्मच का उपयोग करें।',
 'गर्भावस्था या मधुमेह की स्थिति में उपयोग से पहले योग्य चिकित्सक से सलाह लें।',
 'रॉयल गोल्ड च्यवनप्राश खरीदें | एवरीवेदा भारत',
 'प्राकृतिक रोग प्रतिरोधक क्षमता और दैनिक स्फूर्ति के लिए शुद्ध आयुर्वेदिक च्यवनप्राश। पूरे भारत में सुरक्षित डिलीवरी।'),

(2, 'en', 'EveryVeda Pure Himalayan Shilajit Resin (20g)',
 '100% natural, lab-tested Grade-A Himalayan Shilajit resin containing over 80% Fulvic Acid and trace minerals.',
 '<p>Sourced from elevations above 16,000 feet in the pristine Himalayas, EveryVeda Shilajit resin undergoes traditional Shodhana (water purification) with Triphala decoction.</p><p>Supports natural cellular energy production (ATP), stamina, physical recovery, and healthy aging.</p>',
 'Pure Purified Himalayan Shilajit (Asphaltum punjabianum) Resin (80%+ Fulvic Acid).',
 'Dissolve a pea-sized portion (300-500mg) in warm milk, green tea, or warm water once daily in the morning.',
 'Keep container tightly sealed in a cool, dry place. Resin may harden in cold weather; soften by placing jar in warm water.',
 'Keep out of reach of children. Not recommended for individuals with kidney stones or high uric acid levels without medical advice.',
 'Pure Himalayan Shilajit Resin 20g | EveryVeda',
 'Authentic lab-tested Himalayan Shilajit resin with high fulvic acid for physical vigor and endurance.'),

(2, 'hi', 'एवरीवेदा शुद्ध हिमालयी शिलाजीत राल (20 ग्राम)',
 '16,000 फीट की ऊंचाई से प्राप्त, 80%+ फुल्विक एसिड और खनिज तत्वों से युक्त शुद्ध शोधित शिलाजीत।',
 '<p>एवरीवेदा शिलाजीत राल पारंपरिक त्रिफला क्वाथ शोधन विधि द्वारा शुद्ध किया जाता है। यह शरीर में ऊर्जा उत्पादन (ATP), शारीरिक बल और मानसिक एकाग्रता को प्राकृतिक सहारा देता है।</p>',
 '100% शुद्ध शोधित हिमालयी शिलाजीत राल।',
 'चने के दाने बराबर (300-500 मि.ग्रा.) गुनगुने दूध या गुनगुने पानी में घोलकर सुबह के समय लें।',
 'ठंडी और सूखी जगह पर ढक्कन अच्छी तरह बंद रखें। ठंड में सख्त होने पर डिब्बी को कुछ देर हल्के गुनगुने पानी में रखें।',
 'बच्चों की पहुंच से दूर रखें। यूरिक एसिड या गुर्दे की पथरी के मरीज चिकित्सकीय परामर्श के बाद ही लें।',
 'शुद्ध हिमालयी शिलाजीत राल 20 ग्राम | एवरीवेदा',
 'पौरुष बल, शारीरिक सहनशक्ति और स्फूर्ति के लिए लैब-परीक्षित हिमालयी शिलाजीत।'),

(3, 'en', 'Wild Organic Amla Juice (1000ml)',
 'Fresh cold-pressed Indian Gooseberry (Amla) juice rich in natural Vitamin C and bio-antioxidants.',
 '<p>Extracted directly from organic wild amla harvested in the central Indian forests. No water dilution, no added synthetic citric acid or sugar.</p><p>Aids natural digestion, supports healthy liver metabolism, and promotes skin radiance.</p>',
 'Pure Wild Amla (Phyllanthus emblica) Fruit Juice 99.8%, Permitted Class II Preservatives 0.2%.',
 'Mix 20-30ml of Amla juice with equal amount of water and drink empty stomach in the morning.',
 'Store in a cool and dry place. Shake well before use. Consume within 30 days after opening.',
 'Do not consume directly without diluting in water.',
 'Wild Organic Amla Juice 1 Litre | EveryVeda',
 'Cold pressed 100% pure wild Amla juice for daily digestion, detox, and natural Vitamin C.'),

(3, 'hi', 'शुद्ध प्राकृतिक आंवला स्वरस (1000 मिली)',
 'प्राकृतिक विटामिन सी और एंटीऑक्सीडेंट से भरपूर शुद्ध कोल्ड-प्रेस्ड आंवला जूस।',
 '<p>बिना पानी मिलाए, प्राकृतिक आंवलों से निकाला गया शुद्ध स्वरस। यह पाचन को दुरुस्त रखता है और शरीर के विषैले तत्वों को बाहर निकालने में मदद करता है।</p>',
 'शुद्ध आंवला फल स्वरस 99.8%।',
 '20 से 30 मिली आंवला स्वरस बराबर मात्रा में पानी मिलाकर सुबह खाली पेट पिएं।',
 'ठंडी जगह पर रखें। खोलने के 30 दिनों के भीतर उपयोग करें। उपयोग से पहले अच्छी तरह हिलाएं।',
 'सीधे बिना पानी मिलाए सेवन न करें।',
 'शुद्ध जंगली आंवला स्वरस 1 लीटर | एवरीवेदा',
 'प्राकृतिक विटामिन सी और पाचन शक्ति के लिए शुद्ध कोल्ड-प्रेस्ड आंवला जूस।'),

(4, 'en', 'Maha Narayan Joint Relief Oil (200ml)',
 'Classical Ayurvedic herbal massage oil enriched with Dashmoola, Ashwagandha, and Sesame oil for joints and muscles.',
 '<p>Prepared following authentic Bhaishajya Ratnavali texts. Maha Narayan oil penetrates deep into muscle tissues and joint junctions to relieve daily stiffness, muscle fatigue, and discomfort caused by physical exertion.</p>',
 'Bilva, Ashwagandha, Brihati, Kantakari, Gokshura, Bala, Neem, Til Taila (Sesame Oil), Camphor.',
 'Warm slightly and gently massage over affected joints or muscles for 10-15 minutes until absorbed. For best results, follow with a warm water compress.',
 'For external application only. Keep tightly capped in a dry place away from heat.',
 'Do not apply on open cuts, wounds, or sensitive skin. Discontinue if redness occurs.',
 'Maha Narayan Joint Pain Relief Oil 200ml | EveryVeda',
 'Traditional Ayurvedic herbal massage oil for joint stiffness, knee comfort, and muscle recovery.'),

(4, 'hi', 'महानारायण तैल - जोड़ों के दर्द का आयुर्वेदिक तेल (200 मिली)',
 'दशमूल, अश्वगंधा और शुद्ध तिल के तेल से सिद्ध प्राचीन आयुर्वेदिक मालिश तैल।',
 '<p>भैषज्य रत्नावली के शास्त्रीय विधान से निर्मित। जोड़ों की जकड़न, घुटनों के दर्द, मांसपेशियों की थकान और अकड़न में अत्यंत लाभकारी।</p>',
 'बिल्व, अश्वगंधा, बृहती, कंटकारी, गोक्षुर, बला, नीम, शुद्ध तिल का तैल, कपूर।',
 'हल्का गुनगुना करके प्रभावित जोड़ या मांसपेशियों पर 10-15 मिनट तक हल्के हाथों से मालिश करें।',
 'केवल बाह्य उपयोग के लिए। कटे या छिले हुए स्थान पर न लगाएं।',
 'आंखों और खुले घावों से दूर रखें।',
 'महानारायण जोड़ों का दर्द निवारक तैल | एवरीवेदा',
 'जोड़ों की जकड़न और घुटनों के आराम के लिए शास्त्रीय महानारायण तेल।'),

(5, 'en', 'Bhringraj & Brahmi Herbal Hair Oil (200ml)',
 'Kshirapak vidhi prepared scalp therapy oil with Bhringraj, Brahmi, Amla, and Virgin Coconut oil.',
 '<p>Formulated to deeply nourish hair follicles, reduce daily hair thinning, calm the nervous system, and encourage restful sleep when gently massaged before bed.</p>',
 'Eclipta Alba (Bhringraj), Bacopa Monnieri (Brahmi), Emblica Officinalis (Amla), Cold Pressed Coconut & Sesame Oil.',
 'Apply gently onto scalp and roots using fingertips. Leave on for minimum 2 hours or overnight before washing with a mild herbal shampoo.',
 'Store in a dry location at room temperature.',
 'For external scalp application only. Conduct patch test before first application.',
 'Bhringraj & Brahmi Herbal Hair Oil 200ml | EveryVeda',
 'Classical Kshirapak hair oil for scalp nourishment, hair strength, and calming relaxation.'),

(5, 'hi', 'भृंगराज एवं ब्राह्मी हर्बल हेयर ऑयल (200 मिली)',
 'क्षीरपाक विधि से तैयार विशुद्ध भृंगराज, ब्राह्मी और आंवला युक्त आयुर्वेदिक केश तैल।',
 '<p>बालों की जड़ों को गहराई से पोषण देने, बालों के झड़ने को नियंत्रित करने और मानसिक शांति के लिए अत्यंत लाभकारी तेल।</p>',
 'भृंगराज, ब्राह्मी, आंवला, शुद्ध तिल का तेल, नारियल का तेल।',
 'उँगलियों के पोरों से सिर की त्वचा पर हल्के हाथों से मालिश करें। धोने से कम से कम 2 घंटे पहले या रात भर लगा रहने दें।',
 'ठंडी और सूखी जगह पर रखें।',
 'केवल सिर की त्वचा पर बाह्य उपयोग के लिए।',
 'भृंगराज और ब्राह्मी आयुर्वेदिक केश तेल | एवरीवेदा',
 'बालों की मजबूती और मानसिक सुकून के लिए विशुद्ध आयुर्वेदिक तेल।'),

(6, 'en', 'Triphala Churna Digestive Cleanse (250g)',
 'Balanced traditional combination of Amla, Haritaki, and Bibhitaki for gentle colon detox and digestive harmony.',
 '<p>EveryVeda Triphala Churna uses equal ratio of pure forest-harvested fruits, sun-dried and micro-pulverized. Helps regulate bowel movements naturally without forming dependency.</p>',
 'Equal proportions of Emblica Officinalis (Amalaki), Terminalia Chebula (Haritaki), Terminalia Bellirica (Bibhitaki).',
 'Mix 1 teaspoon (3-5g) with a glass of lukewarm water at bedtime.',
 'Keep airtight in container to prevent moisture absorption.',
 'Not recommended during pregnancy without clinical supervision.',
 'Triphala Churna 250g | EveryVeda Digestives',
 'Classical tri-fruit digestive churna for natural bowel regularity and colon health.'),

(6, 'hi', 'त्रिफला चूर्ण - प्राकृतिक उदर शुद्धि (250 ग्राम)',
 'हरड़, बहेड़ा और आंवला का संतुलित आयुर्वेदिक चूर्ण जो पाचन को सुचारू और पेट को साफ रखता है।',
 '<p>प्राकृतिक वनौषधियों से तैयार सूक्ष्म चूर्ण जो बिना किसी आदत के पेट की स्वाभाविक सफाई में सहायता करता है।</p>',
 'हरड़ (हरीतकी), बहेड़ा (बिभीतक), आंवला (आमलकी) समान अनुपात में।',
 'रात को सोते समय 1 चम्मच (3-5 ग्राम) गुनगुने पानी के साथ लें।',
 'नमी से बचाने के लिए डिब्बे को हमेशा अच्छी तरह बंद रखें।',
 'गर्भावस्था में चिकित्सकीय सलाह के बिना न लें।',
 'त्रिफला चूर्ण 250 ग्राम खरीदें | एवरीवेदा',
 'कब्ज और गैस से राहत के लिए शुद्ध आयुर्वेदिक त्रिफला चूर्ण।'),

(7, 'en', 'Ashwagandha Ksheerpak Capsules (60 Veg Caps)',
 'Standardized 5% Withanolides root extract fortified through traditional milk decoction process.',
 '<p>Supports healthy stress response (cortisol balance), deep restful sleep, physical endurance, and nervous rejuvenation.</p>',
 'Withania Somnifera (Ashwagandha) Root Extract 500mg (standardized to 5% Withanolides).',
 'Take 1 to 2 capsules twice daily with warm milk or water after meals.',
 'Store below 25°C in a dry place.',
 'Consult physician if taking thyroid or sedative medications.',
 'Ashwagandha Vitality Capsules 60s | EveryVeda',
 'Standardized Ashwagandha capsules for daily stress management, energy, and muscle strength.'),

(7, 'hi', 'अश्वगंधा क्षीरपाक कैप्सूल (60 शाकाहारी कैप्सूल)',
 '5% विदानोलायड्स से युक्त मानकीकृत अश्वगंधा जड़ का सत्व जो तनाव मुक्ति और बल प्रदान करता है।',
 '<p>दैनिक मानसिक तनाव को कम करने, मांसपेशियों की ताकत बढ़ाने और गहरी नींद को बढ़ावा देने में अत्यंत उपयोगी।</p>',
 'अश्वगंधा मूल सत्व 500 मि.ग्रा.।',
 '1 से 2 कैप्सूल भोजन के बाद गुनगुने दूध या पानी के साथ दिन में दो बार लें।',
 'ठंडी और सूखी जगह पर रखें।',
 'चिकित्सक की देखरेख में उपयोग करें यदि आप अन्य दवाएं ले रहे हैं।',
 'अश्वगंधा कैप्सूल 60 कैप्सूल | एवरीवेदा',
 'तनाव मुक्ति, स्फूर्ति और शारीरिक शक्ति के लिए शुद्ध अश्वगंधा।'),

(8, 'en', 'Pure Kumkumadi Radiance Tailam (30ml)',
 'Ancient beauty elixir infused with pure Kashmiri Kesar, Red Sandalwood, and 24 classical botanicals.',
 '<p>Prepared in pure goat milk and sesame oil. Promotes natural skin glow, smooths blemishes, and hydrates deeply without synthetic silicones.</p>',
 'Kesar (Crocus Sativus), Raktachandan, Manjistha, Yashtimadhu, Padmaka, Goat Milk, Sesame Oil.',
 'Cleanse face and apply 3-4 drops gently over face and neck with upward strokes at bedtime.',
 'Keep away from direct heat and light.',
 'For external facial use only. Avoid contact with eyes.',
 'Kumkumadi Radiance Tailam 30ml | EveryVeda',
 'Pure Kashmiri Saffron Ayurvedic facial oil for radiant natural complexion.'),

(8, 'hi', 'विशुद्ध कुमकुमादि सौंदर्य तैलम (30 मिली)',
 'कश्मीरी केसर, लाल चंदन और 24 आयुर्वेदिक वनस्पतियों से सिद्ध प्राचीन सौंदर्य तैल।',
 '<p>चेहरे की स्वाभाविक चमक बढ़ाने, दाग-धब्बे हल्के करने और त्वचा को गहरा पोषण देने के लिए अमृत तुल्य तैल।</p>',
 'कश्मीरी केसर, रक्तचंदन, मंजीष्ठा, मुलेठी, पद्मकाष्ठ, बकरी का दूध, तिल का तेल।',
 'चेहरे को धोकर रात को सोते समय 3-4 बूंदें चेहरे और गर्दन पर हल्के हाथों से ऊपर की दिशा में लगाएं।',
 'सीधी धूप से दूर रखें।',
 'केवल चेहरे पर बाह्य उपयोग के लिए।',
 'शुद्ध कुमकुमादि तैलम 30 मिली | एवरीवेदा',
 'प्राकृतिक निखार और बेदाग त्वचा के लिए शुद्ध कश्मीरी केसर युक्त कुमकुमादि तेल।');

-- 7. Product Variants (Pack sizes & Bundles)
INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `variant_name_en`, `variant_name_hi`, `price`, `sale_price`, `stock_quantity`, `weight_grams`, `is_default`, `is_active`) VALUES
(1, 1, 'EV-CHY-500G', '500g Jar', '500 ग्राम जार', 599.00, 499.00, 100, 500, 1, 1),
(2, 1, 'EV-CHY-1KG', '1kg Economy Pack', '1 किग्रा बचत पैक', 1099.00, 899.00, 50, 1000, 0, 1),
(3, 2, 'EV-SHL-20G', '20g Glass Jar', '20 ग्राम कांच का जार', 1299.00, 999.00, 60, 20, 1, 1),
(4, 2, 'EV-SHL-40G', '40g Double Pack (Save ₹300)', '40 ग्राम डबल पैक (बचत ₹300)', 2399.00, 1699.00, 25, 40, 0, 1),
(5, 3, 'EV-AML-1L', '1000ml Bottle', '1000 मिली बोतल', 349.00, 280.00, 150, 1000, 1, 1),
(6, 3, 'EV-AML-2L', 'Pack of 2 (2000ml)', '2 का पैक (2000 मिली)', 650.00, 499.00, 50, 2000, 0, 1),
(7, 4, 'EV-OIL-200ML', '200ml Bottle', '200 मिली बोतल', 499.00, 399.00, 80, 200, 1, 1),
(8, 4, 'EV-OIL-400ML', 'Pack of 2 (400ml)', '2 का पैक (400 मिली)', 950.00, 720.00, 40, 400, 0, 1);

-- 7b. Product Secondary Gallery Images
INSERT INTO `product_images` (`product_id`, `image_path`, `alt_text_en`, `alt_text_hi`, `sort_order`, `is_primary`) VALUES
(1, 'product-chyawanprash-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(2, 'product-shilajit-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(3, 'product-amla-juice-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(4, 'product-pain-oil-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(5, 'product-hair-oil-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(6, 'product-triphala-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(7, 'product-ashwagandha-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0),
(8, 'product-kumkumadi-back.svg', 'Product Specifications & Directions Back View', 'उत्पाद घटक एवं उपयोग विधि पृष्ठ भाग', 2, 0);

-- 8. Coupons
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `usage_limit_global`, `usage_limit_per_user`, `used_count`, `start_date`, `end_date`, `is_active`) VALUES
(1, 'VEDA10', 'percentage', 10.00, 499.00, 200.00, 1000, 2, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
(2, 'WELCOME100', 'fixed', 100.00, 999.00, NULL, 500, 1, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1),
(3, 'AYUSH20', 'percentage', 20.00, 1499.00, 500.00, 200, 1, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 1);

-- 9. Banners
INSERT INTO `banners` (`id`, `title_en`, `title_hi`, `subtitle_en`, `subtitle_hi`, `link_url`, `image_path`, `sort_order`, `is_active`) VALUES
(1, 'Pure Ayurvedic Living Delivered To Your Doorstep', 'शुद्ध आयुर्वेदिक जीवनशैली आपके द्वार तक', 'Handcrafted classical formulations with zero chemical additives & authentic Indian botanicals.', 'रसायन मुक्त, 100% शुद्ध जड़ी-बूटियों से निर्मित प्रामाणिक आयुर्वेदिक उत्पाद।', '/catalog', 'banner-hero-1.svg', 1, 1),
(2, 'Himalayan Gold Grade-A Shilajit', 'हिमालयी शुद्ध शिलाजीत — पौरुष एवं शक्ति', 'Purified with classical Triphala Shodhana. Rich in 80%+ Fulvic Acid & Vital Minerals.', 'पारंपरिक त्रिफला शोधन से तैयार, 80%+ फुल्विक एसिड युक्त शुद्ध शिलाजीत।', '/product/everyveda-pure-himalayan-shilajit-resin', 'banner-hero-2.svg', 2, 1),
(3, 'Royal Gold Chyawanprash For Family Immunity', 'पूरे परिवार की रोग प्रतिरोधक क्षमता का रक्षक', 'Enriched with fresh forest Amla, pure Saffron, and Desi Cow Bilona Ghee.', 'ताजे आंवले, शुद्ध केसर और देसी गाय के घी से निर्मित शाही च्यवनप्राश।', '/product/everyveda-royal-chyawanprash-gold', 'banner-hero-3.svg', 3, 1);

-- 10. Homepage Sections
INSERT INTO `homepage_sections` (`id`, `section_key`, `title_en`, `title_hi`, `sort_order`, `is_active`) VALUES
(1, 'hero_slider', 'Hero Banner Slider', 'मुख्य स्लाइडर', 1, 1),
(2, 'trust_badges', 'Trust & Purity Badges', 'विश्वास एवं शुद्धता प्रतीक', 2, 1),
(3, 'featured_categories', 'Shop by Category', 'श्रेणी अनुसार खरीदें', 3, 1),
(4, 'best_sellers', 'Best Selling Wellness Formulations', 'सर्वाधिक लोकप्रिय उत्पाद', 4, 1),
(5, 'promotional_banner', 'Promotional Banner', 'विशेष ऑफर बैनर', 5, 1),
(6, 'featured_products', 'Recommended by Vaidyas', 'वैद्य अनुमोदित उत्पाद', 6, 1),
(7, 'brand_story', 'EveryVeda Brand Heritage', 'एवरीवेदा परंपरा एवं संकल्प', 7, 1),
(8, 'testimonials', 'What Our Customers Say', 'ग्राहकों के अनुभव', 8, 1),
(9, 'wellness_blog', 'Ayurvedic Wellness Journal', 'आयुर्वेदिक स्वास्थ्य पत्रिका', 9, 1),
(10, 'newsletter', 'Subscribe for Ayurveda Wisdom', 'ज्ञान एवं विशेष छूट के लिए जुड़ें', 10, 1);

-- 11. CMS Pages
INSERT INTO `pages` (`id`, `slug`, `is_active`) VALUES
(1, 'about-us', 1),
(2, 'shipping-policy', 1),
(3, 'return-refund-policy', 1),
(4, 'privacy-policy', 1),
(5, 'terms-conditions', 1),
(6, 'medical-disclaimer', 1);

INSERT INTO `page_translations` (`page_id`, `locale`, `title`, `content`, `meta_title`, `meta_description`) VALUES
(1, 'en', 'About EveryVeda',
 '<h2>Our Philosophy: Ancient Wisdom for Modern Vitality</h2><p>EveryVeda was established with a singular, resolute mission: to restore the uncompromising integrity of classical Indian Ayurveda. We believe that wellness is not about quick synthetic fixes, but about restoring equilibrium (Samatva) within the human body.</p><h3>Uncompromising Quality Standards</h3><ul><li><strong>Ethical Wildcrafting:</strong> Herbs gathered at peak potency from protected bio-reserves.</li><li><strong>Classical Shastra Formulation:</strong> Prepared strictly as per Charaka Samhita and Bhaishajya Ratnavali.</li><li><strong>Zero Adulteration:</strong> Free from heavy metals, synthetic binders, artificial food colors, and harmful parabens.</li></ul>',
 'About Us | EveryVeda — Authentic Ayurvedic Living',
 'Discover EveryVeda philosophy, our pure herb sourcing, and classical preparation standards.'),

(1, 'hi', 'एवरीवेदा के बारे में',
 '<h2>हमारा दृष्टिकोण: आधुनिक जीवन में प्राचीन ज्ञान</h2><p>एवरीवेदा की स्थापना एक अटूट संकल्प के साथ की गई: भारतीय आयुर्वेद की शास्त्रीय शुद्धता को हर घर तक पहुंचाना। हमारा मानना है कि स्वास्थ्य रासायनिक गोलियों से नहीं, बल्कि शरीर में त्रिदोष (वात, पित्त, कफ) के प्राकृतिक संतुलन से प्राप्त होता है।</p><h3>हमारी शुद्धता के मानक</h3><ul><li><strong>प्राकृतिक जंगलों से जड़ी-बूटियाँ:</strong> दुर्लभ औषधियों का सही मौसम में संकलन।</li><li><strong>शास्त्रीय विधान:</strong> चरक संहिता और सुश्रुत संहिता के मूल नियमों का पालन।</li><li><strong>100% शुद्ध:</strong> भारी धातुओं, कृत्रिम रंगों और हानिकारक रसायनों से पूरी तरह मुक्त।</li></ul>',
 'हमारे बारे में | एवरीवेदा — शुद्ध आयुर्वेदिक जीवनशैली',
 'एवरीवेदा के ध्येय, जड़ी-बूटियों के चयन और शास्त्रीय निर्माण की जानकारी।'),

(2, 'en', 'Shipping & Delivery Policy',
 '<h2>Delivery Across India</h2><p>EveryVeda ships to over 19,000 PIN codes across India via premier courier partners including Blue Dart, Delhivery, DTDC, and India Post Speed Post.</p><h3>Shipping Charges</h3><ul><li><strong>Orders above ₹499:</strong> FREE Standard Shipping.</li><li><strong>Orders below ₹499:</strong> Flat nominal shipping fee of ₹50.</li></ul><h3>Estimated Delivery Timelines</h3><ul><li>Metro Cities (Delhi NCR, Mumbai, Bengaluru, Chennai, Kolkata): 2-4 business days.</li><li>Tier 2 and Tier 3 cities: 4-6 business days.</li><li>Remote locations and North East / J&K: 5-8 business days.</li></ul>',
 'Shipping & Delivery Policy | EveryVeda India',
 'Information on delivery timeframes, tracking, and free shipping over ₹499 across India.'),

(2, 'hi', 'शिपिंग और डिलीवरी नीति',
 '<h2>संपूर्ण भारत में सुरक्षित डिलीवरी</h2><p>एवरीवेदा भारत के 19,000 से अधिक पिन कोड पर प्रमुख कूरियर पार्टनर्स (ब्लू डार्ट, डेल्हीवरी, डीटीडीसी, इंडिया पोस्ट) के माध्यम से पार्सल पहुंचाता है।</p><h3>शिपिंग शुल्क</h3><ul><li><strong>₹499 से अधिक के ऑर्डर:</strong> निःशुल्क (FREE) डिलीवरी।</li><li><strong>₹499 से कम के ऑर्डर:</strong> केवल ₹50 का सामान्य शिपिंग शुल्क।</li></ul><h3>डिलीवरी समय-सीमा</h3><ul><li>मेट्रो शहर: 2 से 4 कार्य दिवस।</li><li>अन्य शहर: 4 से 6 कार्य दिवस।</li><li>दूरदराज के क्षेत्र: 5 से 8 कार्य दिवस।</li></ul>',
 'शिपिंग और डिलीवरी नीति | एवरीवेदा भारत',
 'भारत भर में डिलीवरी समय, ट्रैकिंग और ₹499 से अधिक पर मुफ्त शिपिंग की जानकारी।'),

(3, 'en', 'Return, Replacement & Refund Policy',
 '<h2>Customer-Centric Return Policy</h2><p>At EveryVeda, our customers’ trust is paramount. Since our wellness and Ayurvedic items are ingestible consumables and personal wellness formulations, opened items cannot be restocked for hygiene and safety reasons.</p><h3>Damaged, Leaked, or Incorrect Products</h3><p>If you receive a package that is damaged, leaked in transit, or incorrect, please notify us within <strong>7 days</strong> of delivery with photographs/unboxing video at support@everyveda.com or via WhatsApp at +91 98765 43210. We will promptly issue an express replacement or complete refund.</p><h3>Refund Processing</h3><p>Refunds for prepaid orders via Razorpay are processed directly back to your original source of payment within 5-7 business working days.</p>',
 'Return & Refund Policy | EveryVeda',
 'Read EveryVeda clear 7-day replacement and refund policy for damaged or transit issues.'),

(3, 'hi', 'वापसी एवं रिफंड नीति',
 '<h2>ग्राहक संतुष्टि और सुरक्षा नीति</h2><p>एवरीवेदा में आपकी संतुष्टि सर्वोच्च प्राथमिकता है। क्योंकि हमारे उत्पाद प्राकृतिक सेवन योग्य और व्यक्तिगत उपयोग के हैं, इसलिए सुरक्षा कारणों से खुली हुई सील वाले उत्पाद वापस नहीं लिए जा सकते।</p><h3>क्षतिग्रस्त या गलत उत्पाद मिलने पर</h3><p>यदि आपको क्षतिग्रस्त, रिसाव युक्त या गलत उत्पाद प्राप्त होता है, तो कृपया डिलीवरी के <strong>7 दिनों</strong> के भीतर पार्सल की फोटो सहित हमें support@everyveda.com पर ईमेल करें या +91 98765 43210 पर संपर्क करें। हम तुरंत नया उत्पाद भेजेंगे अथवा पूर्ण रिफंड जारी करेंगे।</p>',
 'वापसी एवं रिफंड नीति | एवरीवेदा',
 'क्षतिग्रस्त उत्पाद मिलने पर 7 दिनों के भीतर त्वरित बदलाव और रिफंड की सुविधा।'),

(4, 'en', 'Privacy Policy',
 '<h2>Your Privacy Matters</h2><p>EveryVeda Organics Pvt Ltd operates EveryVeda.com. We strictly protect customer personal information, shipping addresses, contact numbers, and purchase history. We do not sell, rent, or trade your personal data to third-party marketing firms.</p><p>All payment transactions are encrypted and processed through RBI-authorized payment aggregators (Razorpay). We never capture, store, or process raw credit/debit card numbers or CVVs on our servers.</p>',
 'Privacy Policy | EveryVeda India',
 'Learn how EveryVeda protects your personal data, transaction security, and cookies.'),

(4, 'hi', 'गोपनीयता नीति (Privacy Policy)',
 '<h2>आपकी व्यक्तिगत जानकारी की सुरक्षा</h2><p>एवरीवेदा आपकी निजी जानकारी, मोबाइल नंबर और पते की पूर्ण सुरक्षा करता है। हम आपका डाटा किसी भी तीसरे पक्ष को नहीं बेचते। भुगतान प्रक्रिया भारतीय रिज़र्व बैंक (RBI) द्वारा अधिकृत रेज़रपे के माध्यम से सुरक्षित रूप से संपन्न होती है।</p>',
 'गोपनीयता नीति | एवरीवेदा',
 'आपकी व्यक्तिगत जानकारी और भुगतान सुरक्षा के प्रति एवरीवेदा की प्रतिबद्धता।'),

(5, 'en', 'Terms and Conditions',
 '<h2>Terms of Website Use</h2><p>By browsing, creating an account, or placing an order on EveryVeda.com, you agree to comply with our Terms of Service governed by the laws of India. All prices listed on the site are in Indian Rupees (INR) inclusive of applicable GST.</p><p>We reserve the right to cancel orders in circumstances of pricing glitches, inventory stockouts, or unverified contact information with prompt full refund.</p>',
 'Terms & Conditions | EveryVeda',
 'Website terms of service, payment agreements, and order policies.'),

(5, 'hi', 'नियम और शर्तें',
 '<h2>वेबसाइट उपयोग के नियम</h2><p>एवरीवेदा.कॉम पर ऑर्डर करने अथवा ब्राउज़ करने पर आप हमारे नियमों और शर्तों से सहमत होते हैं। वेबसाइट पर सभी कीमतें भारतीय रुपये (₹) में और जीएसटी सहित प्रदर्शित हैं।</p>',
 'नियम और शर्तें | एवरीवेदा',
 'वेबसाइट उपयोग, भुगतान एवं ऑर्डर संबंधी नियम और शर्तें।'),

(6, 'en', 'Medical & Wellness Disclaimer',
 '<h2>Ayurvedic Supplement Disclaimer</h2><p>The statements and information on EveryVeda.com have not been evaluated by the Food and Drug Administration or any government medical authority. The products sold on this website are traditional Ayurvedic formulations and dietary wellness supplements. They are not intended to diagnose, treat, cure, or prevent any medical condition or disease.</p><p>Individual results may vary depending on physiological constitution (Prakriti), lifestyle, and dietary habits. If you are pregnant, nursing, taking prescription medications, or under medical supervision, please consult a qualified Ayurvedic physician or medical professional before beginning any new wellness routine.</p>',
 'Medical Disclaimer | EveryVeda',
 'Important Ayurvedic wellness and medical disclaimer regarding dietary wellness products.'),

(6, 'hi', 'चिकित्सकीय एवं स्वास्थ्य अस्वीकरण (Medical Disclaimer)',
 '<h2>आयुर्वेदिक कल्याण अस्वीकरण</h2><p>एवरीवेदा.कॉम पर दी गई जानकारी और उत्पाद किसी भी बीमारी का निदान, उपचार अथवा रोकथाम करने के दावे के साथ नहीं बेचे जाते। ये पारंपरिक आयुर्वेदिक हर्बल उत्पाद एवं प्राकृतिक स्वास्थ्य पूरक हैं।</p><p>प्रत्येक व्यक्ति की शारीरिक प्रकृति (वात, पित्त, कफ) भिन्न होती है, अतः परिणाम अलग-अलग हो सकते हैं। किसी भी विशेष स्वास्थ्य समस्या, गर्भावस्था अथवा नियमित दवाइयों के सेवन की स्थिति में योग्य वैद्य या चिकित्सक से सलाह अवश्य लें।</p>',
 'स्वास्थ्य अस्वीकरण | एवरीवेदा',
 'पारंपरिक आयुर्वेदिक उत्पादों और स्वास्थ्य पूरक के संबंध में महत्वपूर्ण अस्वीकरण।');

-- 12. Blog Posts
INSERT INTO `blog_posts` (`id`, `slug`, `featured_image`, `author_name`, `is_published`, `published_at`) VALUES
(1, 'daily-triphala-benefits-digestive-health', 'blog-triphala.svg', 'Vaidya Ramesh Sharma', 1, '2026-09-15 10:00:00'),
(2, 'understanding-pure-himalayan-shilajit', 'blog-shilajit.svg', 'EveryVeda Research Team', 1, '2026-09-28 11:30:00'),
(3, 'ayurvedic-hair-oiling-rituals-bhringraj', 'blog-hairoil.svg', 'Dr. Meera Nambiar', 1, '2026-10-05 09:15:00');

INSERT INTO `blog_translations` (`blog_post_id`, `locale`, `title`, `summary`, `content`, `meta_title`, `meta_description`) VALUES
(1, 'en', 'Top 5 Benefits of Daily Triphala for Digestive Wellness',
 'Discover how the ancient classical trio of Amalaki, Haritaki, and Bibhitaki gently balances your gut microbiome and digestion.',
 '<p>Triphala, translated literally as "three fruits", is revered as one of the most foundational formulas in Ayurvedic medicine. Composed of Amla (nourishing Rasayana), Haritaki (cleansing), and Bibhitaki (soothing), Triphala is uniquely tridoshic—meaning it balances Vata, Pitta, and Kapha.</p><h3>1. Natural Bowel Regulation</h3><p>Unlike harsh laxatives that irritate the colon wall, Triphala gently tones gastrointestinal musculature, promoting natural peristalsis.</p><h3>2. Rich in Natural Bio-flavonoids</h3><p>Amla contains potent natural polyphenols that neutralize free radical oxidative stress.</p>',
 '5 Benefits of Triphala Churna | EveryVeda Wellness Blog',
 'Learn why daily intake of Triphala supports gut health, regular digestion, and gentle natural cleansing.'),

(1, 'hi', 'पाचन और उदर स्वास्थ्य के लिए त्रिफला के 5 प्रमुख लाभ',
 'जानिए कैसे आंवला, हरड़ और बहेड़ा का यह प्राचीन मेल आपकी पाचन अग्नि को संतुलित और पेट को स्वस्थ रखता है।',
 '<p>आयुर्वेद में त्रिफला को अमृत समान माना गया है। यह तीन चमत्कारी फलों—हरड़, बहेड़ा और आंवला का संतुलित मिश्रण है, जो वात, पित्त और कफ तीनों दोषों को शांत करता है।</p><h3>1. कब्ज और गैस से प्राकृतिक राहत</h3><p>त्रिफला आंतों को बिना कोई नुकसान पहुंचाए स्वाभाविक मल त्याग में सहायता करता है।</p><h3>2. शरीर से विषैले तत्वों की सफाई</h3><p>यह रक्त को शुद्ध करने और त्वचा में प्राकृतिक चमक लाने में भी सहायक है।</p>',
 'त्रिफला के 5 फायदे | एवरीवेदा स्वास्थ्य पत्रिका',
 'पाचन दुरुस्त रखने और शरीर को विषमुक्त करने के लिए त्रिफला के अद्भुत लाभ।'),

(2, 'en', 'Understanding Himalayan Shilajit: Purity, Testing & Ancient Science',
 'A deep dive into how authentic Shilajit resin is formed in high altitude mountains, purified, and tested for maximum fulvic acid potency.',
 '<p>Shilajit is known as the "Conqueror of Mountains and Destroyer of Weakness" in Sanskrit. Formed over centuries by the slow decomposition of medicinal plants, authentic Shilajit oozes from rocks in summer heat above 16,000 feet.</p><h3>Why Fulvic Acid Matters</h3><p>Fulvic acid acts as a natural cellular transporter, delivering trace minerals directly across cell membranes to nourish mitochondria.</p>',
 'Himalayan Shilajit Guide & Benefits | EveryVeda Blog',
 'Comprehensive guide on Shilajit purity, fulvic acid content, and how to spot authentic resin.'),

(2, 'hi', 'शुद्ध हिमालयी शिलाजीत की पहचान, उपयोग और वैज्ञानिक महत्व',
 'जानिए कैसे 16,000 फीट की ऊंचाई से प्राप्त असली शिलाजीत पौरुष बल और शारीरिक स्फूर्ति को बढ़ाता है।',
 '<p>संस्कृत में शिलाजीत का अर्थ है "पर्वतों का विजेता"। सदियों से हिमालय की चट्टानों में संचित वनस्पतियों का यह सार तत्व शरीर को अपार शक्ति और ऊर्जा प्रदान करता है।</p><h3>फुल्विक एसिड की महत्ता</h3><p>शुद्ध शिलाजीत में 80% से अधिक फुल्विक एसिड होता है जो कोशिकाओं तक आवश्यक खनिज पहुंचाता है।</p>',
 'शुद्ध शिलाजीत की पहचान और लाभ | एवरीवेदा ब्लॉग',
 'हिमालयी शिलाजीत की शुद्धता, सेवन विधि और ऊर्जा वर्धक गुणों की सम्पूर्ण जानकारी।'),

(3, 'en', 'The Ancient Art of Shiro Abhyanga: Bhringraj Hair Care Rituals',
 'How regular head massage with herbal Kshirapak oils calms the nervous system and supports healthy hair roots.',
 '<p>In Ayurveda, the head is described as the root of the tree of the human body (Uttamanga). Massaging the scalp with warm Bhringraj and Brahmi oil stimulates marma points, relieves mental stress, and promotes deep restorative sleep.</p>',
 'Ayurvedic Hair Oiling Guide | EveryVeda Blog',
 'Traditional Ayurvedic scalp massage rituals with Bhringraj for strong, lustrous hair.'),

(3, 'hi', 'शिरो अभ्यंग की प्राचीन कला: भृंगराज तैल से बालों की देखभाल',
 'सिर की नियमित मालिश कैसे बालों की जड़ों को मजबूत करती है और मानसिक तनाव से राहत दिलाती है।',
 '<p>आयुर्वेद में सिर को शरीर रूपी वृक्ष की जड़ कहा गया है। गुनगुने भृंगराज और ब्राह्मी तेल से सिर की मालिश करने से नींद अच्छी आती है और बालों का असमय सफेद होना कम होता है।</p>',
 'आयुर्वेदिक हेयर केयर और भृंगराज तेल | एवरीवेदा',
 'स्वस्थ, घने और मजबूत बालों के लिए पारंपरिक आयुर्वेदिक तैल मालिश की विधि।');

-- 13. Settings (Key-Value)
INSERT INTO `settings` (`key`, `value`, `group_name`, `description`) VALUES
('site_name', 'EveryVeda', 'general', 'Brand Name'),
('site_tagline', 'Pure Ayurvedic & Wellness Living', 'general', 'Brand Tagline'),
('contact_email', 'support@everyveda.com', 'contact', 'Customer Support Email'),
('contact_phone', '+91 98765 43210', 'contact', 'Customer Support Phone'),
('whatsapp_number', '919876543210', 'contact', 'WhatsApp Support Number'),
('support_hours', 'Monday - Saturday: 9:00 AM - 7:00 PM IST', 'contact', 'Working Hours'),
('currency_symbol', '₹', 'localization', 'Currency Symbol'),
('currency_code', 'INR', 'localization', 'ISO Currency Code'),
('default_language', 'en', 'localization', 'Default Website Language'),
('tax_cgst_rate', '6.0', 'tax', 'Central GST percentage'),
('tax_sgst_rate', '6.0', 'tax', 'State GST percentage'),
('tax_igst_rate', '12.0', 'tax', 'Interstate GST percentage'),
('prices_include_tax', '1', 'tax', 'Whether product prices include GST (1 for Inclusive, 0 for Exclusive)'),
('shipping_fee_standard', '50.00', 'shipping', 'Standard Flat Delivery Charge'),
('shipping_free_threshold', '499.00', 'shipping', 'Minimum cart value for Free Shipping'),
('enable_cod', '1', 'payment', 'Enable Cash on Delivery (1 for Yes, 0 for No)'),
('cod_extra_charge', '0.00', 'payment', 'Additional fee for Cash on Delivery'),
('razorpay_mode', 'test', 'payment', 'Razorpay Environment (test or live)'),
('razorpay_key_id', 'rzp_test_placeholderKey123', 'payment', 'Razorpay Key ID'),
('razorpay_key_secret', 'placeholderSecretKey456', 'payment', 'Razorpay Key Secret'),
('razorpay_webhook_secret', 'webhookSecret789', 'payment', 'Razorpay Webhook Secret'),
('company_name', 'EveryVeda Organics Pvt Ltd', 'legal', 'Registered Company Legal Name'),
('company_address', 'EveryVeda Organics Pvt Ltd, Sector 62, Noida, Uttar Pradesh 201309, India', 'legal', 'Company Registered Office Address'),
('gstin', '07AAAAE1234P1Z5', 'legal', 'GST Identification Number'),
('fssai_license', '12723999000123', 'legal', 'FSSAI Manufacturing License'),
('ayush_license', 'AYUSH-UP-2023-889', 'legal', 'State Ayush Manufacturing License Number'),
('google_analytics_id', '', 'analytics', 'GA4 Measurement ID (e.g. G-XXXXXXXXXX)'),
('low_stock_notification_email', 'inventory@everyveda.com', 'inventory', 'Email for stock alerts');

SET FOREIGN_KEY_CHECKS = 1;
