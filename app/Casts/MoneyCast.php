<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class MoneyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        // 回傳 Brick\Math 的 BigDecimal 實例
        return BigDecimal::of($value)->toScale(6, RoundingMode::DOWN);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        // 存回 DB 前轉成字串（記得固定小數位）
        return BigDecimal::of($value)->toScale(6, RoundingMode::DOWN)->__toString();
    }
}