<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActiveVisitor;
use App\Models\Cart;
use App\Models\GuestProfile;
use App\Services\VisitorBehaviorAnalyzer;
use App\Services\TrafficAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function __construct(
        protected VisitorBehaviorAnalyzer $behaviorAnalyzer,
        protected TrafficAnalyticsService $trafficAnalytics
    ) {}

    public function heartbeat(Request $request): JsonResponse
    {
        $token = \Illuminate\Support\Str::limit(trim((string) $request->input('visitor_token')), 100, '');
        if (empty($token)) {
            return response()->json(['status' => 'ignored', 'message' => 'Token missing'], 400);
        }

        $this->ensureTableExists();

        $url = \Illuminate\Support\Str::limit(trim((string) $request->input('url', '/')), 500, '');
        $path = \Illuminate\Support\Str::limit(trim((string) $request->input('path', '/')), 255, '');
        $title = \Illuminate\Support\Str::limit(trim((string) $request->input('title', 'Patenli Ayakkabılar')), 255, '');
        $referrer = $request->filled('referrer') ? \Illuminate\Support\Str::limit(trim((string) $request->input('referrer')), 500, '') : null;
        $screen = $request->filled('screen') ? \Illuminate\Support\Str::limit(trim((string) $request->input('screen')), 50, '') : null;
        $action = \Illuminate\Support\Str::limit(trim((string) $request->input('action', 'heartbeat')), 50, '');
        $actionDetail = $request->filled('action_detail') ? \Illuminate\Support\Str::limit(trim((string) $request->input('action_detail')), 255, '') : null;
        $userAgent = \Illuminate\Support\Str::limit((string) ($request->userAgent() ?? ''), 500, '');
        $ip = \Illuminate\Support\Str::limit((string) $request->ip(), 50, '');

        // 1. Cihaz, Tarayıcı ve İşletim Sistemi Tespiti
        $deviceInfo = $this->parseUserAgent($userAgent);

        // 2. Kalıcı Misafir Profilini Getir veya Oluştur (Kalıcı ID ve Geliş Sayacı)
        $profile = null;
        try {
            $profile = GuestProfile::firstOrNew(['visitor_token' => $token]);
            $isNewProfile = !$profile->exists;
            $guestId = $profile->guest_id ?: GuestProfile::generateGuestId($token);

            if ($isNewProfile) {
                $profile->guest_id = $guestId;
                $profile->first_seen_at = now();
                $profile->last_visit_at = now();
                $profile->visit_count = 1;
                $profile->total_page_views = 1;
                if ($referrer) {
                    $profile->referrer = $referrer;
                    $profile->referrer_host = parse_url($referrer, PHP_URL_HOST);
                }
                if ($request->filled('utm_source')) {
                    $profile->utm_source = $request->input('utm_source');
                    $profile->utm_campaign = $request->input('utm_campaign');
                }
            } else {
                // Eğer son hareket üzerinden 30 dakikadan fazla geçmişse bu yeni bir geliş/ziyarettir!
                $lastHeartbeat = $profile->last_heartbeat_at;
                if ($lastHeartbeat && $lastHeartbeat->lt(now()->subMinutes(30))) {
                    $profile->visit_count = ($profile->visit_count ?: 1) + 1;
                    $profile->last_visit_at = now();
                }
                $profile->total_page_views = ($profile->total_page_views ?: 0) + 1;
            }

            $profile->last_heartbeat_at = now();
            $profile->ip_address = $ip;
            $profile->user_agent = $userAgent;
            $profile->device_type = $deviceInfo['device'];
            $profile->browser = $deviceInfo['browser'];
            $profile->operating_system = $deviceInfo['os'];
            if (auth()->check()) {
                $profile->user_id = auth()->id();
            }
            $profile->save();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('GuestProfile save error: ' . $e->getMessage());
        }

        // 3. Aktif Ziyaretçi Kaydını Getir veya Oluştur
        $visitor = ActiveVisitor::firstOrNew(['visitor_token' => $token]);
        if ($visitor->exists) {
            $visitor->refresh();
        }

        if (!$visitor->exists) {
            $visitor->first_seen_at = $profile?->first_seen_at ?? now();
            $visitor->page_views_count = 1;

            if (!empty($ip) && ActiveVisitor::where('ip_address', $ip)->where('is_blocked', true)->exists()) {
                $visitor->is_blocked = true;
            }
        }

        if ($profile) {
            $visitor->guest_profile_id = $profile->id;
            $visitor->guest_id = $profile->guest_id;
            $visitor->visit_count = $profile->visit_count ?: 1;
        } else {
            $visitor->guest_id = GuestProfile::generateGuestId($token);
            $visitor->visit_count = $visitor->visit_count ?: 1;
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

        $previousPath = $visitor->current_path;

        $cleanTitle = ActiveVisitor::cleanTitle($title);
        $pageInfo = ActiveVisitor::resolvePageInfo($path, $title);
        $visitor->current_url = $url;
        $visitor->current_path = $path;
        $visitor->current_title = (!empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0)
            ? $cleanTitle
            : $pageInfo['title'];
        if ($referrer) {
            $refHost = parse_url($referrer, PHP_URL_HOST);
            $ownHost = $request->getHost();
            $isExternal = $refHost && !str_contains(strtolower($refHost), strtolower($ownHost));
            $currentIsInternal = !empty($visitor->referrer_host) && str_contains(strtolower($visitor->referrer_host), strtolower($ownHost));

            if (empty($visitor->referrer) || ($currentIsInternal && $isExternal)) {
                $visitor->referrer = $referrer;
                $visitor->referrer_host = $refHost;
            }
        }

        // UTM Parametreleri
        if ($request->filled('utm_source') && empty($visitor->utm_source)) {
            $visitor->utm_source = $request->input('utm_source');
            $visitor->utm_campaign = $request->input('utm_campaign');
        }

        // 3. Sepet Durumunu İlişkilendir
        $cart = null;
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->with(['items.product.images', 'items.variant'])->first();
        } elseif ($request->hasSession()) {
            $cart = Cart::where('session_id', $request->session()->getId())->with(['items.product.images', 'items.variant'])->first();
        }

        if ($cart) {
            if ($cart->items->isNotEmpty()) {
                $visitor->cart_id = $cart->id;
                $visitor->cart_items_count = $cart->items->sum('quantity');
                $visitor->cart_total = $cart->items->sum(fn ($i) => ($i->price ?? 0) * ($i->quantity ?? 1));

                $summary = [];
                foreach ($cart->items as $item) {
                    $color = $item->variant?->color;
                    $size = $item->variant?->size;
                    $itemProduct = $item->product;
                    $itemImage = $itemProduct ? ($itemProduct->images->first()?->image_url ?? $itemProduct->images->first()?->raw_image_url) : null;
                    $summary[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $itemProduct?->name ?? 'Patenli Ayakkabı',
                        'product_image' => $itemImage,
                        'product_url' => $itemProduct ? ('/urun/' . $itemProduct->slug) : null,
                        'size' => is_array($size) ? implode(', ', $size) : ($size ?? null),
                        'color' => is_array($color) ? implode(', ', $color) : ($color ?? null),
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
        }

        // 4. Kimlik (Ad, E-posta, Telefon) Güncelleme ve Müşteri Eşleştirme
        $this->processIdentity(
            $visitor,
            $request->input('guest_name'),
            $request->input('guest_email'),
            $request->input('guest_phone')
        );

        // 5. Davranış Analizi ve Strateji Üretimi
        if ($visitor->exists) {
            $visitor->refresh();
        }

        $this->behaviorAnalyzer->analyze($visitor, [
            'previous_path' => $previousPath,
            'path' => $path,
            'title' => $title,
            'action' => $action,
            'action_detail' => $actionDetail,
        ]);

        $visitor->is_online = true;
        $visitor->last_heartbeat_at = now();
        $visitor->save();

        // Günlük/Haftalık/Aylık Trafik Analizine Kaydet
        $this->trafficAnalytics->recordHit($visitor, [
            'previous_path' => $previousPath,
            'path' => $path,
            'title' => $title,
            'action' => $action,
            'action_detail' => $actionDetail,
            'cart_added' => $action === 'cart_add',
        ]);

        // 6. Engelleme Kontrolü
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

    /**
     * Ziyaretçinin ad, soyad, telefon ve e-posta bilgilerini anlık (live typing) kaydeder
     * ve gerekirse kayıtlı müşteriyle eşleştirir veya yeni müşteri tanımlar.
     */
    public function identify(Request $request): JsonResponse
    {
        $token = \Illuminate\Support\Str::limit(trim((string) $request->input('visitor_token')), 100, '');
        if (empty($token)) {
            return response()->json(['status' => 'ignored', 'message' => 'Token missing'], 400);
        }

        $this->ensureTableExists();

        $profile = GuestProfile::firstOrNew(['visitor_token' => $token]);
        if (!$profile->exists) {
            $profile->guest_id = GuestProfile::generateGuestId($token);
            $profile->first_seen_at = now();
            $profile->last_visit_at = now();
            $profile->visit_count = 1;
            try { $profile->save(); } catch (\Throwable $e) {}
        }

        $visitor = ActiveVisitor::firstOrNew(['visitor_token' => $token]);
        if (!$visitor->exists) {
            $visitor->first_seen_at = $profile->first_seen_at ?? now();
            $visitor->current_url = \Illuminate\Support\Str::limit(trim((string) $request->input('url', url('/checkout'))), 500, '');
            $visitor->current_path = \Illuminate\Support\Str::limit(trim((string) $request->input('path', '/checkout')), 255, '');
            $visitor->current_title = \Illuminate\Support\Str::limit(trim((string) $request->input('title', 'Ödeme Sayfası (Checkout)')), 255, '');
            $visitor->ip_address = \Illuminate\Support\Str::limit((string) $request->ip(), 50, '');
        }

        $visitor->guest_profile_id = $profile->id;
        $visitor->guest_id = $profile->guest_id;
        $visitor->visit_count = $profile->visit_count ?: 1;

        if ($request->hasSession()) {
            $visitor->session_id = $request->session()->getId();
        }

        $visitor->is_online = true;
        $visitor->last_heartbeat_at = now();

        $this->processIdentity(
            $visitor,
            $request->input('guest_name'),
            $request->input('guest_email'),
            $request->input('guest_phone')
        );

        try {
            $visitor->save();
        } catch (\Throwable $e) {
            $this->ensureTableExists();
            try {
                $visitor->save();
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::warning('ActiveVisitor identify save error: ' . $e2->getMessage());
            }
        }

        return response()->json([
            'status' => 'ok',
            'display_name' => $visitor->display_name,
            'is_identified' => (bool) $visitor->is_identified,
            'user_id' => $visitor->user_id,
        ]);
    }

    /**
     * Kimlik işleme, müşteri eşleştirme ve müşteri tanımlama motoru
     */
    public function processIdentity(ActiveVisitor $visitor, ?string $guestName, ?string $guestEmail, ?string $guestPhone): void
    {
        $hasChanges = false;

        // 0. Temel Girdi Temizliği & String Truncation Koruması
        if ($guestName !== null) {
            $guestName = trim(strip_tags((string) $guestName));
            $guestName = mb_substr($guestName, 0, 100);
            if ($guestName === '') $guestName = null;
        }

        if ($guestEmail !== null) {
            $guestEmail = strtolower(trim((string) $guestEmail));
            $guestEmail = mb_substr($guestEmail, 0, 100);
            if ($guestEmail === '') $guestEmail = null;
        }

        if ($guestPhone !== null) {
            $guestPhone = trim((string) $guestPhone);
            $cleanDigits = preg_replace('/[^0-9]/', '', $guestPhone);

            // Aşırı uzun veri girilmişse (bot, bozuk autofill veya saçma payload)
            if (strlen($cleanDigits) > 20) {
                // İçinde geçerli bir Türkiye cep telefonu (05xx veya 5xx) arayalım
                if (preg_match('/(0?5[0-9]{9})/', $cleanDigits, $m)) {
                    $guestPhone = str_starts_with($m[1], '0') ? $m[1] : ('0' . $m[1]);
                } else {
                    $guestPhone = null; // Çöp veriyi veritabanına sokma
                }
            } elseif (strlen($cleanDigits) >= 10) {
                // Standart 10-15 haneli telefon
                if (strlen($cleanDigits) === 10) {
                    $guestPhone = '0' . $cleanDigits;
                } elseif (strlen($cleanDigits) === 11 && str_starts_with($cleanDigits, '90')) {
                    $guestPhone = '0' . substr($cleanDigits, 2);
                } elseif (strlen($cleanDigits) === 12 && str_starts_with($cleanDigits, '90')) {
                    $guestPhone = '+' . $cleanDigits;
                } else {
                    $guestPhone = substr($cleanDigits, 0, 20);
                }
            } else {
                // Henüz tamamlanmamış telefon girişi (örn: 0532)
                $safeChars = preg_replace('/[^0-9+\s()-]/', '', $guestPhone);
                $guestPhone = substr($safeChars, 0, 20);
                if ($guestPhone === '') $guestPhone = null;
            }
        }

        // 1. Canlı İsim Güncellemesi (Ad Soyad yazılırken)
        if ($guestName !== null && $visitor->guest_name !== $guestName) {
            $visitor->guest_name = $guestName;
            $hasChanges = true;
        }

        // 2. Canlı E-posta Güncellemesi
        if ($guestEmail !== null && $visitor->guest_email !== $guestEmail) {
            $visitor->guest_email = $guestEmail;
            $hasChanges = true;
        }

        // 3. Canlı Telefon Güncellemesi
        if ($guestPhone !== null && $visitor->guest_phone !== $guestPhone) {
            $visitor->guest_phone = $guestPhone;
            $hasChanges = true;
        }

        // 4. Müşteri Tanımlama / Eşleştirme (Numara ve Mail girilince)
        $effectivePhone = $guestPhone ?: $visitor->guest_phone;
        $effectiveEmail = $guestEmail ?: $visitor->guest_email;
        $effectiveName = $guestName ?: $visitor->guest_name;

        $cleanedPhone = $effectivePhone ? preg_replace('/[^0-9]/', '', $effectivePhone) : null;
        $last10Phone = ($cleanedPhone && strlen($cleanedPhone) >= 10) ? substr($cleanedPhone, -10) : null;
        $validEmail = ($effectiveEmail && filter_var($effectiveEmail, FILTER_VALIDATE_EMAIL)) ? $effectiveEmail : null;

        if (!$visitor->user_id) {
            // A) Mevcut kayıtlı müşteriler arasında eşleşme ara
            $matchedUser = null;
            if ($validEmail || $last10Phone) {
                $userQuery = \App\Models\User::query();
                if ($validEmail && $last10Phone) {
                    $userQuery->where(function ($q) use ($validEmail, $last10Phone) {
                        $q->where('email', $validEmail)
                          ->orWhere('phone', 'like', "%{$last10Phone}%");
                    });
                } elseif ($validEmail) {
                    $userQuery->where('email', $validEmail);
                } elseif ($last10Phone) {
                    $userQuery->where('phone', 'like', "%{$last10Phone}%");
                }
                $matchedUser = $userQuery->first();
            }

            if ($matchedUser) {
                $visitor->user_id = $matchedUser->id;
                $visitor->is_identified = true;
                if (empty($visitor->guest_name)) {
                    $visitor->guest_name = mb_substr($matchedUser->name, 0, 100);
                }
                $hasChanges = true;
            } elseif ($validEmail && $last10Phone) {
                // B) Sistemde kayıtlı değilse numara ve mail bilgileri girilince müşteri tanımla
                try {
                    $customerName = !empty($effectiveName) ? mb_substr($effectiveName, 0, 100) : 'Müşteri';
                    $newUser = \App\Models\User::withoutEvents(function () use ($customerName, $validEmail, $effectivePhone) {
                        return \App\Models\User::create([
                            'name' => $customerName,
                            'email' => $validEmail,
                            'phone' => $effectivePhone ? mb_substr((string) $effectivePhone, 0, 25) : null,
                            'role' => 'customer',
                            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                        ]);
                    });

                    if ($newUser) {
                        $visitor->user_id = $newUser->id;
                        $visitor->is_identified = true;
                        $hasChanges = true;
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Canlı ziyaretçi müşteri oluşturma uyarısı: ' . $e->getMessage());
                }
            }
        }

        // 5. Ziyaretçinin Sepetini (Cart) Güncelle ve Senkronize Et
        $cart = null;
        if ($visitor->cart_id) {
            $cart = \App\Models\Cart::find($visitor->cart_id);
        } elseif ($visitor->session_id) {
            $cart = \App\Models\Cart::where('session_id', $visitor->session_id)->latest()->first();
        }

        if ($cart) {
            try {
                $cartUpdates = [];
                if (!empty($visitor->guest_name) && $cart->guest_name !== $visitor->guest_name) {
                    $cartUpdates['guest_name'] = mb_substr($visitor->guest_name, 0, 100);
                }
                if (!empty($visitor->guest_email) && $cart->guest_email !== $visitor->guest_email) {
                    $cartUpdates['guest_email'] = mb_substr($visitor->guest_email, 0, 100);
                }
                if (!empty($visitor->guest_phone) && $cart->guest_phone !== $visitor->guest_phone) {
                    $cartUpdates['guest_phone'] = mb_substr($visitor->guest_phone, 0, 30);
                }
                if ($visitor->user_id && !$cart->user_id) {
                    $cartUpdates['user_id'] = $visitor->user_id;
                }
                if (!empty($cartUpdates)) {
                    $cart->update($cartUpdates);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Cart guest info update error: ' . $e->getMessage());
            }
        }

        if ($hasChanges) {
            // 6. Kalıcı Misafir Profilini Senkronize Et
            if ($visitor->visitor_token) {
                try {
                    $p = GuestProfile::where('visitor_token', $visitor->visitor_token)->first();
                    if ($p) {
                        $pUpdates = [];
                        if (!empty($visitor->guest_name) && $p->guest_name !== $visitor->guest_name) {
                            $pUpdates['guest_name'] = mb_substr($visitor->guest_name, 0, 100);
                        }
                        if (!empty($visitor->guest_email) && $p->guest_email !== $visitor->guest_email) {
                            $pUpdates['guest_email'] = mb_substr($visitor->guest_email, 0, 100);
                        }
                        if (!empty($visitor->guest_phone) && $p->guest_phone !== $visitor->guest_phone) {
                            $pUpdates['guest_phone'] = mb_substr($visitor->guest_phone, 0, 30);
                        }
                        if ($visitor->user_id && !$p->user_id) {
                            $pUpdates['user_id'] = $visitor->user_id;
                        }
                        if (!empty($pUpdates)) {
                            $p->update($pUpdates);
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('GuestProfile identity update error: ' . $e->getMessage());
                }
            }

            try {
                $visitor->save();
            } catch (\Throwable $e) {
                $this->ensureTableExists();
                try {
                    $visitor->save();
                } catch (\Throwable $e2) {
                    \Illuminate\Support\Facades\Log::warning('ActiveVisitor processIdentity save error: ' . $e2->getMessage());
                }
            }
        }
    }

    protected function ensureTableExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('guest_profiles')) {
                \Illuminate\Support\Facades\Schema::create('guest_profiles', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('guest_id', 20)->unique()->index();
                    $table->string('visitor_token', 64)->unique()->index();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('guest_name', 191)->nullable()->index();
                    $table->string('guest_email', 191)->nullable()->index();
                    $table->string('guest_phone', 50)->nullable()->index();
                    $table->integer('visit_count')->default(1)->index();
                    $table->integer('total_page_views')->default(1);
                    $table->integer('total_time_spent_seconds')->default(0);
                    $table->timestamp('first_seen_at')->useCurrent();
                    $table->timestamp('last_visit_at')->useCurrent()->index();
                    $table->timestamp('last_heartbeat_at')->useCurrent()->index();
                    $table->string('ip_address', 45)->nullable()->index();
                    $table->text('user_agent')->nullable();
                    $table->string('device_type', 20)->default('desktop');
                    $table->string('browser', 50)->nullable();
                    $table->string('operating_system', 50)->nullable();
                    $table->text('referrer')->nullable();
                    $table->string('referrer_host', 100)->nullable();
                    $table->string('utm_source', 100)->nullable();
                    $table->string('utm_campaign', 100)->nullable();
                    $table->timestamps();
                });
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('active_visitors')) {
                \Illuminate\Support\Facades\Schema::create('active_visitors', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('visitor_token', 64)->index();
                    $table->string('guest_id', 20)->nullable()->index();
                    $table->integer('visit_count')->default(1)->index();
                    $table->string('session_id', 191)->nullable()->index();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->foreignId('guest_profile_id')->nullable()->constrained('guest_profiles')->nullOnDelete();
                    $table->string('guest_name', 191)->nullable()->index();
                    $table->string('guest_email', 191)->nullable()->index();
                    $table->string('guest_phone', 50)->nullable()->index();
                    $table->boolean('is_identified')->default(false)->index();
                    $table->string('ip_address', 45)->nullable()->index();
                    $table->text('user_agent')->nullable();
                    $table->string('device_type', 20)->default('desktop');
                    $table->string('browser', 50)->nullable();
                    $table->string('operating_system', 50)->nullable();
                    $table->string('screen_resolution', 20)->nullable();
                    $table->text('current_url');
                    $table->string('current_path', 255)->index();
                    $table->string('current_title', 255)->nullable();
                    $table->text('referrer')->nullable();
                    $table->string('referrer_host', 100)->nullable();
                    $table->string('utm_source', 100)->nullable();
                    $table->string('utm_campaign', 100)->nullable();
                    $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();
                    $table->decimal('cart_total', 10, 2)->default(0.00);
                    $table->integer('cart_items_count')->default(0);
                    $table->json('cart_summary')->nullable();
                    $table->json('journey_trail')->nullable();
                    $table->integer('intent_score')->default(15)->index();
                    $table->string('intent_level', 30)->default('browsing')->index();
                    $table->text('behavior_insight')->nullable();
                    $table->json('recommended_strategy')->nullable();
                    $table->integer('time_spent_seconds')->default(0);
                    $table->integer('page_views_count')->default(1);
                    $table->json('pending_command')->nullable();
                    $table->json('last_command_executed')->nullable();
                    $table->boolean('is_online')->default(true)->index();
                    $table->boolean('is_blocked')->default(false)->index();
                    $table->timestamp('first_seen_at')->useCurrent();
                    $table->timestamp('last_heartbeat_at')->useCurrent()->index();
                    $table->timestamps();

                    $table->index(['last_heartbeat_at', 'is_online']);
                    $table->index(['visitor_token', 'last_heartbeat_at']);
                });
            } else {
                \Illuminate\Support\Facades\Schema::table('active_visitors', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'guest_id')) {
                        $table->string('guest_id', 20)->nullable()->after('visitor_token')->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'visit_count')) {
                        $table->integer('visit_count')->default(1)->after('guest_id')->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'guest_profile_id')) {
                        $table->foreignId('guest_profile_id')->nullable()->after('user_id')->constrained('guest_profiles')->nullOnDelete();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'guest_name')) {
                        $table->string('guest_name', 191)->nullable()->after('user_id')->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'guest_email')) {
                        $table->string('guest_email', 191)->nullable()->after('guest_name')->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'guest_phone')) {
                        $table->string('guest_phone', 50)->nullable()->after('guest_email')->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('active_visitors', 'is_identified')) {
                        $table->boolean('is_identified')->default(false)->after('guest_phone')->index();
                    }
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Filament Yolculuk Modalı için canlı HTML kısmi çıktısı döner.
     */
    public function journeyPartial(Request $request, $id)
    {
        $visitor = ActiveVisitor::with(['user', 'cart.items.product.images', 'cart.items.variant'])->find($id);
        if (!$visitor) {
            return response('<div style="padding: 20px; text-align: center; color: #ef4444;">Ziyaretçi kaydı bulunamadı.</div>', 404);
        }

        return view('filament.pages.partials.visitor-journey-modal', [
            'record' => $visitor,
            'isPartial' => true,
        ]);
    }
}
