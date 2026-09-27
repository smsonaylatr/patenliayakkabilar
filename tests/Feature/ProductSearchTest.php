<?php

namespace Tests\Feature;

use App\Filament\Pages\ActiveVisitors;
use App\Livewire\Frontend\SearchModal;
use App\Livewire\Product\ProductGrid;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Işıklı Patenli Ayakkabılar',
            'slug' => 'isikli-patenli-ayakkabilar',
            'status' => true,
        ]);

        $p1 = Product::create([
            'name' => 'Işıklı Patenli Ayakkabı Pembe',
            'slug' => 'isikli-patenli-ayakkabi-pembe',
            'price' => 1499.00,
            'status' => true,
            'category_id' => $category->id,
            'short_description' => 'Harika pembe ışıklı patenli ayakkabı.',
            'sku' => 'PATEN-PEMBE-01',
        ]);
        $p1->categories()->attach($category->id);

        ProductVariant::create([
            'product_id' => $p1->id,
            'color' => 'Pembe',
            'size' => '32',
            'stock' => 10,
        ]);

        $p2 = Product::create([
            'name' => 'Siyah Tekerlekli Sneaker',
            'slug' => 'siyah-tekerlekli-sneaker',
            'price' => 1299.00,
            'status' => true,
            'short_description' => 'Konforlu siyah sneaker.',
            'sku' => 'PATEN-SIYAH-02',
        ]);

        ProductVariant::create([
            'product_id' => $p2->id,
            'color' => 'Siyah',
            'size' => '36',
            'stock' => 5,
        ]);
    }

    public function test_catalog_search_filters_products_by_query(): void
    {
        $response = $this->get('/patenli-ayakkabilar?search=Pembe');
        $response->assertStatus(200);
        $response->assertSee('Işıklı Patenli Ayakkabı Pembe');
        $response->assertDontSee('Siyah Tekerlekli Sneaker');
        $response->assertSee('Pembe" Arama Sonuçları');
    }

    public function test_arama_route_redirects_to_catalog_with_search_param(): void
    {
        $response = $this->get('/arama?q=isikli');
        $response->assertRedirect('/patenli-ayakkabilar?search=isikli');

        $response2 = $this->get('/ara?search=pembe');
        $response2->assertRedirect('/patenli-ayakkabilar?search=pembe');
    }

    public function test_active_visitors_channel_search_generates_correct_url(): void
    {
        $url = ActiveVisitors::resolveRedirectUrl([
            'channel' => 'search',
            'search_query' => 'ışıklı',
        ]);

        $this->assertEquals('/patenli-ayakkabilar?search=' . urlencode('ışıklı'), $url);
    }

    public function test_product_grid_livewire_component_filters_products(): void
    {
        Livewire::test(ProductGrid::class, ['search' => 'Pembe'])
            ->assertSee('Işıklı Patenli Ayakkabı Pembe')
            ->assertDontSee('Siyah Tekerlekli Sneaker');

        Livewire::test(ProductGrid::class, ['search' => 'Siyah'])
            ->assertSee('Siyah Tekerlekli Sneaker')
            ->assertDontSee('Işıklı Patenli Ayakkabı Pembe');

        Livewire::test(ProductGrid::class, ['search' => 'bulunamayan_kelime_xyz'])
            ->assertSee('ile eşleşen ürün bulunamadı')
            ->assertDontSee('Işıklı Patenli Ayakkabı Pembe');
    }

    public function test_search_modal_livewire_component(): void
    {
        Livewire::test(SearchModal::class)
            ->set('search', 'Pembe')
            ->assertSee('Işıklı Patenli Ayakkabı Pembe')
            ->call('performSearch')
            ->assertRedirect('/patenli-ayakkabilar?search=' . urlencode('Pembe'));
    }
}
