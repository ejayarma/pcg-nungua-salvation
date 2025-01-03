<?php

namespace App\Traits;

trait EnumValues
{

    /**
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the enum cases as a collection with key-value pairs.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function keyValueCollection(): \Illuminate\Support\Collection
    {
        return collect(self::cases())->map(function ($case) {
            return ['key' => $case->value];
        });
    }
}
