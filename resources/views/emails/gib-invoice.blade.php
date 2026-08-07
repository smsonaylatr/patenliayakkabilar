<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Arşiv Faturanız - Patenli Ayakkabılar</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; color: #333; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-height: 60px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; color: #38bdf8; }
        .content { padding: 35px 30px; }
        .greeting { font-size: 16px; margin-bottom: 20px; color: #1e293b; font-weight: 600; }
        .message { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 25px; }
        .order-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
        .order-card table { width: 100%; border-collapse: collapse; }
        .order-card td { padding: 6px 0; font-size: 14px; color: #475569; }
        .order-card td.val { text-align: right; font-weight: 600; color: #0f172a; }
        .btn-container { text-align: center; margin: 30px 0 15px; }
        .btn { display: inline-block; background-color: #0284c7; color: #ffffff !important; font-weight: 600; font-size: 15px; text-decoration: none; padding: 14px 32px; border-radius: 8px; box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25); }
        .footer { background: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .footer a { color: #0284c7; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Patenli Ayakkabılar" style="max-height: 55px; display: block; margin: 0 auto 10px;">
            <h1>Patenli Ayakkabılar</h1>
            <p style="margin: 5px 0 0; font-size: 13px; color: #94a3b8;">Resmi GİB E-Arşiv Faturanız</p>
        </div>

        <div class="content">
            <div class="greeting">Sayın {{ $order->customer_name }},</div>
            
            <p class="message">
                Patenli Ayakkabılar mağazamızdan vermiş olduğunuz <strong>#{{ $order->order_number }}</strong> numaralı siparişinize ait resmi GİB E-Arşiv faturanız başarıyla oluşturulmuştur. Faturanız bu e-postanın ekinde ve aşağıdaki bağlantıda yer almaktadır.
            </p>

            <div class="order-card">
                <table>
                    <tr>
                        <td>Sipariş Numarası:</td>
                        <td class="val">#{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td>Sipariş Tarihi:</td>
                        <td class="val">{{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : date('d.m.Y') }}</td>
                    </tr>
                    <tr>
                        <td>Fatura Tarihi:</td>
                        <td class="val">{{ $order->gib_invoice_date ? $order->gib_invoice_date->format('d.m.Y H:i') : date('d.m.Y') }}</td>
                    </tr>
                    <tr>
                        <td>Toplam Tutar:</td>
                        <td class="val" style="color: #0284c7; font-size: 16px;">₺{{ number_format($order->grand_total, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <div class="btn-container">
                <a href="{{ $invoiceUrl }}" class="btn" target="_blank">📄 Faturayı Görüntüle ve Yazdır</a>
            </div>
            
            <p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 15px;">
                Faturanızı dilediğiniz zaman bilgisayarınıza indirebilir veya yazdırabilirsiniz.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0 0 8px;"><strong>Patenli Ayakkabılar E-Ticaret</strong></p>
            <p style="margin: 0 0 8px;">Müşteri Destek: <a href="mailto:destek@patenliayakkabilar.com">destek@patenliayakkabilar.com</a></p>
            <p style="margin: 0;">Bu e-posta otomatik olarak gönderilmiştir. Lütfen doğrudan yanıtlamayınız.</p>
        </div>
    </div>
</body>
</html>
