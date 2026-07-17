<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaytrWebhookController extends Controller
{
    /**
     * PayTR'dan dönen başarılı işlem sonucu yönlendirmesi.
     */
    public function success(Request $request)
    {
        // Gömülü iframe içinde yönlendirme olduğu için, iframe içinden
        // parent frame'i success route'una yönlendirecek bir script basmalıyız.
        $orderNumber = session('last_order_number', null);
        
        return view('payment.paytr-result', [
            'status' => 'success',
            'order_number' => $orderNumber
        ]);
    }

    /**
     * PayTR'dan dönen başarısız işlem sonucu yönlendirmesi.
     */
    public function fail(Request $request)
    {
        return view('payment.paytr-result', [
            'status' => 'error',
            'reason' => $request->post('reason') ?? 'Bilinmeyen hata'
        ]);
    }

    /**
     * PayTR arka plan bildirimi (Webhook).
     * Siparişin ödenip ödenmediği kesin olarak burada anlaşılır.
     */
    public function webhook(Request $request)
    {
        $post = $request->all();

        // Zorunlu alanların kontrolü
        if (!isset($post['merchant_oid']) || !isset($post['status']) || !isset($post['hash'])) {
            return response('Eksik parametre', 400);
        }

        $merchant_key = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');

        // Hash doğrulama
        $hash_string = $post['merchant_oid'] . $merchant_salt . $post['status'] . $post['total_amount'];
        $hash = base64_encode(hash_hmac('sha256', $hash_string, $merchant_key, true));

        if ($hash != $post['hash']) {
            Log::error('PayTR Hash Uyumsuzluğu!', ['post' => $post]);
            return response('PAYTR notification failed: bad hash', 400);
        }

        // Siparişi bul
        $order = Order::where('order_number', $post['merchant_oid'])->first();

        if (!$order) {
            Log::error('PayTR Sipariş Bulunamadı!', ['order_number' => $post['merchant_oid']]);
            return response('Siparis bulunamadi', 400);
        }

        // Ödeme Başarılı
        if ($post['status'] == 'success') {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
            }
        } 
        // Ödeme Başarısız
        else {
            if ($order->payment_status !== 'failed') {
                $order->update([
                    'payment_status' => 'failed',
                ]);
            }
        }

        // PayTR'a işlemin başarılı alındığını bildirmek şarttır.
        return response('OK');
    }
}
