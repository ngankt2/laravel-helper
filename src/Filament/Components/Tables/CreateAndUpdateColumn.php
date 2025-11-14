<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class CreateAndUpdateColumn
{
    public static function make($field)
    {
        return TextColumn::make($field)
            ->date('H:i d/m/Y')
            ->placeholder('--/--/---')
            ->description(fn ($record) => 'Cập nhật: '.($record->updated_at ? $record->updated_at->format('H:i d/m/Y') : '--/--/---'))
            ->sortable();
    }
}
