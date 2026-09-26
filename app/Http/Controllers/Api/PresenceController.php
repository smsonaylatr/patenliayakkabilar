<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActiveVisitor;
use App\Models\Cart;
use App\Services\VisitorBehaviorAnalyzer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function __construct(
        protected VisitorBehaviorAnalyzer $behaviorAnalyzer
    ) {}

    public function heartbeat(Request $request): JsonResponse
    {
        $token = $request->input('visitor_token');
        if (empty($token)) {
            return response()->json(['status' => 'ignored', 'message' => 'Token missing'], 400);
        }

        $url = $request->input('url', '/');
        $path = $request->input('path', '/');
        $title = $request->input('title', 'Patenli Ayakkabılar');
        $referrer = $request->input('referrer');
        $screen = $request->input('screen');
        $action = $request->input('action', 'heartbeat');
        $actionDetail = $request->input('action_detail');
        $userAgent = $request->userAgent() ?? '';
        $ip = $request->ip();

        // 1. Cihaz, Tarayıcı ve İşletim Sistemi Tespiti
        $deviceInfo = $this->parseUserAgent($userAgent);

        // 2. Aktif Ziyaretçi Kaydını Getir veya Oluştur
        $visitor = ActiveVisitor::firstOrNew(['visitor_token' => $token]);

        if (!$visitor->exists) {
            $visitor->first_seen_at = now();
            $visitor->page_views_count = 1;
        }

        $visitor->session_id = $request->hasSession() ? $request->session()->getId() : null;
        if (auth()->check()) {
            $visitor->user_id = auth()->id();
        }

        $visitor->ip_address = $ip;
        $visitor->user_agent = $userAgent;
        $visitor->device_type = $deviceInfo['device'];
        $visitor->browser = $deviceInfo['browser'];
        $visitor->operating_system = $deviceInfo['os'];
        $visitor->screen_resolution = $screen;

        $visitor->current_url = $url;
        $visitor->current_path = $path;
        $visitor->current_title = $title;
        if ($referrer && empty($visitor->referrer)) {
            $visitor->referrer = $referrer;
            $visitor->referrer_host = parse_url($referrer, PHP_URL_HOST);
        }

        // UTM Parametreleri
        if ($request->has('utm_source') && empty($visitor->utm_source)) {
            $visitor->utm_source = $request->input('utm_source');
            $visitor->utm_campaign = $request->input('utm_campaign');
        }

        // 3. Sepet Durumunu İlişkilendir
        $cart = null;
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->with(['items.product', 'items.variant'])->first();
        } elseif ($request->hasSession()) {
            $cart = Cart::where('session_id', $request->session()->getId())->with(['items.product', 'items.variant'])->first();
        }

        if ($cart && $cart->items->isNotEmpty()) {
            $visitor->cart_id = $cart->id;
            $visitor->cart_items_count = $cart->items->sum('quantity');
            $visitor->cart_total = $cart->items->sum(fn ($i) => ($i->price ?? 0) * ($i->quantity ?? 1));

            $summary = [];
            foreach ($cart->items as $item) {
                $summary[] = [
                    'product_name' => $item->product?->name ?? 'Patenli Ayakkabı',
                    'size' => $item->variant?->size ?? null,
                    'color' => $item->variant?->color ?? null,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                ];
            }
            $visitor->cart_summary = $summary;
        } else {
            $visitor->cart_items_count = 0;
            $visitor->cart_total = 0.00;
            $visitor->cart_summary = [];
        }

        // 4. Davranış Analizi ve Strateji Üretimi
        $this->behaviorAnalyzer->analyze($visitor, [
            'path' => $path,
            'title' => $title,
            'action' => $action,
            'action_detail' => $actionDetail,
        ]);

        $visitor->is_online = true;
        $visitor->last_heartbeat_at = now();
        $visitor->save();

        // 5. Engelleme Kontrolü
        if ($visitor->is_blocked) {
            return response()->json([
                'status' => 'blocked',
                'command' => [
                    'action' => 'blocked',
                    'message' => 'Web sitemize erişiminiz sınırlandırılmıştır.',
                ],
            ]);
        }

        // 6. Bekleyen Komut Varsa İlet ve Arşivle
        $command = null;
        if (!empty($visitor->pending_command)) {
            $command = $visitor->pending_command;
            $visitor->last_command_executed = $command;
            $visitor->pending_command = null;
            $visitor->save();
        }

        return response()->json([
            'status' => 'ok',
            'command' => $command,
            'intent_score' => $visitor->intent_score,
            'intent_level' => $visitor->intent_level,
        ]);
    }

    /**
     * User-Agent dizesinden cihaz, tarayıcı ve işletim sistemini ayıklar.
     */
    protected function parseUserAgent(string $ua): array
    {
        $device = 'desktop';
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobile))/i', $ua)) {
            $device = 'tablet';
        } elseif (preg_match('/(mobi|iphone|ipod|android.*mobile|blackberry|iemobile)/i', $ua)) {
            $device = 'mobile';
        }

        $browser = 'Bilinmiyor';
        if (str_contains($ua, 'Edg/')) {
            $browser = 'Edge';
        } elseif (str_contains($ua, 'Chrome/') && !str_contains($ua, 'Edg/')) {
            $browser = 'Chrome';
        } elseif (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome/')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Firefox/')) {
            $browser = 'Firefox';
        } elseif (str_contains($ua, 'Opera/') || str_contains($ua, 'OPR/')) {
            $browser = 'Opera';
        }

        $os = 'Bilinmiyor';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            $os = 'iOS';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'os' => $os,
        ];
    }
}
