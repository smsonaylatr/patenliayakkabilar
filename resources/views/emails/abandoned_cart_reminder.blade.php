<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sepetinizde Ürünler Unuttunuz!</title>
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
            <h1>Sepetinizde Ürünler Sizi Bekliyor! 🎁</h1>
        </div>
        <div class="content">
            <p>Merhaba {{ $cart->user->name ?? 'Değerli Müşterimiz' }},</p>
            <p>Patenli Ayakkabılar'da gezinirken sepetinize harika ürünler eklemiştiniz ama siparişinizi henüz tamamlamadınız. Stoklarımız tükenmeden sepetinizdeki ürünleri almak için hala bir şansınız var!</p>
            
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
                <a href="{{ url('/checkout') }}" class="button">Sepetime Git ve Satın Al</a>
            </div>
        </div>
        <div class="footer">
            <p>Bu e-posta size sitemize üye olduğunuz için bilgilendirme amacıyla gönderilmiştir.</p>
            <p>&copy; {{ date('Y') }} Patenli Ayakkabılar. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
