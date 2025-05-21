<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

use Spatie\Activitylog\Models\Activity;

class CustomActivity extends Activity
{
    public function subject(): MorphTo
    {
        return $this->morphTo()->withTrashed(); 
    }
}
