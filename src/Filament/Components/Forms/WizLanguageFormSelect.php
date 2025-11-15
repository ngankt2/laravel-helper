<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Forms\Components\Select;

class WizLanguageFormSelect
{
    public static function make($field = 'language'): Select
    {
        return Select::make($field)
            ->label('Ngôn ngữ')
            ->options(function () {
                return collect(config('lang.content_languages', []))
                    ->mapWithKeys(fn($lang) => [$lang['code'] => $lang['name']]);
            })
            ->disabledOn('edit')
            ->required();
    }
}
