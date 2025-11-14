<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Tables\Columns\ToggleColumn;

class WizToggleColumn
{
    public static function make($field)
    {
        return ToggleColumn::make($field)
            ->sortable()
            ->onColor('success')
            ->offColor('danger')
            ->onIcon('heroicon-o-eye')
            ->offIcon('heroicon-o-eye-slash');
    }
}
