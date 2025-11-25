<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Carbon;

class WizDatePicker
{
    public static function make($field,$defaultNow = null): DatePicker
    {
        $datePicker = DatePicker::make($field)
            ->native(false)
            ->displayFormat('d/m/Y')
            ->locale('vi');
        if ($defaultNow) {
            $datePicker->default(Carbon::now()->format('d/m/Y'));
        }
        return $datePicker;
    }
}
