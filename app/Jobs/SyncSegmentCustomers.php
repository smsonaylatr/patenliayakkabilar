<?php

namespace App\Jobs;

use App\Models\CustomerScore;
use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSegmentCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $segments = CustomerSegment::where('type', 'dynamic')->where('is_active', true)->get();

        foreach ($segments as $segment) {
            $conditions = $segment->conditions ?? [];
            if (empty($conditions)) continue;

            $userIds = $this->findMatchingUsers($conditions);

            // Sync: eski eşleşmeleri kaldır, yenilerini ekle
            $segment->customers()->sync(
                collect($userIds)->mapWithKeys(fn ($id) => [$id => ['added_at' => now()]])->toArray()
            );

            $segment->update(['customer_count' => count($userIds)]);
        }

        Log::info("Segment sync completed for {$segments->count()} dynamic segments.");
    }

    private function findMatchingUsers(array $conditions): array
    {
        $query = User::where('role', 'customer');

        // LTV koşulları
        if (isset($conditions['min_ltv'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('lifetime_value', '>=', $conditions['min_ltv'])
            );
        }

        // Risk koşulları
        if (isset($conditions['max_risk'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('risk_score', '<=', $conditions['max_risk'])
            );
        }

        // Son sipariş günü (max)
        if (isset($conditions['max_days_since_order'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('days_since_last_order', '<=', $conditions['max_days_since_order'])
            );
        }

        // Son sipariş günü (min)
        if (isset($conditions['min_days_since_order'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('days_since_last_order', '>=', $conditions['min_days_since_order'])
            );
        }

        // Minimum sipariş sayısı
        if (isset($conditions['min_orders'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('total_orders', '>=', $conditions['min_orders'])
            );
        }

        // Tam sipariş sayısı
        if (isset($conditions['exact_orders'])) {
            $query->whereHas('score', fn ($q) =>
                $q->where('total_orders', '=', $conditions['exact_orders'])
            );
        }

        // Kayıt tarihi (son X gün)
        if (isset($conditions['max_days_since_registration'])) {
            $query->where('created_at', '>=', now()->subDays($conditions['max_days_since_registration']));
        }

        return $query->pluck('id')->toArray();
    }
}
