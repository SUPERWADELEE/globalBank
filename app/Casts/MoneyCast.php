<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return BigDecimal::of($value)
            ->toScale(2, RoundingMode::HALF_UP)
            ->__toString(); 
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return BigDecimal::of($value)
            ->toScale(6, RoundingMode::DOWN)
            ->__toString(); 
    }
}