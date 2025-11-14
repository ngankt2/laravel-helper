<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Filters;

use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class FilterHasDataInField
{
    public static function make($field, $label)
    {
        return Filter::make('has_'.$field)
            ->label($label)
            ->query(fn (EloquentBuilder $query, array $data): EloquentBuilder => match ($data['value'] ?? null) {
                'has' => $query->whereNotNull($field)->where($field, '!=', ''),
                'none' => $query->where(function ($q) use ($field) {
                    $q->whereNull($field)->orWhere($field, '');
                }),
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
                    ])
                    ->placeholder('Chọn trạng thái'),
            ]);
    }
}
