<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'related_type',
        'related_id',
        'message',
        'scheduled_at',
        'sent'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent' => 'boolean'
    ];

    public function related()
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }
}
