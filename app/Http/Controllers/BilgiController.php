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
        // Tüm aktif ürünleri al (Varsayılan sıralama ile: homepage_sort)
        $products = Product::with(['images'])
            ->where('status', true)
            ->orderByRaw('CASE WHEN homepage_sort > 0 THEN 0 ELSE 1 END')
            ->orderBy('homepage_sort', 'asc')
            ->orderBy('id', 'desc')
            ->get();
        
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
            'purpose' => 'nullable|string|max:255',
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
            'notes' => 'Alım Amacı: ' . $request->purpose,
            'status' => 'new'
        ]);

        // Opsiyonel: Admin'e Telegram / E-posta bildirimi atılabilir.
        
        return response()->json([
            'success' => true,
            'message' => 'Talebiniz başarıyla alındı. Müşteri temsilcimiz en kısa sürede size ulaşacaktır.'
        ]);
    }
}
