<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Namu\WireChat\Traits\Chatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use Chatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'remember_token',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Returns the URL for the user's cover image (avatar).
     * Adjust the 'avatar_url' field to your database setup.
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->avatar_url
            ?? ($this->profile_photo ? asset('storage/' . $this->profile_photo) : null)
            ?? asset('assets/img/profile-img.jpg');
    }

    public function canCreateGroups(): bool
    {
        return in_array($this->role, ['admin', 'supplier', 'system']);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'supplier_id');
    }
}
