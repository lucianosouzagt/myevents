<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable, HasUuids;

    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'two_factor_enabled',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_expires_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Models\AdminRole::class, 'admin_role_user', 'admin_user_id', 'role_id');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }
}
