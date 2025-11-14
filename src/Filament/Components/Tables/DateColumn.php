<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class DateColumn
{
    public static function make($field)
    {
        return TextColumn::make($field)
            ->date('d/m/Y')
            ->placeholder('--/--/---')
            ->sortable();
    }
}
