<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Filters;

use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class FilterHasDataInRelation
{
    public static function make(string $relation, string $label)
    {
        return Filter::make('has_'.$relation)
            ->label($label)
            ->query(fn (EloquentBuilder $query, array $data): EloquentBuilder => match ($data['value'] ?? null) {
                'has' => $query->whereHas($relation),
                'none' => $query->whereDoesntHave($relation),
                default => $query,
            })
            ->indicateUsing(function (array $data) use ($label): ?string {
                return match ($data['value'] ?? null) {
                    'has' => 'Có '.$label,
                    'none' => 'Không có '.$label,
                    default => null,
                };
            })
            ->schema([
                Select::make('value')
                    ->label('Trạng thái '.$label)
                    ->options([
                        'has' => 'Có '.$label,
                        'none' => 'Không có '.$label,
                    ]),
            ]);
    }
}
