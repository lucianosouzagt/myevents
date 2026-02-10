<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarbecueCategory extends Model
{
    protected $fillable = ['slug', 'name'];

    public function itemTypes(): HasMany
    {
        return $this->hasMany(BarbecueItemType::class);
    }
}

