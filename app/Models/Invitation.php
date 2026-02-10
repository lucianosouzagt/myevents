<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Invitation extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'event_id',
        'user_id',
        'email',
        'whatsapp',
        'guest_name',
        'allowed_guests',
        'confirmed_guests',
        'token',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendee(): HasOne
    {
        return $this->hasOne(Attendee::class);
    }

    public function checkins()
    {
        return $this->hasManyThrough(Checkin::class, Attendee::class);
    }

    public function logs()
    {
        return $this->hasMany(InvitationLog::class);
    }
}
