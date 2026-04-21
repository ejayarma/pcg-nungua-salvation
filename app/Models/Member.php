<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Member extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
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

    public function getRightfulGroup(): string
    {
        $genGroup = '';

        if (! $this->date_of_birth) {
            return 'Unknown';
        }

        $age = now()->diffInYears($this->date_of_birth, true);

        if ($age < 12) {
            $genGroup = 'Children Service';
        } elseif ($age >= 12 && $age < 18) {
            $genGroup = 'JY';
        } elseif ($age >= 18 && $age < 30) {
            $genGroup = 'YPG';
        } elseif ($age >= 30 && $age < 40) {
            $genGroup = 'YAF';
        } else {
            $genGroup = Str::upper($this->gender) === 'MALE' ? "Men's Fellowship" : "Women's Fellowship";
        }

        return $genGroup;
    }
}
