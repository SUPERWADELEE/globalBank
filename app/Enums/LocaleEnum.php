<?php

namespace App\Enums;

enum LocaleEnum: string
{
    case TraditionalChinese = 'zh_TW';
    case English = 'en';
    case Japanese = 'ja';
    case Korean = 'ko';

    public function label(): string
    {
        return __('admin_user.locales.' . $this->value);

    }
}