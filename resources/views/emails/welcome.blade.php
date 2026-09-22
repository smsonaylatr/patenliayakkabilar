@extends('emails.layouts.base')

@section('title', 'Aramıza Hoşgeldiniz!')

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: #ff4e00; margin-top: 0; font-size: 26px;">Hoşgeldiniz! 🎉</h2>
</div>

<p style="font-size: 16px; margin-bottom: 20px;">Merhaba {{ $user->name }},</p>

<p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
    Patenli Ayakkabılar ailesine katıldığınız için çok mutluyuz. Senin için harika modeller hazırladık! Artık en yeni ürünlerden, kampanyalardan ve sana özel indirimlerden ilk senin haberin olacak.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
    <tr>
        <td style="padding-bottom: 15px;">
            <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px;">
                <tr>
                    <td width="40" style="font-size: 24px; text-align: center;">🚀</td>
                    <td style="font-size: 15px; font-weight: bold; color: #374151;">Hızlı ve Ücretsiz Kargo</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 15px;">
            <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px;">
                <tr>
                    <td width="40" style="font-size: 24px; text-align: center;">💯</td>
                    <td style="font-size: 15px; font-weight: bold; color: #374151;">%100 Orijinal Ürün Garantisi</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 15px;">
            <table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f9fafb; border-radius: 8px;">
                <tr>
                    <td width="40" style="font-size: 24px; text-align: center;">🔄</td>
                    <td style="font-size: 15px; font-weight: bold; color: #374151;">Kolay İade ve Değişim</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 25px; color: #111827;">
    İlk alışverişine özel sürprizler seni bekliyor!
</p>

<div style="text-align: center; margin-top: 10px;">
    <a href="https://patenliayakkabilar.com" style="display: inline-block; background-color: #ff4e00; color: #ffffff; text-decoration: none; padding: 15px 30px; border-radius: 6px; font-weight: bold; font-size: 16px;">Alışverişe Başla</a>
</div>
@endsection
