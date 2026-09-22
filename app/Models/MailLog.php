<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MailLog extends Model
{
    // Model::unguard() kuralları gereği, fillable zorunlu olmasa da tanımlıyoruz
    protected $guarded = [];

    protected $fillable = [
        'mailable_class',
        'to_email',
        'to_name',
        'subject',
        'status',
        'error_message',
        'sent_at',
        'metadata',
        'loggable_type',
        'loggable_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Polymorphic relation (örn. sipariş, kullanıcı vb. ile ilişkili log)
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // --- Scope'lar ---

    public function scopeSent(Builder $query): void
    {
        $query->where('status', 'sent');
    }

    public function scopeFailed(Builder $query): void
    {
        $query->where('status', 'failed');
    }

    public function scopeQueued(Builder $query): void
    {
        $query->where('status', 'queued');
    }

    public function scopeForEmail(Builder $query, string $email): void
    {
        $query->where('to_email', $email);
    }

    // --- Accessors ---

    /**
     * Kullanıcı dostu durum etiketi (Label)
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'queued' => 'Kuyrukta',
            'sending' => 'Gönderiliyor',
            'sent' => 'Gönderildi',
            'failed' => 'Başarısız',
            default => 'Bilinmiyor',
        };
    }
}
