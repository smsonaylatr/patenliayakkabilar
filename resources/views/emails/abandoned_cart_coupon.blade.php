<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Size Özel %10 İndirim!</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #ff4e00;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .coupon-box {
            background: linear-gradient(135deg, #ff4e00, #ff7b3d);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
            color: #ffffff;
        }
        .coupon-box .label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .coupon-box .code {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 3px;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin: 10px 0;
        }
        .coupon-box .expiry {
            font-size: 13px;
            opacity: 0.9;
            margin-top: 8px;
        }
        .product-list {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        .product-item {
            display: flex;
            padding: 20px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
            background-color: #f3f4f6;
        }
        .product-details {
            flex-grow: 1;
        }
        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .product-variant {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 5px 0;
        }
        .product-price {
            font-size: 16px;
            font-weight: bold;
            color: #ff4e00;
            margin: 0;
        }
        .button-container {
            text-align: center;
            margin-top: 40px;
        }
        .button {
            display: inline-block;
            background-color: #ff4e00;
            color: #ffffff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Size Özel %10 İndirim Fırsatı! 🎁</h1>
        </div>
        <div class="content">
            <p>Merhaba {{ $customerName }},</p>
            <p>Sepetinize eklediğiniz harika ürünler hâlâ sizi bekliyor! Alışverişinizi tamamlamanız için size özel bir <strong>%10 indirim kuponu</strong> hazırladık.</p>

            <div class="coupon-box">
                <div class="label">Kupon Kodunuz</div>
                <div class="code">{{ $couponCode }}</div>
                <div class="expiry">⏰ {{ $expiresAt }} tarihine kadar geçerlidir</div>
            </div>

            <div class="product-list">
                @foreach($cart->items as $item)
                    <div class="product-item">
                        @if($item->product && $item->product->images->first())
                            <img src="{{ url(Storage::url($item->product->images->first()->image_path)) }}" alt="{{ $item->product->name }}" class="product-image">
                        @else
                            <div class="product-image" style="display: flex; align-items: center; justify-content: center; font-size: 12px; color: #9ca3af;">Görsel Yok</div>
                        @endif
                        <div class="product-details">
                            <h3 class="product-name">{{ $item->product->name ?? 'Ürün' }}</h3>
                            @if($item->variant)
                                <p class="product-variant">Renk: {{ is_array($item->variant->color) ? implode(', ', $item->variant->color) : $item->variant->color }} | Beden: {{ $item->variant->size }}</p>
                            @endif
                            <p class="product-variant">Adet: {{ $item->quantity }}</p>
                            <p class="product-price">{{ number_format($item->product->discount_price ?? $item->product->price ?? 0, 2) }} ₺</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="button-container">
                <a href="{{ url('/checkout') }}" class="button">Kuponu Kullan ve Satın Al</a>
            </div>

            <p style="text-align: center; color: #6b7280; font-size: 13px; margin-top: 20px;">Checkout sayfasında kupon kodunuzu girerek indirimi uygulayabilirsiniz.</p>
        </div>
        <div class="footer">
            <p>Bu e-posta size sitemize üye olduğunuz için bilgilendirme amacıyla gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} Patenli Ayakkabılar. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
