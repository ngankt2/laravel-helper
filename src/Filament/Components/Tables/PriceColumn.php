<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class PriceColumn
{
    public static function make(string $field): TextColumn
    {

        return TextColumn::make($field)
            ->money('VND', true)
            ->alignRight()
            ->color('danger')
            ->sortable();
    }
}
