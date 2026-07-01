<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@patenliayakkabilar.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Categories
        $cat1 = Category::firstOrCreate(['slug' => 'isikli-patenli-ayakkabi'], ['name' => 'Işıklı Patenli Ayakkabılar', 'status' => true]);
        $cat2 = Category::firstOrCreate(['slug' => 'tekerlekli-sneaker'], ['name' => 'Tekerlekli Sneaker', 'status' => true]);

        // Products
        $products = [
            [
                'name' => 'Işıklı Patenli Ayakkabı Pembe',
                'slug' => 'isikli-patenli-ayakkabi-pembe',
                'price' => 1499.99,
                'discount_price' => 1299.99,
                'stock' => 50,
                'category_id' => $cat1->id,
                'short_description' => 'Çocukların favorisi pembe ışıklı patenli ayakkabı.',
                'featured' => true,
            ],
            [
                'name' => 'Siyah Tekerlekli Sneaker',
                'slug' => 'siyah-tekerlekli-sneaker',
                'price' => 1299.99,
                'discount_price' => null,
                'stock' => 30,
                'category_id' => $cat2->id,
                'short_description' => 'Hem günlük hem eğlence için siyah tekerlekli sneaker.',
                'best_seller' => true,
            ],
            [
                'name' => 'Mavi Çocuk Patenli Ayakkabı',
                'slug' => 'mavi-cocuk-patenli-ayakkabi',
                'price' => 1399.99,
                'discount_price' => 1199.99,
                'stock' => 45,
                'category_id' => $cat1->id,
                'short_description' => 'Erkek çocukların vazgeçilmezi mavi patenli ayakkabı.',
            ],
            [
                'name' => 'Beyaz Işıklı Patenli Ayakkabı',
                'slug' => 'beyaz-isikli-patenli-ayakkabi',
                'price' => 1599.99,
                'discount_price' => 1499.99,
                'stock' => 20,
                'category_id' => $cat1->id,
                'short_description' => 'Şık ve dikkat çekici beyaz ışıklı model.',
            ],
            [
                'name' => 'Erkek Çocuk Tekerlekli Ayakkabı',
                'slug' => 'erkek-cocuk-tekerlekli-ayakkabi',
                'price' => 1199.99,
                'discount_price' => null,
                'stock' => 60,
                'category_id' => $cat2->id,
                'short_description' => 'Konforlu taban, gizlenebilir tekerlek.',
            ],
            [
                'name' => 'Kız Çocuk Patenli Spor Ayakkabı',
                'slug' => 'kiz-cocuk-patenli-spor-ayakkabi',
                'price' => 1349.99,
                'discount_price' => 1149.99,
                'stock' => 40,
                'category_id' => $cat1->id,
                'short_description' => 'Kız çocuklarına özel pembe ve mor detaylı spor ayakkabı.',
            ],
        ];

        foreach ($products as $index => $p) {
            $product = Product::firstOrCreate(['slug' => $p['slug']], $p);
            
            // Add variants
            $sizes = [30, 31, 32, 33, 34];
            foreach ($sizes as $size) {
                ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'size' => $size, 'color' => 'Standart'],
                    ['stock' => 10, 'price_extra' => 0]
                );
            }

            // Add product image
            $imageNumber = $index + 1;
            $imagePath = "products/{$imageNumber}.webp";
            
            // Copy image from resources to storage if it exists
            $source = resource_path("images/products/{$imageNumber}.webp");
            $dest = storage_path("app/public/products/{$imageNumber}.webp");
            
            if (file_exists($source) && !file_exists($dest)) {
                @mkdir(dirname($dest), 0775, true);
                copy($source, $dest);
            }

            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'image_path' => $imagePath],
                ['sort_order' => 0]
            );
        }
    }
}
