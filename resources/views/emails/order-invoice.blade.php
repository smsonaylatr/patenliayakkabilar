<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 40px; font-size: 12px; color: #888; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Merhaba {{ $order->customer_name }},</h2>
        </div>
        
        <p><strong>{{ $order->order_number }}</strong> numaralı siparişinize ait e-faturanız/e-arşiv belgeniz başarıyla oluşturulmuştur.</p>
        
        <p>Faturanızı bu e-postanın ekinde PDF formatında bulabilirsiniz.</p>
        
        @if(!empty($pdfUrl))
            <p style="text-align: center;">
                <a href="{{ $pdfUrl }}" class="btn">Faturayı Tarayıcıda Görüntüle</a>
            </p>
        @endif
        
        <p>Bizi tercih ettiğiniz için teşekkür ederiz.</p>
        
        <div class="footer">
            Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.<br>
            Patenli Ayakkabılar
        </div>
    </div>
</body>
</html>
