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

        $this->ensureTableExists();

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

        $previousPath = $visitor->current_path;

        $cleanTitle = ActiveVisitor::cleanTitle($title);
        $pageInfo = ActiveVisitor::resolvePageInfo($path, $title);
        $visitor->current_url = $url;
        $visitor->current_path = $path;
        $visitor->current_title = (!empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0)
            ? $cleanTitle
            : $pageInfo['title'];
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
        $token = $request->input('visitor_token');
        if (empty($token)) {
            return response()->json(['status' => 'ignored', 'message' => 'Token missing'], 400);
        }

        $this->ensureTableExists();

        $visitor = ActiveVisitor::firstOrNew(['visitor_token' => $token]);
        if (!$visitor->exists) {
            $visitor->first_seen_at = now();
            $visitor->current_url = $request->input('url', url('/checkout'));
            $visitor->current_path = $request->input('path', '/checkout');
            $visitor->current_title = 'Ödeme Sayfası (Checkout)';
            $visitor->ip_address = $request->ip();
        }

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

        $visitor->save();

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

        $guestName = $guestName !== null ? trim($guestName) : null;
        $guestEmail = $guestEmail !== null ? strtolower(trim($guestEmail)) : null;
        $guestPhone = $guestPhone !== null ? trim($guestPhone) : null;

        // 1. Canlı İsim Güncellemesi (Ad Soyad yazılırken)
        if ($guestName !== null && $guestName !== '' && $visitor->guest_name !== $guestName) {
            $visitor->guest_name = $guestName;
            $hasChanges = true;
        }

        // 2. Canlı E-posta Güncellemesi
        if ($guestEmail !== null && $guestEmail !== '' && $visitor->guest_email !== $guestEmail) {
            $visitor->guest_email = $guestEmail;
            $hasChanges = true;
        }

        // 3. Canlı Telefon Güncellemesi
        if ($guestPhone !== null && $guestPhone !== '' && $visitor->guest_phone !== $guestPhone) {
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
                    $visitor->guest_name = $matchedUser->name;
                }
                $hasChanges = true;
            } elseif ($validEmail && $last10Phone) {
                // B) Sistemde kayıtlı değilse numara ve mail bilgileri girilince müşteri tanımla
                try {
                    $customerName = !empty($effectiveName) ? $effectiveName : 'Müşteri';
                    $newUser = \App\Models\User::withoutEvents(function () use ($customerName, $validEmail, $effectivePhone) {
                        return \App\Models\User::create([
                            'name' => $customerName,
                            'email' => $validEmail,
                            'phone' => $effectivePhone,
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
            $cartUpdates = [];
            if (!empty($visitor->guest_name) && $cart->guest_name !== $visitor->guest_name) {
                $cartUpdates['guest_name'] = $visitor->guest_name;
            }
            if (!empty($visitor->guest_email) && $cart->guest_email !== $visitor->guest_email) {
                $cartUpdates['guest_email'] = $visitor->guest_email;
            }
            if (!empty($visitor->guest_phone) && $cart->guest_phone !== $visitor->guest_phone) {
                $cartUpdates['guest_phone'] = $visitor->guest_phone;
            }
            if ($visitor->user_id && !$cart->user_id) {
                $cartUpdates['user_id'] = $visitor->user_id;
            }
            if (!empty($cartUpdates)) {
                $cart->update($cartUpdates);
            }
        }

        if ($hasChanges) {
            $visitor->save();
        }
    }

    protected function ensureTableExists(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('active_visitors')) {
            try {
                \Illuminate\Support\Facades\Schema::create('active_visitors', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('visitor_token', 64)->index();
                    $table->string('session_id', 191)->nullable()->index();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
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
            } catch (\Throwable $e) {}
        }
    }
}
