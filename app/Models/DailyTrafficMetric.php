<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailyTrafficMetric extends Model
{
    protected $fillable = [
        'date',
        'unique_visitors_count',
        'page_views_count',
        'sessions_count',
        'cart_additions_count',
        'checkout_starts_count',
        'orders_count',
        'orders_revenue',
        'device_stats',
        'source_stats',
        'top_paths',
        'avg_duration_seconds',
        'analysis_summary',
    ];

    protected $casts = [
        'date' => 'string',
        'device_stats' => 'array',
        'source_stats' => 'array',
        'top_paths' => 'array',
        'orders_revenue' => 'decimal:2',
        'unique_visitors_count' => 'integer',
        'page_views_count' => 'integer',
        'sessions_count' => 'integer',
        'cart_additions_count' => 'integer',
        'checkout_starts_count' => 'integer',
        'orders_count' => 'integer',
        'avg_duration_seconds' => 'integer',
    ];

    /**
     * Belirli bir tarih için metriği getirir veya oluşturur.
     */
    public static function getOrCreateForDate(?Carbon $date = null): self
    {
        $dateStr = $date ? $date->toDateString() : now()->toDateString();

        $existing = static::where('date', $dateStr)->first() ?? static::whereDate('date', $dateStr)->first();
        if ($existing) {
            return $existing;
        }

        try {
            return static::create([
                'date' => $dateStr,
                'unique_visitors_count' => 0,
                'page_views_count' => 0,
                'sessions_count' => 0,
                'cart_additions_count' => 0,
                'checkout_starts_count' => 0,
                'orders_count' => 0,
                'orders_revenue' => 0.00,
                'device_stats' => ['mobile' => 0, 'desktop' => 0, 'tablet' => 0],
                'source_stats' => [],
                'top_paths' => [],
                'avg_duration_seconds' => 0,
            ]);
        } catch (\Throwable $e) {
            return static::where('date', $dateStr)->first() ?? static::whereDate('date', $dateStr)->first() ?? static::first();
        }
    }
}
