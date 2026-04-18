<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MessageBroadcast extends Model implements Auditable
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'message',
        'medium',
        'recipients',
        'recipient_count',
        'recipient_group',
        'created_by',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'recipients' => 'array',
        'status' => \App\Enums\MessageBroadcastStatusEnum::class,
        'recipient_group' => \App\Enums\MessageBroadcastRecipientEnum::class,
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
