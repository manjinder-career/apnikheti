<?php

namespace Tests\Feature;

use App\Models\MediaItem;
use App\Models\Seed;
use App\Models\SeedCategory;
use App\Models\SeedSubmission;
use App\Models\ShopProduct;
use App\Models\ShopProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApniKhetiWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('ਆਪਣੀ ਖੇਤੀ');
        $response->assertSee('Our Farming. Our Seeds. Our Future.');
    }

    public function test_about_page_loads_with_founder_info()
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Amrit Singh Chahal');
    }

    public function test_seed_library_lists_published_seeds_and_supports_search()
    {
        $response = $this->get('/seeds');
        $response->assertStatus(200);
        $response->assertSee('ਬੰਸੀ ਕਣਕ');

        $searchResponse = $this->get('/seeds?q=Bansi');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Bansi Wheat');
    }

    public function test_seed_detail_page_loads_correctly()
    {
        $seed = Seed::published()->first();
        $response = $this->get('/seeds/'.$seed->slug);
        $response->assertStatus(200);
        $response->assertSee($seed->name_pa);
    }

    public function test_language_switcher_updates_locale_session()
    {
        $response = $this->get('/?lang=pa');
        $response->assertStatus(200);
        $this->assertEquals('pa', session('locale'));
    }

    public function test_public_user_can_submit_traditional_seed_contribution()
    {
        $response = $this->post('/contribute-seed', [
            'contributor_name' => 'Gurdev Singh',
            'village' => 'Badhni',
            'district' => 'Moga',
            'seed_name_pa' => 'ਦੇਸੀ ਕਾਲੀ ਮੱਕੀ (Black Maize)',
            'seed_name_en' => 'Desi Black Maize',
            'crop_category_name' => 'Maize',
            'description' => 'Rare traditional dark black corn variety preserved by local family.',
            'display_consent' => '1',
            'contact_consent' => '1',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('seed_submissions', [
            'contributor_name' => 'Gurdev Singh',
            'seed_name_pa' => 'ਦੇਸੀ ਕਾਲੀ ਮੱਕੀ (Black Maize)',
            'status' => 'pending',
        ]);
    }

    public function test_unauthorized_user_cannot_access_admin_portal()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_admin_user_can_login_and_access_admin_dashboard()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        $response = $this->post('/login', [
            'email' => 'admin@apnikheti.org',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $dashResponse = $this->actingAs($admin)->get('/admin');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Admin Dashboard');
    }

    public function test_admin_can_approve_seed_submission_and_publish_it()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        $submission = SeedSubmission::where('status', 'pending')->first();
        $category = SeedCategory::first();

        $response = $this->actingAs($admin)->post('/admin/submissions/'.$submission->id.'/approve', [
            'seed_category_id' => $category->id,
            'review_notes' => 'Verified and approved.',
        ]);

        $response->assertRedirect(route('admin.submissions.index'));
        $this->assertDatabaseHas('seed_submissions', [
            'id' => $submission->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('seeds', [
            'name_pa' => $submission->seed_name_pa,
            'publication_status' => 'published',
        ]);
    }

    public function test_admin_can_export_seeds_csv()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        $response = $this->actingAs($admin)->get('/admin/seeds/export');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_upload_gallery_photo_and_publish_youtube_video()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        Storage::fake('public');

        $photoResponse = $this->actingAs($admin)->post(route('admin.media.store'), [
            'title' => 'Seed saving workshop',
            'media_type' => 'photo',
            'category' => 'Workshop',
            'image' => UploadedFile::fake()->createWithContent(
                'workshop.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a0ioAAAAASUVORK5CYII=')
            ),
            'is_published' => '1',
        ]);

        $photoResponse->assertRedirect(route('admin.media.index'));
        $photo = MediaItem::where('title', 'Seed saving workshop')->firstOrFail();
        $this->assertStringStartsWith('storage/media/', $photo->url_or_path);
        $this->assertTrue($photo->is_published);
        Storage::disk('public')->assertExists(substr($photo->url_or_path, strlen('storage/')));
        $this->get(route('gallery.index'))->assertOk()->assertSee('Seed saving workshop');

        $multiplePhotoResponse = $this->actingAs($admin)->post(route('admin.media.store'), [
            'title' => 'Harvest day',
            'media_type' => 'photo',
            'category' => 'Field events',
            'images' => [
                UploadedFile::fake()->createWithContent(
                    'harvest-one.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a0ioAAAAASUVORK5CYII=')
                ),
                UploadedFile::fake()->createWithContent(
                    'harvest-two.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a0ioAAAAASUVORK5CYII=')
                ),
            ],
            'is_published' => '1',
        ]);

        $multiplePhotoResponse->assertRedirect(route('admin.media.index'));
        $this->assertDatabaseHas('media_items', [
            'title' => 'Harvest day',
            'category' => 'Field events',
            'media_type' => 'photo',
        ]);
        $this->assertSame(2, MediaItem::where('title', 'Harvest day')->count());

        $videoResponse = $this->actingAs($admin)->post(route('admin.media.store'), [
            'title' => 'Field demonstration',
            'media_type' => 'video',
            'url_or_path' => 'https://youtu.be/abcdefghijk',
            'is_published' => '1',
        ]);

        $videoResponse->assertRedirect(route('admin.media.index'));
        $video = MediaItem::where('title', 'Field demonstration')->firstOrFail();
        $this->assertSame('abcdefghijk', $video->youtube_id);
        $this->get(route('videos.index'))->assertOk()->assertSee('Field demonstration');

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'title' => 'Short field update',
            'media_type' => 'video',
            'url_or_path' => 'https://www.youtube.com/shorts/ZYXwvutsrqp',
            'is_published' => '1',
        ])->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseHas('media_items', [
            'title' => 'Short field update',
            'youtube_id' => 'ZYXwvutsrqp',
        ]);

        $this->actingAs($admin)->put(route('admin.media.update', $photo), [
            'title' => 'Seed saving workshop 2026',
            'media_type' => 'photo',
            'url_or_path' => $photo->url_or_path,
            'category' => 'Field events',
            'description' => 'Updated gallery caption.',
            'is_published' => '0',
        ])->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseHas('media_items', [
            'id' => $photo->id,
            'title' => 'Seed saving workshop 2026',
            'is_published' => false,
            'category' => 'Field events',
        ]);
        $this->get(route('gallery.index'))->assertDontSee('Seed saving workshop 2026');
    }

    public function test_admin_can_manage_seed_shop_products()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        Storage::fake('public');

        $response = $this->actingAs($admin)->post(route('admin.shop-products.store'), [
            'name' => 'Pollinator garden kit',
            'product_type' => 'kit',
            'description' => 'A starter collection for a home garden.',
            'price' => '399.00',
            'is_available' => '1',
            'is_published' => '1',
            'image' => UploadedFile::fake()->createWithContent(
                'pollinator.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a0ioAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect(route('admin.shop-products.index'));
        $product = ShopProduct::where('name', 'Pollinator garden kit')->firstOrFail();
        $this->assertSame('kit', $product->product_type);
        $this->assertTrue($product->is_available);
        $this->assertTrue($product->is_published);
        $this->assertStringStartsWith('storage/shop-products/', $product->image_url);
        Storage::disk('public')->assertExists(substr($product->image_url, strlen('storage/')));
        $this->get(route('admin.shop-products.index'))->assertOk()->assertSee($product->name);
        $this->get(route('shop.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee(asset($product->image_url), false);

        $this->actingAs($admin)->put(route('admin.shop-products.update', $product), [
            'name' => 'Pollinator garden kit',
            'product_type' => 'kit',
            'price' => '449.00',
            'is_available' => '1',
            'is_published' => '1',
        ])->assertRedirect(route('admin.shop-products.index'));

        $this->assertDatabaseHas('shop_products', ['id' => $product->id, 'price' => '449.00']);

        $this->delete(route('admin.shop-products.destroy', $product))
            ->assertRedirect(route('admin.shop-products.index'));
        $this->assertDatabaseMissing('shop_products', ['id' => $product->id]);
    }

    public function test_admin_can_manage_shop_categories_and_filter_public_products()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        $this->actingAs($admin)->post(route('admin.shop-categories.store'), [
            'name' => 'Millet seeds',
            'description' => 'Traditional millet varieties.',
        ])->assertRedirect(route('admin.shop-categories.index'));

        $category = ShopProductCategory::where('name', 'Millet seeds')->firstOrFail();
        $this->assertSame('millet-seeds', $category->slug);

        $product = ShopProduct::create([
            'name' => 'Heritage millet',
            'product_type' => 'seed',
            'shop_product_category_id' => $category->id,
            'price' => 125,
            'is_available' => true,
            'is_published' => true,
        ]);

        $this->get(route('shop.index', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($category->name);

        $this->actingAs($admin)->put(route('admin.shop-categories.update', $category), [
            'name' => 'Millet collection',
            'description' => 'A curated millet range.',
        ])->assertRedirect(route('admin.shop-categories.index'));

        $category->refresh();
        $this->assertSame('millet-collection', $category->slug);
        $this->assertSame('Millet collection', $product->fresh()->category->name);

        $this->delete(route('admin.shop-categories.destroy', $category))
            ->assertRedirect(route('admin.shop-categories.index'));
        $this->assertNull($product->fresh()->shop_product_category_id);
    }

    public function test_shop_lists_published_products_and_prompts_order_enquiry()
    {
        $product = ShopProduct::create([
            'name' => 'Heritage wheat seed',
            'product_type' => 'seed',
            'description' => 'Traditional wheat variety.',
            'price' => 250,
            'is_available' => true,
            'is_published' => true,
        ]);
        ShopProduct::create([
            'name' => 'Unpublished seed kit',
            'product_type' => 'kit',
            'price' => 500,
            'is_available' => true,
            'is_published' => false,
        ]);

        $response = $this->get(route('shop.index'));
        $response->assertOk()
            ->assertSee($product->name)
            ->assertDontSee('Unpublished seed kit')
            ->assertSee('Add to Cart');

        $this->get(route('contact', ['subject' => 'Order enquiry: '.$product->name]))
            ->assertOk()
            ->assertSee('Order enquiry: '.$product->name);
    }

    public function test_api_v1_endpoints()
    {
        $response = $this->getJson('/api/v1/seeds');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data' => ['data']]);

        $statsResponse = $this->getJson('/api/v1/stats');
        $statsResponse->assertStatus(200);
        $statsResponse->assertJsonStructure(['status', 'data' => ['total_published_seeds']]);
    }
}
