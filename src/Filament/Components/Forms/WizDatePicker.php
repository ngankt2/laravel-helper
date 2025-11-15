<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class WizDatePicker
{
    public static function make($field): DatePicker
    {
        return DatePicker::make($field)
            ->native(false)
            ->format('d/m/Y')
            ->displayFormat('d/m/Y')
            ->locale('vi')
            ->default(Carbon::now()->format('d/m/Y'));
    }
}
