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
            ->format('Y/m/d H:i:s')
            ->displayFormat('d/m/Y H:i:s')
            ->seconds(true)
            ->default(Carbon::now()->format('Y/m/d H:i:s'))
            ->locale('vi');
    }
}
