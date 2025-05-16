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
            ->toScale(2, RoundingMode::DOWN)
            ->__toString(); // ✅ 回傳 string 給 Livewire 用
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return BigDecimal::of($value)
            ->toScale(6, RoundingMode::DOWN)
            ->__toString(); // ✅ 存入也轉為字串
    }
}