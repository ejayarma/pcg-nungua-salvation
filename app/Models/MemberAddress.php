<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Nnjeim\World\Models\State;

class MemberAddress extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
