<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierWaybillController extends Controller
{
    /**
     * Sipariş verilerini hazırlar ve konsolide eder.
     */
    protected function prepareData(array $orderIds): array
    {
        $records = Order::whereIn('id', $orderIds)
            ->with(['items.product.images', 'items.variant'])
            ->latest('created_at')
            ->get();

        $ordersData = [];
        $consolidatedMap = [];
        $totalQuantity = 0;

        foreach ($records as $order) {
            $orderItems = [];
            foreach ($order->items as $item) {
                $img = $this->resolveItemImage($item);
                $variant = $item->variant_info ?: ($item->variant?->size ?? ($item->variant?->name ?? '-'));
                $name = $item->product_name ?: ($item->product?->name ?? 'Ürün');
                $qty = (int)($item->quantity ?? 1);
                $totalQuantity += $qty;

                $orderItems[] = [
                    'image' => $img,
                    'name' => $name,
                    'variant' => $variant,
                    'quantity' => $qty,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ];

                // Konsolide özet için benzersiz anahtar
                $key = ($item->product_id ?? 0) . '-' . $variant;
                if (!isset($consolidatedMap[$key])) {
                    $consolidatedMap[$key] = [
                        'image' => $img,
                        'name' => $name,
                        'variant' => $variant,
                        'quantity' => 0,
                    ];
                }
                $consolidatedMap[$key]['quantity'] += $qty;
            }

            $ordersData[] = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'city' => $order->shipping_city ?: ($order->billing_city ?: '-'),
                'district' => $order->shipping_district ?: ($order->billing_district ?: ''),
                'date' => $order->created_at?->format('d.m.Y H:i') ?? '-',
                'status' => $order->status,
                'items' => $orderItems,
            ];
        }

        $consolidated = collect(array_values($consolidatedMap))
            ->sortBy(['name', 'variant'])
            ->values()
            ->all();

        return [
            'orders' => $ordersData,
            'consolidated' => $consolidated,
            'totalOrders' => $records->count(),
            'totalProducts' => count($consolidated),
            'totalQuantity' => $totalQuantity,
            'date' => now()->format('d.m.Y H:i'),
        ];
    }

    /**
     * Ürün görselini base64 veya güvenli URL olarak çözer.
     */
    protected function resolveItemImage($item): string
    {
        $defaultImage = 'https://patenliayakkabilar.com/favicon.png';

        if (!$item->product) {
            return $defaultImage;
        }

        $firstImg = $item->product->images->first();
        if (!$firstImg || empty($firstImg->image_path)) {
            return $defaultImage;
        }

        $path = $firstImg->image_path;

        // Harici URL ise
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Yerel diskte dosya varsa base64 formatına çevir (PDF ve web için %100 sorunsuz çalışır)
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            try {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION) ?: 'jpeg';
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Throwable $e) {
                // Hata durumunda storage URL'sine dön
            }
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * PDF Dosyasını İndirir veya Yazdırma Görünümüne Yönlendirir.
     */
    public function downloadPdf(Request $request)
    {
        $ids = $this->parseIds($request);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Lütfen en az bir sipariş seçin.');
        }

        // Eğer DomPDF paketi yüklüyse PDF oluştur
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            try {
                $data = $this->prepareData($ids);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.supplier-waybill', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

                $fileName = 'tedarikci-irsaliye-' . now()->format('Y-m-d-His') . '.pdf';
                return $pdf->download($fileName);
            } catch (\Throwable $e) {
                // PDF motoru hata verirse yazdırma sayfasına yönlendir
            }
        }

        // DomPDF yoksa doğrudan tarayıcı yazdırma/PDF ekranına yönlendir
        return redirect()->route('admin.orders.supplier-waybill.print', ['ids' => implode(',', $ids), 'auto_print' => 1]);
    }

    /**
     * Tarayıcıda yazdırılabilir / görüntülenebilir sayfa açar.
     */
    public function printView(Request $request)
    {
        $ids = $this->parseIds($request);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Lütfen en az bir sipariş seçin.');
        }

        $data = $this->prepareData($ids);

        return view('admin.orders.supplier-waybill-print', $data);
    }

    /**
     * İstekten gelen ID'leri ayrıştırır.
     */
    protected function parseIds(Request $request): array
    {
        $raw = $request->query('ids') ?? $request->input('ids');
        if (is_array($raw)) {
            return array_filter(array_map('intval', $raw));
        }
        if (is_string($raw)) {
            return array_filter(array_map('intval', explode(',', $raw)));
        }
        return [];
    }
}
