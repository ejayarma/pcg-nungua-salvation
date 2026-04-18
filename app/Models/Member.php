<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'date_of_birth',
        'generational_group_id',
        'gender',
        'is_communicant',
        'occupation',
    ];

    public function generationalGroup(): BelongsTo
    {
        return $this->belongsTo(GenerationalGroup::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(MemberAddress::class);
    }

    public function contactPerson(): HasOne
    {
        return $this->hasOne(ContactPerson::class);
    }
}
