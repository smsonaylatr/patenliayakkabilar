<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveVisitor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cart_summary' => 'array',
        'journey_trail' => 'array',
        'recommended_strategy' => 'array',
        'pending_command' => 'array',
        'last_command_executed' => 'array',
        'is_online' => 'boolean',
        'is_blocked' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'cart_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('last_heartbeat_at', '>=', now()->subSeconds(45));
    }

    public function scopeIdle($query)
    {
        return $query->whereBetween('last_heartbeat_at', [now()->subMinutes(5), now()->subSeconds(45)]);
    }

    public function scopeHighIntent($query)
    {
        return $query->where('intent_score', '>=', 60);
    }

    public function scopeHesitating($query)
    {
        return $query->where('intent_level', 'hesitating');
    }

    public function scopeWithCart($query)
    {
        return $query->where('cart_items_count', '>', 0);
    }

    public function scopeMembers($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeGuests($query)
    {
        return $query->whereNull('user_id');
    }

    public function getIsCurrentlyOnlineAttribute(): bool
    {
        return $this->last_heartbeat_at && $this->last_heartbeat_at->gte(now()->subSeconds(45));
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }

        $shortId = strtoupper(substr(str_replace(['pa_vt_', '-'], '', $this->visitor_token), 0, 6));
        return "Misafir #{$shortId}";
    }

    public function getDurationFormattedAttribute(): string
    {
        $seconds = abs((int) ($this->time_spent_seconds ?: 0));
        if ($seconds === 0 && $this->first_seen_at) {
            $seconds = abs((int) $this->first_seen_at->diffInSeconds(now()));
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 1) {
            return "{$remainingSeconds} sn";
        }

        return "{$minutes} dk {$remainingSeconds} sn";
    }

    protected static array $productCache = [];

    public function getCurrentProductAttribute(): ?\App\Models\Product
    {
        if (!empty($this->current_path) && str_starts_with($this->current_path, '/urun/')) {
            $parts = explode('/', trim($this->current_path, '/'));
            $slug = $parts[1] ?? null;
            if ($slug) {
                $slug = explode('?', $slug)[0];
                if (!array_key_exists($slug, static::$productCache)) {
                    static::$productCache[$slug] = \App\Models\Product::where('slug', $slug)->with('images')->first();
                }
                return static::$productCache[$slug];
            }
        }
        return null;
    }

    public function getCurrentProductImageAttribute(): ?string
    {
        $product = $this->current_product;
        if (!$product) return null;
        return $product->images->first()?->image_url ?? $product->images->first()?->raw_image_url;
    }

    /**
     * Ziyaretçiye yönlendirme emri kuyrukla
     */
    public function queueRedirect(string $targetUrl, ?string $message = null, int $countdown = 0): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'redirect',
                'target_url' => $targetUrl,
                'message' => $message,
                'countdown' => $countdown,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiye özel teklif / kupon / bildirim popup'ı fırlat
     */
    public function queueOffer(string $title, string $message, ?string $couponCode = null, ?string $actionButton = null, ?string $actionUrl = null): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'offer',
                'title' => $title,
                'message' => $message,
                'coupon_code' => $couponCode,
                'action_button' => $actionButton,
                'action_url' => $actionUrl,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiye genel alert bildirim göster
     */
    public function queueAlert(string $message, string $title = 'Bildirim', string $type = 'info'): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'alert',
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Sayfayı uzaktan yenilet
     */
    public function queueReload(): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'reload',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Oturumu kapat / sayfadan at
     */
    public function queueKick(): void
    {
        $this->update([
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'kick',
                'target_url' => '/',
                'message' => 'Oturumunuz yönetici tarafından sonlandırıldı.',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Ziyaretçiyi engelle
     */
    public function blockVisitor(): void
    {
        $this->update([
            'is_blocked' => true,
            'pending_command' => [
                'id' => 'cmd_' . uniqid(),
                'action' => 'blocked',
                'message' => 'Web sitemize erişiminiz sınırlandırılmıştır.',
                'created_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
