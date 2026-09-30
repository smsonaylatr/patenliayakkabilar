<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GibEArsivService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InvoiceDownloadController extends Controller
{
    /**
     * Faturanın PDF veya HTML belgesini gösterir / indirir
     */
    public function show(Order $order, Request $request)
    {
        // Fatura kesilmiş mi ve UUID veya HTML var mı kontrol et
        if ((!$order->is_invoiced || empty($order->gib_invoice_uuid)) && empty($order->gib_invoice_html)) {
            abort(404, 'Bu siparişe ait GİB E-Arşiv faturası bulunamadı.');
        }

        $html = $order->gib_invoice_html;

        // Veritabanında HTML yoksa GİB portalından çekmeye çalışalım
        if (empty($html) && !empty($order->gib_invoice_uuid)) {
            $service = app(GibEArsivService::class);
            $html = $service->getInvoiceHtml($order->gib_invoice_uuid);

            if ($html) {
                $order->update(['gib_invoice_html' => $html]);
            }
        }

        if (empty($html)) {
            return response("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h2>Fatura Belgesi Alınamadı</h2><p>Fatura GİB portalında oluşturuldu ancak belge içeriği alınamadı.</p></div>", 404);
        }

        // HTML formatında özel talep varsa (?format=html)
        if ($request->query('format') === 'html') {
            if ($request->has('download')) {
                $fileName = 'fatura_' . ($order->gib_invoice_number ?: $order->order_number) . '.html';
                return Response::make($html, 200, [
                    'Content-Type' => 'text/html; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }
            return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
        }

        // Standart ve varsayılan: Yüksek Kaliteli PDF Oluştur ve Göster / İndir
        $pdfContent = GibEArsivService::convertHtmlToPdf($html);
        $fileName = 'fatura_' . ($order->gib_invoice_number ?: $order->order_number) . '.pdf';

        if ($pdfContent) {
            $disposition = $request->has('download') ? 'attachment' : 'inline';
            return Response::make($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition . '; filename="' . $fileName . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
        }

        // PDF oluşturulamadıysa güvenli HTML fallback
        if ($request->has('download')) {
            $htmlFileName = 'fatura_' . ($order->gib_invoice_number ?: $order->order_number) . '.html';
            return Response::make($html, 200, [
                'Content-Type' => 'text/html; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $htmlFileName . '"',
            ]);
        }

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
