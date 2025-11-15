<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

class WizLanguageColumn
{
    public static function make()
    {
        return TextColumn::make('language')
            ->toggleable(isToggledHiddenByDefault: true)
            ->sortable()
            ->formatStateUsing(fn($record): ?string => zi_language_name($record->language))
            ->label('Ngôn ngữ');
    }
}
