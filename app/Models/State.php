<?php

namespace App\Models;

use Nnjeim\World\Models\Traits\StateRelations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State as ModelsState;
use Nnjeim\World\Models\Traits\WorldConnection;

class State extends ModelsState
{

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
