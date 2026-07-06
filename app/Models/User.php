<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function score()
    {
        return $this->hasOne(CustomerScore::class);
    }

    public function events()
    {
        return $this->hasMany(CustomerEvent::class)->latest('created_at');
    }

    public function segments()
    {
        return $this->belongsToMany(CustomerSegment::class, 'customer_segment_user')
            ->withPivot('added_at');
    }

    public function notes()
    {
        return $this->hasMany(CustomerNote::class)->latest();
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->orders()->sum('grand_total');
    }

    public function getOrderCountAttribute(): int
    {
        return $this->orders()->count();
    }
}
