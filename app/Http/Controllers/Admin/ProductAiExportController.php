<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductAiExportService;
use Illuminate\Http\Request;

class ProductAiExportController extends Controller
{
    protected ProductAiExportService $exportService;

    public function __construct(ProductAiExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * İstekten gelen ürünleri çözer.
     */
    protected function resolveProducts(Request $request)
    {
        $raw = $request->query('ids') ?? $request->input('ids');

        if (empty($raw) || $raw === 'all') {
            return Product::where('status', true)->get();
        }

        $ids = is_array($raw) ? array_map('intval', $raw) : array_filter(array_map('intval', explode(',', $raw)));

        if (empty($ids)) {
            return Product::where('status', true)->get();
        }

        return Product::whereIn('id', $ids)->get();
    }

    /**
     * İstekten gelen dışa aktarma seçeneklerini çözer.
     */
    protected function resolveOptions(Request $request): array
    {
        return [
            'include_descriptions' => !$request->boolean('no_desc', false),
            'only_in_stock' => $request->boolean('in_stock', false),
        ];
    }

    /**
     * PDF İndirir veya Dompdf yoksa yazdırma/PDF kaydetme sayfasına yönlendirir.
     */
    public function downloadPdf(Request $request)
    {
        $products = $this->resolveProducts($request);
        $options = $this->resolveOptions($request);

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'Dışa aktarılacak ürün bulunamadı.');
        }

        // Eğer Dompdf yüklüyse doğrudan PDF indir
        if (class_exists(\Dompdf\Dompdf::class)) {
            try {
                return $this->exportService->exportToPdf($products, $options);
            } catch (\Throwable $e) {
                // Hata durumunda güvenli şekilde yazdırma ekranına geç
            }
        }

        // Dompdf yoksa doğrudan tarayıcı yazdırma/PDF kaydetme ekranına yönlendir
        $ids = $products->pluck('id')->implode(',');
        return redirect()->route('admin.products.ai-catalog.print', ['ids' => $ids, 'auto_print' => 1]);
    }

    /**
     * Tarayıcıda yazdırılabilir / görüntülenebilir ve doğrudan PDF olarak kaydedilebilir sayfa açar.
     */
    public function printView(Request $request)
    {
        $products = $this->resolveProducts($request);

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'Görüntülenecek ürün bulunamadı.');
        }

        $data = $this->exportService->prepareData($products);

        return view('admin.products.ai-catalog-print', $data);
    }

    /**
     * Excel (.xlsx) dosyasını indirir.
     */
    public function downloadExcel(Request $request)
    {
        $products = $this->resolveProducts($request);
        $options = $this->resolveOptions($request);

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'Dışa aktarılacak ürün bulunamadı.');
        }

        return $this->exportService->exportToExcel($products, $options);
    }

    /**
     * CSV dosyasını indirir.
     */
    public function downloadCsv(Request $request)
    {
        $products = $this->resolveProducts($request);
        $options = $this->resolveOptions($request);

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'Dışa aktarılacak ürün bulunamadı.');
        }

        return $this->exportService->exportToCsv($products, $options);
    }
}
