<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\FarmerStory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Organization;
use App\Models\Seed;
use App\Models\SeedCategory;
use App\Models\SeedPhoto;
use App\Models\SeedSubmission;
use App\Models\ShopProduct;
use App\Models\ShopProductCategory;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users & Roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@apnikheti.org'],
            [
                'name' => 'Amrit Singh Chahal',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@apnikheti.org'],
            [
                'name' => 'Seed Reviewer',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
            ]
        );

        $editor = User::firstOrCreate(
            ['email' => 'editor@apnikheti.org'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('password'),
                'role' => 'seed_editor',
            ]
        );

        // 2. Site Settings (Founder Bio, Slogans, Statistics, Contact details)
        $settings = [
            ['key' => 'site_name_en', 'value' => 'Apni Kheti Apne Beej', 'group' => 'general'],
            ['key' => 'site_name_pa', 'value' => 'ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ', 'group' => 'general'],
            ['key' => 'site_name_hi', 'value' => 'अपनी खेती अपने बीज', 'group' => 'general'],
            ['key' => 'slogan_en', 'value' => 'Our Farming. Our Seeds. Our Future.', 'group' => 'general'],
            ['key' => 'slogan_pa', 'value' => 'ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ', 'group' => 'general'],
            ['key' => 'slogan_hi', 'value' => 'अपनी खेती अपने बीज', 'group' => 'general'],
            ['key' => 'founder_name', 'value' => 'Amrit Singh Chahal', 'group' => 'about'],
            ['key' => 'founder_title', 'value' => 'Seed Conservationist & Natural Farming Advocate', 'group' => 'about'],
            ['key' => 'founder_bio', 'value' => 'Amrit Singh Chahal is dedicated to preserving traditional seeds, promoting natural farming techniques, and protecting traditional agricultural heritage across Punjab and North India. Working alongside farmers and local organizations, he has documented and preserved numerous indigenous crop varieties to ensure food sovereignty and seed security.', 'group' => 'about'],
            ['key' => 'verified_seed_count', 'value' => '500+', 'group' => 'stats'],
            ['key' => 'display_verified_stat', 'value' => '1', 'group' => 'stats'],
            ['key' => 'contact_email', 'value' => 'info@apnikheti.org', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Punjab, India', 'group' => 'contact'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/apnikhetiapnebeej', 'group' => 'social'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@apnikhetiapnebeej', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 3. Seed Categories
        $categoriesData = [
            [
                'slug' => 'wheat',
                'name_en' => 'Wheat (ਕਣਕ)',
                'name_pa' => 'ਕਣਕ (Wheat)',
                'name_hi' => 'गेहूं (Wheat)',
                'description_en' => 'Traditional, landrace, and indigenous wheat varieties of Punjab and surrounding areas.',
                'description_pa' => 'ਪੰਜਾਬ ਅਤੇ ਆਲੇ-ਦੁਆਲੇ ਦੇ ਖੇਤਰਾਂ ਦੀਆਂ ਰਵਾਇਤੀ ਅਤੇ ਦੇਸੀ ਕਣਕ ਦੀਆਂ ਕਿਸਮਾਂ।',
                'sort_order' => 1,
            ],
            [
                'slug' => 'rice',
                'name_en' => 'Rice & Basmati (ਝੋਨਾ / ਬਾਸਮਤੀ)',
                'name_pa' => 'ਝੋਨਾ / ਬਾਸਮਤੀ',
                'name_hi' => 'चावल / बासमती',
                'description_en' => 'Traditional aromatic rice and heritage basmati seeds.',
                'description_pa' => 'ਰਵਾਇਤੀ ਖੁਸ਼ਬੂਦਾਰ ਚੌਲ ਅਤੇ ਦੇਸੀ ਬਾਸਮਤੀ ਕਿਸਮਾਂ।',
                'sort_order' => 2,
            ],
            [
                'slug' => 'maize',
                'name_en' => 'Maize (ਮੱਕੀ)',
                'name_pa' => 'ਮੱਕੀ (Maize)',
                'name_hi' => 'मक्का (Maize)',
                'description_en' => 'Open-pollinated traditional corn and maize landraces.',
                'description_pa' => 'ਰਵਾਇਤੀ ਦੇਸੀ ਮੱਕੀ ਦੀਆਂ ਕਿਸਮਾਂ।',
                'sort_order' => 3,
            ],
            [
                'slug' => 'millets',
                'name_en' => 'Millets (ਬਾਜਰਾ / ਮਿਲਟਸ)',
                'name_pa' => 'ਬਾਜਰਾ / ਮਿਲਟਸ',
                'name_hi' => 'बाजरा / मिलेट्स',
                'description_en' => 'Nutri-cereals including Pearl Millet (Bajra), Foxtail Millet (Kangni), and Finger Millet.',
                'description_pa' => 'ਦੇਸੀ ਬਾਜਰਾ, ਕੰਗਣੀ ਅਤੇ ਹੋਰ ਪੌਸ਼ਟਿਕ ਮਿਲਟਸ।',
                'sort_order' => 4,
            ],
            [
                'slug' => 'pulses',
                'name_en' => 'Pulses & Legumes (ਦਾਲਾਂ)',
                'name_pa' => 'ਦਾਲਾਂ (Pulses)',
                'name_hi' => 'दालें (Pulses)',
                'description_en' => 'Traditional black gram (maah), green gram (moong), chickpea (chana), and lentils.',
                'description_pa' => 'ਦੇਸੀ ਮਾਂਹ, ਮੂੰਗੀ, ਛੋਲੇ ਅਤੇ ਮਸਰ।',
                'sort_order' => 5,
            ],
            [
                'slug' => 'oilseeds',
                'name_en' => 'Oilseeds (ਤੇਲਬੀਜ - ਸਰ੍ਹੋਂ / ਤਿਲ)',
                'name_pa' => 'ਤੇਲਬੀਜ (ਸਰ੍ਹੋਂ / ਤਿਲ)',
                'name_hi' => 'तिलहन (सरसों / तिल)',
                'description_en' => 'Traditional mustard, sesame, and flax seeds.',
                'description_pa' => 'ਦੇਸੀ ਸਰ੍ਹੋਂ, ਤਿਲ ਅਤੇ ਅਲਸੀ ਦੇ ਬੀਜ।',
                'sort_order' => 6,
            ],
            [
                'slug' => 'vegetables',
                'name_en' => 'Vegetables (ਸਬਜ਼ੀਆਂ)',
                'name_pa' => 'ਸਬਜ਼ੀਆਂ',
                'name_hi' => 'सब्जियां',
                'description_en' => 'Desi vegetable varieties of gourds, brinjal, radish, spinach, and chillies.',
                'description_pa' => 'ਦੇਸੀ ਸਬਜ਼ੀਆਂ ਦੇ ਬੀਜ।',
                'sort_order' => 7,
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[$catData['slug']] = SeedCategory::firstOrCreate(['slug' => $catData['slug']], $catData);
        }

        // 4. Traditional Seed Records
        $seedsData = [
            [
                'seed_category_id' => $categories['wheat']->id,
                'slug' => 'bansi-wheat-desi',
                'name_pa' => 'ਬੰਸੀ ਕਣਕ (Bansi Wheat)',
                'name_en' => 'Desi Bansi Wheat',
                'name_hi' => 'बंसी गेहूं (Bansi Wheat)',
                'local_names' => 'Bansi Kanak, Desi Wheat, Red Wheat',
                'scientific_name' => 'Triticum durum landrace',
                'description_en' => 'Bansi wheat is an ancient traditional durum variety prized for its rich amber colour, nutty flavour, low glycemic index, and high dietary fibre. Requires minimal chemical inputs.',
                'description_pa' => 'ਬੰਸੀ ਕਣਕ ਇੱਕ ਪ੍ਰਾਚੀਨ ਦੇਸੀ ਕਿਸਮ ਹੈ ਜੋ ਆਪਣੇ ਸੁਆਦ, ਸੁਨਹਿਰੀ ਰੰਗ ਅਤੇ ਸਿਹਤਮੰਦ ਗੁਣਾਂ ਲਈ ਜਾਣੀ ਜਾਂਦੀ ਹੈ। ਇਸਨੂੰ ਰਸਾਇਣਕ ਖਾਦਾਂ ਦੀ ਬਹੁਤ ਘੱਟ ਲੋੜ ਹੁੰਦੀ ਹੈ।',
                'description_hi' => 'बंसी गेहूं एक प्राचीन पारंपरिक किस्म है जो अपने स्वाद और स्वास्थ्य गुणों के लिए प्रसिद्ध है।',
                'region_of_origin' => 'Punjab / Central India',
                'growing_season' => 'Rabi (Winter)',
                'sowing_period' => 'October - November',
                'soil_preferences' => 'Loamy, well-drained fertile soil with organic matter',
                'water_requirements' => 'Requires 2-3 rain-fed or light irrigations; drought tolerant',
                'crop_duration' => '135 - 145 days',
                'traditional_uses' => 'Stone-ground whole wheat flour for traditional rotis, daliya (broken wheat), and bati.',
                'culinary_uses' => 'Nutritious rotis that stay soft for long hours, halwa, and porridge.',
                'farmer_observations' => 'Demonstrates strong resistance to rust and extreme heat spells during grain filling.',
                'preservation_methods' => 'Store dried grains in earthen pots with neem leaves and clean ash.',
                'is_featured' => true,
                'is_verified' => true,
                'publication_status' => 'published',
                'contributor_name' => 'Amrit Singh Chahal',
            ],
            [
                'seed_category_id' => $categories['wheat']->id,
                'slug' => 'sona-moti-wheat',
                'name_pa' => 'ਸੋਨਾ ਮੋਤੀ ਕਣਕ (Sona Moti Wheat)',
                'name_en' => 'Sona Moti Ancient Wheat',
                'name_hi' => 'सोना मोती गेहूं',
                'local_names' => 'Moti Kanak, Round Wheat, Sona Moti',
                'scientific_name' => 'Triticum sphaerococcum',
                'description_en' => 'Sona Moti (Indian Dwarf Wheat) produces tiny round pearl-like grains. It is rich in folic acid, high mineral content, and naturally low gluten content, making it easily digestible.',
                'description_pa' => 'ਸੋਨਾ ਮੋਤੀ ਕਣਕ ਦੇ ਬੀਜ ਮੋਤੀਆਂ ਵਰਗੇ ਗੋਲ ਹੁੰਦੇ ਹਨ। ਇਹ ਫੋਲਿਕ ਐਸਿਡ ਅਤੇ ਖਣਿਜਾਂ ਨਾਲ ਭਰਪੂਰ ਹੁੰਦੀ ਹੈ।',
                'description_hi' => 'सोना मोती के दाने मोती जैसे गोल होते हैं और यह स्वास्थ्य के लिए अत्यंत लाभदायक है।',
                'region_of_origin' => 'Indus Valley Heritage / Punjab',
                'growing_season' => 'Rabi (Winter)',
                'sowing_period' => 'November 1st fortnight',
                'soil_preferences' => 'Clay loams and sandy loams',
                'water_requirements' => 'Moderate water requirement; responds well to jeevamrut and organic mulching',
                'crop_duration' => '140 days',
                'traditional_uses' => 'Traditional medicinal diets, rotis for elders, and health mixes.',
                'culinary_uses' => 'Healthy artisanal rotis, traditional sweets, and bakery goods.',
                'farmer_observations' => 'Height reaches up to 4.5 feet; sturdy straw used for cattle fodder.',
                'preservation_methods' => 'Sun dry to under 10% moisture and store in sealed traditional bamboo containers.',
                'is_featured' => true,
                'is_verified' => true,
                'publication_status' => 'published',
                'contributor_name' => 'Amrit Singh Chahal',
            ],
            [
                'seed_category_id' => $categories['rice']->id,
                'slug' => 'traditional-desi-basmati',
                'name_pa' => 'ਦੇਸੀ ਬਾਸਮਤੀ (Desi Basmati)',
                'name_en' => 'Desi Heritage Basmati',
                'name_hi' => 'देशी बासमती',
                'local_names' => 'Heritage Basmati, 386 Basmati',
                'scientific_name' => 'Oryza sativa basmati',
                'description_en' => 'Traditional long-grained tall basmati variety known for its characteristic rich natural aroma that fills the field before harvest.',
                'description_pa' => 'ਰਵਾਇਤੀ ਉੱਚੀ ਦੇਸੀ ਬਾਸਮਤੀ ਜੋ ਆਪਣੀ ਸੁਭਾਵਿਕ ਖੁਸ਼ਬੂ ਅਤੇ ਲੰਬੇ ਚਾਵਲਾਂ ਲਈ ਪ੍ਰਸਿੱਧ ਹੈ।',
                'description_hi' => 'पारंपरिक सुगंधित बासमती चावल की प्राचीन किस्म।',
                'region_of_origin' => 'Gurdaspur / Amritsar belt, Punjab',
                'growing_season' => 'Kharif (Monsoon)',
                'sowing_period' => 'June Nursery, July transplanting',
                'soil_preferences' => 'Clay loam with good moisture retention',
                'water_requirements' => 'Needs rain or natural field moisture; minimal artificial water logging needed after establishment',
                'crop_duration' => '145 - 155 days',
                'traditional_uses' => 'Festive kheer, traditional pilaf, and wedding feasts.',
                'culinary_uses' => 'Fragrant steamed rice, biryani, and festive dishes.',
                'farmer_observations' => 'Resistant to major insect pests when grown with bio-inputs.',
                'preservation_methods' => 'Paddy seed aged 1 year improves fragrance before sowing.',
                'is_featured' => true,
                'is_verified' => true,
                'publication_status' => 'published',
                'contributor_name' => 'Amrit Singh Chahal',
            ],
            [
                'seed_category_id' => $categories['millets']->id,
                'slug' => 'kangni-foxtail-millet',
                'name_pa' => 'ਕੰਗਣੀ (Kangni / Foxtail Millet)',
                'name_en' => 'Kangni (Foxtail Millet)',
                'name_hi' => 'कंगनी (Foxtail Millet)',
                'local_names' => 'Kangni, Kakum',
                'scientific_name' => 'Setaria italica',
                'description_en' => 'Ancient drought-hardy millet variety grown traditionally in rain-fed regions of Punjab. Highly nutritious and low GI.',
                'description_pa' => 'ਘੱਟ ਪਾਣੀ ਵਿੱਚ ਹੋਣ ਵਾਲਾ ਪੁਰਾਤਨ ਮਿਲਟ ਜੋ ਬਹੁਤ ਹੀ ਪੌਸ਼ਟਿਕ ਹੈ।',
                'description_hi' => 'कम पानी में उगने वाला पारंपरिक पोषक अनाज।',
                'region_of_origin' => 'Malwa region, Punjab',
                'growing_season' => 'Kharif',
                'sowing_period' => 'July',
                'soil_preferences' => 'Light sandy to loamy soil',
                'water_requirements' => 'Very low; completely rainfed after germination',
                'crop_duration' => '85 - 90 days',
                'traditional_uses' => 'Kheer, porridge, and winter flatbreads.',
                'culinary_uses' => 'Millet upma, khichdi, and healthy snacks.',
                'farmer_observations' => 'Requires zero synthetic fertilizer; naturally deters pests.',
                'preservation_methods' => 'Sun dry thoroughly and store in sealed metal bins with dried clove buds.',
                'is_featured' => false,
                'is_verified' => true,
                'publication_status' => 'published',
                'contributor_name' => 'Farmer Community Member',
            ],
            [
                'seed_category_id' => $categories['pulses']->id,
                'slug' => 'desi-kala-chana',
                'name_pa' => 'ਦੇਸੀ ਕਾਲੇ ਛੋਲੇ (Desi Black Chickpea)',
                'name_en' => 'Desi Black Chickpea (Kala Chana)',
                'name_hi' => 'देशी काला चना',
                'local_names' => 'Desi Chana, Kala Chana',
                'scientific_name' => 'Cicer arietinum',
                'description_en' => 'Small, dark brown traditional chickpea variety rich in protein and iron. Excellent soil nitrogen fixer.',
                'description_pa' => 'ਛੋਟੇ ਦਾਣਿਆਂ ਵਾਲੇ ਦੇਸੀ ਕਾਲੇ ਛੋਲੇ ਜੋ ਪ੍ਰੋਟੀਨ ਅਤੇ ਆਇਰਨ ਨਾਲ ਭਰਪੂਰ ਹਨ। ਖੇਤ ਦੀ ਉਪਜਾਊ ਸ਼ਕਤੀ ਵਧਾਉਂਦੇ ਹਨ।',
                'description_hi' => 'प्रोटीन और आयरन से भरपूर देशी काला चना।',
                'region_of_origin' => 'Punjab Plain',
                'growing_season' => 'Rabi',
                'sowing_period' => 'October - November',
                'soil_preferences' => 'Well-drained light soil',
                'water_requirements' => 'Requires 1-2 light irrigations',
                'crop_duration' => '130 - 140 days',
                'traditional_uses' => 'Traditional Punjabi chana curry, roasted chana, and sattu.',
                'culinary_uses' => 'Sprouted salads, curries, and sattu flour.',
                'farmer_observations' => 'Nodules fix nitrogen into soil, boosting subsequent crop yields.',
                'preservation_methods' => 'Mix dried seeds with small quantity of mustard oil before storing.',
                'is_featured' => false,
                'is_verified' => true,
                'publication_status' => 'published',
                'contributor_name' => 'Amrit Singh Chahal',
            ],
        ];

        foreach ($seedsData as $seedData) {
            $seed = Seed::firstOrCreate(['slug' => $seedData['slug']], $seedData);
            SeedPhoto::firstOrCreate(
                ['seed_id' => $seed->id],
                [
                    'photo_path' => 'images/seeds/' . $seed->slug . '.jpg',
                    'caption' => $seed->name_en . ' preserved seed sample',
                ]
            );
        }

        // 5. Article Categories & Organic Farming Knowledge Center
        $artCatFarming = ArticleCategory::firstOrCreate(
            ['slug' => 'organic-natural-farming'],
            [
                'name_en' => 'Organic & Natural Farming',
                'name_pa' => 'ਕੁਦਰਤੀ ਖੇਤੀ ਤਕਨੀਕਾਂ',
                'name_hi' => 'प्राकृतिक खेती तकनीक',
                'description' => 'Guides on soil enrichment, bio-formulations, vermicomposting, and pest control.',
            ]
        );

        $artCatSeedSaving = ArticleCategory::firstOrCreate(
            ['slug' => 'seed-conservation-guides'],
            [
                'name_en' => 'Seed Conservation & Preservation',
                'name_pa' => 'ਬੀਜ ਸੰਭਾਲ ਅਤੇ ਸੁਰੱਖਿਆ',
                'name_hi' => 'बीज संरक्षण निर्देशिका',
                'description' => 'Techniques for seed selection, harvesting, drying, and storage.',
            ]
        );

        Article::firstOrCreate(
            ['slug' => 'soil-health-jeevamrut-preparation'],
            [
                'article_category_id' => $artCatFarming->id,
                'title_en' => 'Building Soil Health with Traditional Jeevamrut & Ghanjeevamrut',
                'title_pa' => 'ਜੀਵਾਮ੍ਰਿਤ ਅਤੇ ਘਨਜੀਵਾਮ੍ਰਿਤ ਨਾਲ ਜ਼ਮੀਨ ਦੀ ਉਪਜਾਊ ਸ਼ਕਤੀ ਵਧਾਉਣਾ',
                'title_hi' => 'जीवामृत और घनजीवामृत से मृदा स्वास्थ्य सुधार',
                'excerpt_en' => 'Learn how cow-dung based microbial formulations regenerate degraded soils, increase earthworm activity, and restore natural soil biology.',
                'excerpt_pa' => 'ਦੇਸੀ ਗਾਂ ਦੇ ਗੋਬਰ ਅਤੇ ਗਊ-ਮੂਤਰ ਨਾਲ ਤਿਆਰ ਜੀਵਾਮ੍ਰਿਤ ਰਾਹੀਂ ਜ਼ਮੀਨ ਦੇ ਮਿੱਤਰ ਕੀੜਿਆਂ ਅਤੇ ਜੀਵਾਣੂਆਂ ਨੂੰ ਮੁੜ ਜੀਵਤ ਕਰਨ ਦਾ ਤਰੀਕਾ।',
                'content_en' => "Soil is not dead dirt; it is a living ecosystem filled with millions of beneficial microorganisms. Chemical fertilizers destroy these beneficial organisms over time, leading to soil compaction and water retention loss.\n\n### Preparing Jeevamrut\n1. 10 kg Desi Cow Dung\n2. 10 Liters Desi Cow Urine\n3. 2 kg Jaggery (Gud)\n4. 2 kg Besan (Gram flour)\n5. 1 Handful of fertile forest soil\n\nMix all ingredients in a 200-liter drum filled with water. Stir clockwise twice a day for 48-72 hours. Apply through irrigation or as a soil spray.",
                'content_pa' => "ਮਿੱਟੀ ਇੱਕ ਜੀਵੰਤ ਪ੍ਰਣਾਲੀ ਹੈ। ਰਸਾਇਣਕ ਖਾਦਾਂ ਨਾਲ ਮਿੱਟੀ ਦੇ ਮਿੱਤਰ ਜੀਵਾਣੂ ਖਤਮ ਹੋ ਜਾਂਦੇ ਹਨ। ਜੀਵਾਮ੍ਰਿਤ ਰਾਹੀਂ ਅਸੀਂ ਮਿੱਟੀ ਵਿੱਚ ਜੈਵਿਕ ਮਾਦਾ ਵਧਾ ਸਕਦੇ ਹਾਂ।",
                'author_name' => 'Amrit Singh Chahal',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        Article::firstOrCreate(
            ['slug' => 'traditional-seed-selection-and-storage-methods'],
            [
                'article_category_id' => $artCatSeedSaving->id,
                'title_en' => 'Traditional Methods of Seed Selection and Storage in Punjab',
                'title_pa' => 'ਪੰਜਾਬ ਵਿੱਚ ਬੀਜਾਂ ਦੀ ਚੋਣ ਅਤੇ ਸੰਭਾਲ ਦੇ ਦੇਸੀ ਤਰੀਕੇ',
                'title_hi' => 'पारंपरिक बीज चयन और भंडारण की तकनीकें',
                'excerpt_en' => 'Selecting the healthiest plants in the middle of the field and preserving them using neem leaves, clean ash, and earthen vessels.',
                'excerpt_pa' => 'ਖੇਤ ਦੇ ਵਿਚਕਾਰਲੇ ਹਿੱਸੇ ਵਿੱਚੋਂ ਤੰਦਰੁਸਤ ਬੂਟਿਆਂ ਦੀ ਚੋਣ ਕਰਕੇ ਨਿੰਮ ਦੇ ਪੱਤਿਆਂ ਅਤੇ ਸਵਾਹ ਨਾਲ ਬੀਜ ਸਾਂਭਣ ਦੇ ਪਰੰਪਰਾਗਤ ਢੰਗ।',
                'content_en' => "Seed selection begins right in the standing crop before harvest. Always select seeds from the center of healthy, disease-free field patches.\n\n### Storage Guidelines\n- Thoroughly sun-dry seeds until moisture content drops below 9-10%.\n- Use dry neem leaves at the bottom and top of storage containers.\n- Mix wood ash with pulse seeds to protect against weevils.",
                'author_name' => 'Amrit Singh Chahal',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        // 6. Farmer Stories
        FarmerStory::firstOrCreate(
            ['farmer_name' => 'Sukhdev Singh', 'district' => 'Faridkot'],
            [
                'village' => 'Chahal',
                'crop_name' => 'Desi Bansi Wheat',
                'farming_practice' => 'Zero Chemical Natural Farming',
                'description' => 'I have been growing Desi Bansi wheat for the past 5 years without any synthetic spray. The wheat flour tastes natural, rotis remain soft all day, and my soil earthworm count has increased multifold.',
                'status' => 'published',
                'display_consent' => true,
            ]
        );

        FarmerStory::firstOrCreate(
            ['farmer_name' => 'Gurmeet Kaur', 'district' => 'Bathinda'],
            [
                'village' => 'Rampura',
                'crop_name' => 'Traditional Desi Vegetable Kitchen Garden',
                'farming_practice' => 'Organic Composting & Native Seeds',
                'description' => 'We saved native seeds of bottle gourd, ridge gourd, brinjal, and mustard. Using home compost and neem oil sprays, our family consumes 100% chemical-free fresh vegetables throughout the year.',
                'status' => 'published',
                'display_consent' => true,
            ]
        );

        // 7. Organizations & Collaborators
        Organization::firstOrCreate(
            ['name' => 'Kheti Virasat Mission'],
            [
                'description' => 'Promoting organic farming, natural seed conservation, and environmental health across Punjab.',
                'website_url' => 'https://khetivirasat.org',
                'collaboration_type' => 'Seed Conservation & Workshops',
                'projects_summary' => 'Joint seed exchange melas and natural farming awareness workshops for regional farmers.',
                'is_published' => true,
            ]
        );

        Organization::firstOrCreate(
            ['name' => 'Alliance for Sustainable & Holistic Agriculture (ASHA)'],
            [
                'description' => 'A nationwide network committed to seed sovereignty, farmer livelihoods, and ecological farming.',
                'website_url' => 'https://kisanswaraj.in',
                'collaboration_type' => 'Knowledge Exchange & Seed Policy Advocacy',
                'projects_summary' => 'Documenting traditional seed diversity and farmer-led seed saver initiatives.',
                'is_published' => true,
            ]
        );

        // 8. Sample Pending Seed Submission
        SeedSubmission::firstOrCreate(
            ['seed_name_pa' => 'ਦੇਸੀ ਲਾਲ ਮੱਕੀ (Red Maize)', 'contributor_name' => 'Harpreet Singh'],
            [
                'village' => 'Bhaini Sahib',
                'district' => 'Ludhiana',
                'state' => 'Punjab',
                'phone' => '+91 98123 45678',
                'email' => 'harpreet@example.com',
                'seed_name_en' => 'Desi Red Maize',
                'crop_category_name' => 'Maize',
                'local_names' => 'Lal Makki',
                'description' => 'An old variety of deep red maize preserved by our grandfather. Grown without chemical fertilizers.',
                'traditional_uses' => 'Makki di roti, roasted corncob, and animal feed.',
                'growing_season' => 'Kharif (July - October)',
                'preservation_method' => 'Hanging cobs under roof shed away from rain.',
                'duration_known' => 'More than 40 years in family',
                'contact_consent' => true,
                'display_consent' => true,
                'status' => 'pending',
            ]
        );

        // 9. Demo Customer Account
        $customer = User::firstOrCreate(
            ['email' => 'customer@apnikheti.org'],
            [
                'name' => 'Gurpreet Singh',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+91 98765 12345',
                'address' => 'House #12, Near Gurdwara Sahib, VPO Bhaini Sahib',
                'city' => 'Ludhiana',
                'state' => 'Punjab',
                'pincode' => '141126',
            ]
        );

        // 10. Shop Product Categories
        $wheatCat = ShopProductCategory::firstOrCreate(
            ['slug' => 'traditional-wheat-grains'],
            ['name' => 'Traditional Wheat & Grains', 'description' => 'Heirloom, non-hybrid desi wheat varieties celebrated for aroma and natural pest resistance.']
        );

        $milletCat = ShopProductCategory::firstOrCreate(
            ['slug' => 'heirloom-millets-pulses'],
            ['name' => 'Heirloom Millets & Pulses', 'description' => 'Drought-tolerant native millets and organic pulses high in fiber and micronutrients.']
        );

        $kitsCat = ShopProductCategory::firstOrCreate(
            ['slug' => 'starter-kits'],
            ['name' => 'Seed Starter Kits', 'description' => 'Thoughtfully curated native seed starter collections for kitchen gardens and organic farms.']
        );

        $oilseedsCat = ShopProductCategory::firstOrCreate(
            ['slug' => 'natural-oilseeds'],
            ['name' => 'Natural Oilseeds', 'description' => 'Traditional yellow and black mustard, sesame, and flax seeds.']
        );

        // 11. Shop Products
        $p1 = ShopProduct::updateOrCreate(
            ['name' => 'Bansi Wheat (ਦੇਸੀ ਬੰਸੀ ਕਣਕ)'],
            [
                'sku' => 'BEJ-WHT-001',
                'product_type' => 'seed',
                'shop_product_category_id' => $wheatCat->id,
                'description' => 'A famous traditional drought-hardy tall wheat variety with golden-amber grains, high dietary fiber, and superior sweet chapati taste.',
                'price' => 450.00,
                'stock_quantity' => 35,
                'unit' => 'packet',
                'weight' => '5 Kg',
                'is_available' => true,
                'is_published' => true,
            ]
        );

        $p2 = ShopProduct::updateOrCreate(
            ['name' => 'Desi Red Maize (ਦੇਸੀ ਲਾਲ ਮੱਕੀ)'],
            [
                'sku' => 'BEJ-CRN-002',
                'product_type' => 'seed',
                'shop_product_category_id' => $milletCat->id,
                'description' => 'Native Punjab red corn rich in antioxidants and anthocyanins. Preserved without chemical treatments for generations.',
                'price' => 220.00,
                'stock_quantity' => 20,
                'unit' => 'packet',
                'weight' => '1 Kg',
                'is_available' => true,
                'is_published' => true,
            ]
        );

        $p3 = ShopProduct::updateOrCreate(
            ['name' => 'Complete Kitchen Garden Native Seed Kit (12 Varieties)'],
            [
                'sku' => 'KIT-GDN-003',
                'product_type' => 'kit',
                'shop_product_category_id' => $kitsCat->id,
                'description' => 'Carefully curated kit containing 12 desi vegetable seeds: Bottle Gourd, Bitter Gourd, Okra, Palak, Radish, Coriander, Desi Tomato, Brinjal, and more.',
                'price' => 650.00,
                'stock_quantity' => 15,
                'unit' => 'kit',
                'weight' => '1 Box',
                'is_available' => true,
                'is_published' => true,
            ]
        );

        $p4 = ShopProduct::updateOrCreate(
            ['name' => 'Desi Mustard (ਪੀਲੀ ਸਰ੍ਹੋਂ)'],
            [
                'sku' => 'BEJ-MST-004',
                'product_type' => 'seed',
                'shop_product_category_id' => $oilseedsCat->id,
                'description' => 'Traditional yellow mustard seed with pungent aromatic cold-press oil quality and high oil yield.',
                'price' => 160.00,
                'stock_quantity' => 4, // low stock trigger
                'unit' => 'packet',
                'weight' => '500g',
                'is_available' => true,
                'is_published' => true,
            ]
        );

        // 12. Sample Customer Orders
        $order1 = Order::firstOrCreate(
            ['order_number' => 'AK-20261021-DEMO1'],
            [
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'shipping_address' => $customer->address,
                'shipping_city' => $customer->city,
                'shipping_state' => $customer->state,
                'shipping_pincode' => $customer->pincode,
                'subtotal' => 650.00,
                'shipping_cost' => 0.00,
                'total_amount' => 650.00,
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'status' => 'delivered',
                'courier_name' => 'India Post Speed Post',
                'tracking_number' => 'ED987654321IN',
                'customer_notes' => 'Please leave with neighbour if unavailable.',
            ]
        );

        if ($order1->items()->count() === 0) {
            OrderItem::create([
                'order_id' => $order1->id,
                'shop_product_id' => $p3->id,
                'product_name' => $p3->name,
                'product_sku' => $p3->sku,
                'unit_price' => 650.00,
                'quantity' => 1,
                'total_price' => 650.00,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order1->id,
                'status' => 'pending',
                'comment' => 'Order placed successfully by customer via Cash on Delivery.',
                'created_by' => 'Customer',
                'created_at' => now()->subDays(3),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order1->id,
                'status' => 'shipped',
                'comment' => 'Order dispatched via India Post Speed Post. Tracking code: ED987654321IN.',
                'created_by' => 'Admin (Amrit Singh Chahal)',
                'created_at' => now()->subDays(2),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order1->id,
                'status' => 'delivered',
                'comment' => 'Package delivered to recipient. Payment received.',
                'created_by' => 'Admin (Amrit Singh Chahal)',
                'created_at' => now()->subDay(),
            ]);
        }

        $order2 = Order::firstOrCreate(
            ['order_number' => 'AK-20261021-DEMO2'],
            [
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'shipping_address' => $customer->address,
                'shipping_city' => $customer->city,
                'shipping_state' => $customer->state,
                'shipping_pincode' => $customer->pincode,
                'subtotal' => 450.00,
                'shipping_cost' => 50.00,
                'total_amount' => 500.00,
                'payment_method' => 'upi',
                'payment_status' => 'pending',
                'status' => 'processing',
                'customer_notes' => 'Calling ahead is appreciated.',
            ]
        );

        if ($order2->items()->count() === 0) {
            OrderItem::create([
                'order_id' => $order2->id,
                'shop_product_id' => $p1->id,
                'product_name' => $p1->name,
                'product_sku' => $p1->sku,
                'unit_price' => 450.00,
                'quantity' => 1,
                'total_price' => 450.00,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order2->id,
                'status' => 'pending',
                'comment' => 'Order placed via UPI payment transfer.',
                'created_by' => 'Customer',
                'created_at' => now()->subHours(6),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order2->id,
                'status' => 'processing',
                'comment' => 'Payment confirmed. Seeds being selected and weighed from climate-controlled storage.',
                'created_by' => 'Admin (Amrit Singh Chahal)',
                'created_at' => now()->subHours(2),
            ]);
        }
    }
}
