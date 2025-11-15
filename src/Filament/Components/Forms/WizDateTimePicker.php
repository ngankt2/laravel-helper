<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Forms\Components\DateTimePicker;
use Illuminate\Support\Carbon;

class WizDateTimePicker
{
    public static function make($field): DateTimePicker
    {
        return DateTimePicker::make($field)
            ->native(false)
            ->format('H:i d/m/Y')
            ->displayFormat('H:i d/m/Y')
            ->locale('vi')
            ->default(Carbon::now()->format('H:i d/m/Y'));
    }
}
