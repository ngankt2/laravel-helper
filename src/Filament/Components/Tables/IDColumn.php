<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class IDColumn
{
    public static function make(): TextColumn
    {

        return TextColumn::make('id')
            ->searchable()
            ->label('ID')
            ->width(10)
            ->sortable()
            ->toggleable()
            ->toggledHiddenByDefault();
    }
}
