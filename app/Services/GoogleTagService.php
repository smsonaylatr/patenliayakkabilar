<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GoogleTagService
{
    public const CACHE_KEY = 'google_tag_settings_v2';

    /**
     * Google Tag Platform (gtag.js, GTM, GA4, Google Ads, Consent Mode v2) ayarlarını döndürür.
     */
    public static function getConfig(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $keys = [
                'google_analytics_id',
                'google_tag_id',
                'gtm_container_id',
                'google_ads_id',
                'google_ads_conversion_label',
                'google_ads_cod_conversion_label',
                'google_ads_add_to_cart_label',
                'google_ads_begin_checkout_label',
                'google_consent_mode_enabled',
                'google_enhanced_conversions',
                'gcr_merchant_id',
            ];

            $settings = [];
            try {
                $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
            } catch (\Throwable) {
                $settings = [];
            }

            $gaId = trim((string) ($settings['google_analytics_id'] ?? ''))
                ?: (string) config('services.google.analytics_id', 'G-ZMY22BQK1N');

            $gtmId = trim((string) ($settings['gtm_container_id'] ?? ''))
                ?: (string) config('services.google.gtm_container_id', 'GTM-583KKT3Q');

            $googleTagId = trim((string) ($settings['google_tag_id'] ?? ''))
                ?: (string) config('services.google.tag_id', '');

            $adsId = trim((string) ($settings['google_ads_id'] ?? ''))
                ?: (string) config('services.google.ads_id', '');

            $merchantId = trim((string) ($settings['gcr_merchant_id'] ?? ''))
                ?: '5828544730';

            $primaryTagId = $googleTagId ?: ($gaId ?: $adsId);

            return [
                'primary_tag_id' => $primaryTagId,
                'ga4_id' => $gaId,
                'google_tag_id' => $googleTagId,
                'gtm_id' => $gtmId,
                'ads_id' => $adsId,
                'merchant_id' => $merchantId,
                'ads_purchase_label' => trim((string) ($settings['google_ads_conversion_label'] ?? '')),
                'ads_cod_purchase_label' => trim((string) ($settings['google_ads_cod_conversion_label'] ?? '')),
                'ads_add_to_cart_label' => trim((string) ($settings['google_ads_add_to_cart_label'] ?? '')),
                'ads_begin_checkout_label' => trim((string) ($settings['google_ads_begin_checkout_label'] ?? '')),
                'consent_mode_enabled' => filter_var($settings['google_consent_mode_enabled'] ?? '1', FILTER_VALIDATE_BOOLEAN),
                'enhanced_conversions_enabled' => filter_var($settings['google_enhanced_conversions'] ?? '1', FILTER_VALIDATE_BOOLEAN),
            ];
        });
    }

    /**
     * Önbelleği temizle
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ürünün indirimli/geçerli satış fiyatını ve indirim tutarını hesaplar.
     */
    public static function resolveProductPricing(Product $product): array
    {
        $rawPrice = (float) ($product->price ?? 0);
        $rawDiscount = $product->discount_price !== null ? (float) $product->discount_price : null;

        if ($rawDiscount !== null && $rawDiscount > 0) {
            if ($rawDiscount < $rawPrice) {
                $finalPrice = $rawDiscount;
                $discountAmount = max(0, $rawPrice - $rawDiscount);
            } elseif ($rawDiscount > $rawPrice && $rawPrice > 0) {
                $finalPrice = $rawPrice;
                $discountAmount = max(0, $rawDiscount - $rawPrice);
            } else {
                $finalPrice = $rawPrice;
                $discountAmount = 0.0;
            }
        } else {
            $finalPrice = $rawPrice;
            $discountAmount = 0.0;
        }

        return [
            'price' => round($finalPrice, 2),
            'discount' => round($discountAmount, 2),
            'original_price' => round(max($rawPrice, (float) $rawDiscount), 2),
        ];
    }

    /**
     * GA4 & Google Ads E-Ticaret standartlarına (gtag.js) uygun tekil ürün verisi üretir.
     */
    public static function formatProductItem(
        Product $product,
        ?ProductVariant $variant = null,
        int $quantity = 1,
        ?int $index = null,
        ?string $listName = null,
        ?string $listId = null
    ): array {
        $pricing = self::resolveProductPricing($product);
        $categoryName = $product->relationLoaded('categories')
            ? ($product->categories->first()?->name ?? 'Patenli Ayakkabı')
            : ($product->categories()->first()?->name ?? 'Patenli Ayakkabı');

        $variantLabel = null;
        $sizeStr = null;
        if ($variant) {
            $sizeStr = $variant->size ? (string) $variant->size : null;
            $colorStr = null;
            if (!empty($variant->color)) {
                $colorStr = is_array($variant->color) ? implode(', ', $variant->color) : (string) $variant->color;
            }
            if ($colorStr && $sizeStr) {
                $variantLabel = "{$colorStr} / {$sizeStr}";
            } elseif ($sizeStr) {
                $variantLabel = $sizeStr;
            } elseif ($colorStr) {
                $variantLabel = $colorStr;
            }
        }

        // Merchant Center Feed ile %100 uyumlu ID
        $merchantItemId = $sizeStr ? ($product->id . '_' . $sizeStr) : (string) $product->id;

        $item = [
            'item_id' => (string) $product->id,
            'id' => $merchantItemId,
            'item_group_id' => (string) $product->id,
            'item_name' => $product->name,
            'affiliation' => 'Patenli Ayakkabılar',
            'currency' => 'TRY',
            'discount' => $pricing['discount'],
            'item_brand' => $product->brand ?: 'Patenli Ayakkabılar',
            'item_category' => $categoryName,
            'price' => $pricing['price'],
            'quantity' => max(1, $quantity),
            'google_business_vertical' => 'retail',
        ];

        if ($variantLabel) {
            $item['item_variant'] = $variantLabel;
        }

        if ($index !== null) {
            $item['index'] = $index;
        }

        if ($listName) {
            $item['item_list_name'] = $listName;
        }

        if ($listId) {
            $item['item_list_id'] = $listId;
        }

        return $item;
    }

    /**
     * Ürün listesi (view_item_list) için payload oluşturur.
     */
    public static function formatItemListPayload(
        iterable $products,
        string $listName = 'Tüm Modeller',
        string $listId = 'all_products'
    ): array {
        $items = [];
        $index = 0;

        foreach ($products as $product) {
            if (!$product instanceof Product) {
                continue;
            }
            $items[] = self::formatProductItem($product, null, 1, $index, $listName, $listId);
            $index++;
        }

        return [
            'item_list_id' => $listId,
            'item_list_name' => $listName,
            'items' => $items,
        ];
    }

    /**
     * Sepet (view_cart, begin_checkout, add_shipping_info, add_payment_info) için payload oluşturur.
     */
    public static function formatCartPayload(
        ?Cart $cart,
        ?string $couponCode = null,
        float $discountAmount = 0.0,
        array $extra = []
    ): array {
        if (!$cart) {
            return array_merge([
                'currency' => 'TRY',
                'value' => 0,
                'items' => [],
            ], $extra);
        }

        $cartItems = $cart->relationLoaded('items')
            ? $cart->items
            : $cart->items()->with(['product.categories', 'variant'])->get();

        $items = [];
        $subtotal = 0.0;
        $index = 0;

        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product) {
                continue;
            }
            $itemData = self::formatProductItem(
                $cartItem->product,
                $cartItem->variant,
                (int) $cartItem->quantity,
                $index
            );
            $itemPrice = (float) ($cartItem->price ?: $itemData['price']);
            $itemData['price'] = round($itemPrice, 2);

            if ($couponCode) {
                $itemData['coupon'] = $couponCode;
            }

            $items[] = $itemData;
            $subtotal += $itemPrice * (int) $cartItem->quantity;
            $index++;
        }

        $value = max(0, round($subtotal - $discountAmount, 2));

        $payload = [
            'currency' => 'TRY',
            'value' => $value,
            'items' => $items,
        ];

        if ($couponCode) {
            $payload['coupon'] = $couponCode;
        }

        return array_merge($payload, $extra);
    }

    /**
     * Sipariş (purchase) için tam GA4 & Google Ads E-Ticaret payload'u üretir.
     */
    public static function formatPurchasePayload(Order $order): array
    {
        $order->loadMissing(['items.product.categories', 'items.variant']);

        $items = [];
        $index = 0;
        $prodIds = [];

        foreach ($order->items as $orderItem) {
            $product = $orderItem->product;
            $variant = $orderItem->variant;

            if ($product) {
                $item = self::formatProductItem(
                    $product,
                    $variant,
                    (int) $orderItem->quantity,
                    $index
                );
                $item['price'] = round((float) $orderItem->unit_price, 2);
                if ($orderItem->variant_info && empty($item['item_variant'])) {
                    $item['item_variant'] = $orderItem->variant_info;
                }
            } else {
                $item = [
                    'item_id' => (string) ($orderItem->product_id ?: $orderItem->id),
                    'id' => (string) ($orderItem->product_id ?: $orderItem->id),
                    'item_name' => $orderItem->product_name ?: 'Patenli Ayakkabı',
                    'affiliation' => 'Patenli Ayakkabılar',
                    'currency' => 'TRY',
                    'item_brand' => 'Patenli Ayakkabılar',
                    'item_category' => 'Patenli Ayakkabı',
                    'item_variant' => $orderItem->variant_info ?: '',
                    'price' => round((float) $orderItem->unit_price, 2),
                    'quantity' => max(1, (int) $orderItem->quantity),
                    'index' => $index,
                    'google_business_vertical' => 'retail',
                ];
            }

            if ($order->coupon_code) {
                $item['coupon'] = $order->coupon_code;
            }

            $items[] = $item;
            $prodIds[] = $item['id'];
            $index++;
        }

        $grandTotal = round((float) ($order->grand_total ?: $order->subtotal), 2);
        $shippingPrice = round((float) ($order->shipping_price ?? 0), 2);
        // KDV %10 dahil hesaplama (ayakkabı/tekstil KDV oranı %10)
        $netWithoutShipping = max(0, $grandTotal - $shippingPrice);
        $taxAmount = round($netWithoutShipping - ($netWithoutShipping / 1.10), 2);

        $paymentMethodLabel = match ($order->payment_method) {
            'credit_card' => 'Kredi Kartı',
            'cash_on_delivery' => 'Kapıda Ödeme',
            'wire_transfer' => 'Havale / EFT',
            default => (string) $order->payment_method,
        };

        $payload = [
            'transaction_id' => (string) $order->order_number,
            'affiliation' => 'Patenli Ayakkabılar',
            'value' => $grandTotal,
            'tax' => $taxAmount,
            'shipping' => $shippingPrice,
            'currency' => 'TRY',
            'payment_type' => $paymentMethodLabel,
            'payment_method' => (string) ($order->payment_method ?? ''),
            'items' => $items,
            'ecomm_prodid' => $prodIds,
            'ecomm_pagetype' => 'purchase',
            'ecomm_totalvalue' => $grandTotal,
        ];

        $discountAmount = round((float) ($order->discount_total ?? 0), 2);
        if ($discountAmount <= 0 && $order->subtotal && $grandTotal) {
            $expectedTotal = (float) $order->subtotal + $shippingPrice;
            if ($expectedTotal > $grandTotal) {
                $discountAmount = round($expectedTotal - $grandTotal, 2);
            }
        }
        if ($discountAmount > 0) {
            $payload['discount'] = $discountAmount;
        }

        if (!empty($order->coupon_code)) {
            $payload['coupon'] = (string) $order->coupon_code;
        }

        return $payload;
    }

    /**
     * Google Enhanced Conversions (Gelişmiş Dönüşümler) için kullanıcı verisini (user_data) biçimlendirir.
     */
    public static function formatUserData(
        ?string $name = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $city = null,
        ?string $district = null,
        ?string $address = null
    ): array {
        $userData = [];

        if (!empty($email) && filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
            $userData['email'] = mb_strtolower(trim($email), 'UTF-8');
        }

        if (!empty($phone)) {
            $normalizedPhone = self::normalizePhoneE164($phone);
            if ($normalizedPhone) {
                $userData['phone_number'] = $normalizedPhone;
            }
        }

        $addressData = [];
        if (!empty($name)) {
            $parts = preg_split('/\s+/u', trim($name)) ?: [];
            if (count($parts) >= 2) {
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
                $addressData['first_name'] = $firstName;
                $addressData['last_name'] = $lastName;
            } elseif (count($parts) === 1 && $parts[0] !== '') {
                $addressData['first_name'] = $parts[0];
            }
        }

        if (!empty($address)) {
            $addressData['street'] = trim($address);
        }
        if (!empty($city)) {
            $addressData['city'] = trim($city);
        }
        if (!empty($district)) {
            $addressData['region'] = trim($district);
        }
        if (!empty($addressData)) {
            $addressData['country'] = 'TR';
            $userData['address'] = $addressData;
        }

        return $userData;
    }

    /**
     * Türkiye telefon numarasını Google E.164 (+905XXXXXXXXX) formatına dönüştürür.
     */
    public static function normalizePhoneE164(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (!$digits) {
            return null;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '5')) {
            return '+90' . $digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '05')) {
            return '+9' . $digits;
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '905')) {
            return '+' . $digits;
        }

        if (strlen($digits) >= 10) {
            return '+' . ltrim($digits, '0');
        }

        return null;
    }
}
