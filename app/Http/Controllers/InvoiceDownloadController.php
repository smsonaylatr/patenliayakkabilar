<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GibEArsivService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InvoiceDownloadController extends Controller
{
    /**
     * Faturanın HTML belgesini gösterir / indirir
     */
    public function show(Order $order, Request $request)
    {
        // Güvenlik doğrulaması (Genişletilebilir)
        if (!$order->is_invoiced || empty($order->gib_invoice_uuid)) {
            abort(404, 'Bu siparişe ait GİB E-Arşiv faturası bulunamadı.');
        }

        $html = $order->gib_invoice_html;

        // Veritabanında HTML yoksa GİB portalından çekmeye çalışalım
        if (empty($html)) {
            $service = app(GibEArsivService::class);
            $html = $service->getInvoiceHtml($order->gib_invoice_uuid);

            if ($html) {
                $order->update(['gib_invoice_html' => $html]);
            }
        }

        if (empty($html)) {
            return response("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>Fatura HTML Belgesi Alınamadı</h2><p>Fatura GİB portalında oluşturuldu ancak HTML içeriği indirilemedi.</p></div>", 404);
        }

        // İndirme parametresi varsa HTML olarak indir
        if ($request->has('download')) {
            $fileName = 'fatura_' . ($order->gib_invoice_number ?: $order->order_number) . '.html';
            return Response::make($html, 200, [
                'Content-Type' => 'text/html; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        // Doğrudan tarayıcıda yazdırılabilir biçimde göster
        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
