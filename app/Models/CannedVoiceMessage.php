<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CannedVoiceMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'audio_path',
        'coupon_code',
        'action_button',
        'action_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Ses dosyasının tam web erişim URL'i
     */
    public function getAudioUrlAttribute(): ?string
    {
        if (!empty($this->audio_path)) {
            // Eğer doğrudan tam URL verilmişse (http/https)
            if (str_starts_with($this->audio_path, 'http://') || str_starts_with($this->audio_path, 'https://')) {
                return $this->audio_path;
            }

            return asset('storage/' . ltrim($this->audio_path, '/'));
        }

        return null;
    }

    /**
     * Ses kaydının diskte fiziksel olarak mevcut olup olmadığını doğrular
     */
    public function hasAudio(): bool
    {
        if (empty($this->audio_path)) {
            return false;
        }

        if (str_starts_with($this->audio_path, 'http://') || str_starts_with($this->audio_path, 'https://')) {
            return true;
        }

        return Storage::disk('public')->exists($this->audio_path);
    }
}
