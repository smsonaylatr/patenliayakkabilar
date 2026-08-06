<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Stoklar Yenilendi!</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 30px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .content { padding: 30px; text-align: center; }
        .product-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0; text-align: center; }
        .product-img { max-width: 220px; height: auto; border-radius: 12px; margin-bottom: 15px; }
        .product-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .product-variant { display: inline-block; background: #e0f2fe; color: #0369a1; font-weight: 600; font-size: 13px; padding: 4px 12px; rounded-radius: 20px; border-radius: 6px; margin-bottom: 12px; }
        .price { font-size: 24px; font-weight: 800; color: #dc2626; margin-bottom: 20px; }
        .btn { display: inline-block; background: #0f172a; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 30px; text-decoration: none; transition: background 0.2s; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-t: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Beklediğiniz Ürün Stokta!</h1>
        </div>
        <div class="content">
            <p style="font-size: 15px; color: #475569;">Harika haber! Daha önce stok bildirimi istediğiniz ürünün stokları yenilendi.</p>
            
            <div class="product-card">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="product-img">
                @endif
                <div class="product-title">{{ $product->name }}</div>
                @if($variant)
                    <div class="product-variant">{{ $variant->size }} Beden</div>
                @endif
                <div class="price">{{ number_format($product->discount_price ?? $product->price, 2) }} ₺</div>
                
                <a href="{{ url('/urun/' . $product->slug) }}" class="btn">Hemen İncele & Satın Al</a>
            </div>

            <p style="font-size: 13px; color: #64748b;">Stoklar tükenmeden siparişinizi vermek için acele edin!</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Patenli Ayakkabılar — Bu e-posta stok bildirimi talebiniz üzerine gönderilmiştir.
        </div>
    </div>
</body>
</html>
