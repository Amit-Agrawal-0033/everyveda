<?php
/**
 * Update Product Claims and Titles for FSSAI / AYUSH Compliance
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

echo "Updating product claims and terminology for statutory compliance...\n";

// 1. Update Product #3 slug and title (Wild-Harvested Forest Amla Juice)
Database::update('products', [
    'slug' => 'wild-harvested-forest-amla-juice',
    'updated_at' => date('Y-m-d H:i:s')
], "id = 3");

// English translation for Product #3
Database::update('product_translations', [
    'name' => 'EveryVeda Wild-Harvested Forest Amla Juice (1000ml)',
    'short_description' => 'Cold-pressed wild Indian Gooseberry (Amla) juice naturally rich in Vitamin C, bio-antioxidants, and digestive enzymes.',
    'description' => '<p>EveryVeda Wild-Harvested Amla Juice is cold-pressed directly from fresh, mature amla hand-gathered from native Indian forest reserves. It is crafted without artificial flavorings, synthetic citric acid, added sugars, or water dilution.</p><p>Supports natural digestive harmony, helps maintain healthy metabolic functions, and contributes to natural skin radiance as described in classical Ayurvedic texts.</p>',
    'ingredients' => 'Wild Forest Amla (Phyllanthus emblica) Fruit Juice 99.8%, Permitted Class II Preservatives (Sodium Benzoate & Potassium Sorbate) 0.2%.',
    'meta_title' => 'Wild-Harvested Forest Amla Juice 1000ml | EveryVeda India',
    'meta_description' => '100% pure cold-pressed wild forest amla juice without added sugar or water. Sourced from native reserves. Fast delivery across India.'
], "product_id = 3 AND locale = 'en'");

// Hindi translation for Product #3
Database::update('product_translations', [
    'name' => 'एवरीवेदा जंगली आंवला रस (1000 मिली)',
    'short_description' => 'प्राकृतिक वनों से संकलित ताजे आंवलों से निर्मित कोल्ड-प्रेस्ड रस, जो प्राकृतिक विटामिन सी और एंटीऑक्सीडेंट से समृद्ध है।',
    'description' => '<p>एवरीवेदा आंवला रस प्राकृतिक वनों से एकत्रित आंवलों के पहले अर्क (कोल्ड-प्रेस) से तैयार किया जाता है। इसमें किसी भी प्रकार का कृत्रिम रंग, रिफाइंड चीनी या अतिरिक्त पानी नहीं मिलाया जाता।</p><p>यह पाचन तंत्र को सुदृढ़ रखने, प्राकृतिक मेटाबॉलिज्म को सहारा देने और त्वचा के प्राकृतिक स्वास्थ्य में सहायक है।</p>',
    'meta_title' => 'जंगली आंवला रस 1000 मिली खरीदें | एवरीवेदा',
    'meta_description' => 'बिना मिलावट का विशुद्ध प्राकृतिक जंगली आंवला रस। पाचन और जीवन शक्ति के लिए उत्तम।'
], "product_id = 3 AND locale = 'hi'");

// 2. Refine Joint Oil (Product #4) claims to be strictly compliant with Drugs & Magic Remedies Act
Database::update('product_translations', [
    'short_description' => 'Classical Sahasrayogam taila prepared with 30+ herbs in pure sesame oil for soothing muscular comfort and joint mobility.',
    'description' => '<p>EveryVeda Maha Narayan Taila is formulated according to the authentic Sahasrayogam principles. Simmered over low flame with Bilva, Ashwagandha, Bala, and rejuvenating botanicals infused in cold-pressed black sesame oil.</p><p>Regular gentle application provides soothing warmth to tired joints, eases routine muscular stiffness, and supports healthy flexibility after strenuous physical exertion.</p>'
], "product_id = 4 AND locale = 'en'");

// 3. Update Shilajit (Product #2) description to highlight NABL batch-tested parameters
Database::update('product_translations', [
    'short_description' => '100% natural, laboratory-tested Grade-A Himalayan Shilajit resin containing standardized Fulvic Acid and natural trace minerals.',
    'description' => '<p>Sourced from high-altitude Himalayan rock fissures above 16,000 feet, EveryVeda Shilajit resin undergoes traditional Shodhana (water purification) with Triphala decoction.</p><p>Rich in naturally occurring fulvic acid and organic trace minerals. Supports natural cellular vitality, endurance, and everyday stamina.</p>'
], "product_id = 2 AND locale = 'en'");

echo "Compliance updates completed successfully!\n";
