<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageBroadcast extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'medium',
        'recipients',
        'recipient_count',
        'recipient_group',
        'created_by',
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
