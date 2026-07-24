<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PotentialCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BilgiController extends Controller
{
    /**
     * Açılış sayfasını gösterir (Fiyat yok, sadece görsel ve özellikler)
     */
    public function index(Request $request)
    {
        // Tüm aktif ürünleri al
        $products = Product::with(['images'])->where('is_active', true)->latest()->get();
        
        return view('landing.bilgi', compact('products'));
    }

    /**
     * Pop-up formundan gelen müşteri bilgilerini kaydeder
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen geçerli bir telefon numarası giriniz.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Müşteriyi kaydet
        $lead = PotentialCustomer::create([
            'product_id' => $request->product_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => 'new'
        ]);

        // Opsiyonel: Admin'e Telegram / E-posta bildirimi atılabilir.
        
        return response()->json([
            'success' => true,
            'message' => 'Talebiniz başarıyla alındı. Müşteri temsilcimiz en kısa sürede size ulaşacaktır.'
        ]);
    }
}
