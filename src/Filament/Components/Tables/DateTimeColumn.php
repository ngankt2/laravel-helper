<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class DateTimeColumn
{
    public static function make($field)
    {
        return TextColumn::make($field)
            ->date('H:i d/m/Y')
            ->placeholder('--/--/---')
            ->sortable();
    }
}
