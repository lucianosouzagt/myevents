<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminRole extends Model
{
    protected $table = 'admin_roles';
    protected $fillable = ['name','slug'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(AdminUser::class, 'admin_role_user', 'role_id', 'admin_user_id');
    }
}

