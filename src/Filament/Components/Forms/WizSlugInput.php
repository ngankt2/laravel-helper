<?php

namespace Ngankt2\LaravelHelper\Filament\Components\Forms;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class WizSlugInput
{
    /**
     * @param string        $field
     * @param string        $fieldText
     * @param callable|null $modifyUniqueQuery
     *
     * Ví dụ:
     * WizSlugInput::make('slug', 'title');
     */
    public static function make(string $field, string $fieldText): TextInput
    {
        return TextInput::make($field)
            ->suffixActions([
                // 1. Tạo slug từ tiêu đề
                Action::make('generateSlugFromTitle')
                    ->label('Từ tiêu đề')
                    ->tooltip('Tạo slug tự động từ tiêu đề')
                    ->icon('heroicon-m-sparkles')
                    ->action(function (Get $get, Set $set) use ($field, $fieldText) {
                        $title = $get($fieldText) ?? '';

                        if (! $title) {
                            return;
                        }

                        $set($field, Str::slug($title));
                    }),

                // 2. Chuẩn hoá chính nội dung slug hiện tại
                Action::make('normalizeSlug')
                    ->label('Chuẩn hoá')
                    ->tooltip('Chuẩn hoá slug hiện tại cho đúng format')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->action(function (Get $get, Set $set) use ($field) {
                        $currentSlug = $get($field) ?? '';

                        if (! $currentSlug) {
                            return;
                        }

                        $set($field, Str::slug($currentSlug));
                    }),
            ]);
    }
}
