<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarbecueItemType extends Model
{
    protected $fillable = [
        'barbecue_category_id',
        'name',
        'unit',
        'default_per_adult',
        'default_per_child',
        'active',
    ];

    protected $casts = [
        'default_per_adult' => 'float',
        'default_per_child' => 'float',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarbecueCategory::class, 'barbecue_category_id');
    }
}

