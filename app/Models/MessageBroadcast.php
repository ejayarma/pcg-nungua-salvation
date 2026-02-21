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
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
