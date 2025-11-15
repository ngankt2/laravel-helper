<?php

namespace Ngankt2\LaravelHelper\Filament\Traits\Languages;

use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Filament\Tables;
use Livewire\Attributes\Url;

trait ResourceHasTranslations
{
    #[Url]
    public string $translate = '_all';

    public function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return $this->buildTableQuery();
    }

    /**
     * @return ActionGroup
     *                     Hiển thị trong table với việc shơư bangr 1 vaf cần hiển thị cột danh sách các ngôn ngữ
     *                     Giúp bâấm vào 1 ngôn ngữ có thể edit luôn
     */
    protected static function getLanguageDropdownTableAction(string $routeName): ActionGroup
    {
        return ActionGroup::make([
            ...array_map(function ($lang) use ($routeName) {
                return EditAction::make()
                    ->label(function ($record) use ($lang) {
                        $hasTranslation = false; // $record->translations->where('language', $lang['code'])->isNotEmpty();
                        $prefix         = $hasTranslation ? 'Edit ' : 'Add ';

                        return $prefix . $lang['name']; // Sử dụng tên ngôn ngữ mới
                    })
                    ->icon('heroicon-o-language')
                    ->color(function ($record) {
                        $hasTranslation = false; // $record->translations->where('language', $lang['code'])->isNotEmpty();

                        return $hasTranslation ? Color::Green : Color::Gray;
                    })
                    ->url(fn($record) => route($routeName, [
                        'record'   => $record->id,
                        'language' => $lang['code'], // Truyền mã ngôn ngữ mới
                    ]));
            }, config('lang.content_languages')), // Dùng mảng mới
        ])
            ->label(__('Edit'))
            ->icon('heroicon-o-pencil-square')
            ->size(Size::Small)
            ->color('secondary')
            ->button();
    }

    protected static function getLanguageColumnForMultipleLanguage(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('translations.language')
            ->label(__('db.languages'))
            ->formatStateUsing(function ($record) {
                $languages = $record->translations->pluck('language');
                $flags     = $languages->map(function ($language) {
                    return zi_language_icon($language);  // Trả về flag tương ứng nếu có, nếu không trả về chuỗi rỗng
                });

                return $flags->implode(', ');
            })
            ->searchable();
    }

    protected static function getNameColumnForMultipleLanguage(): Tables\Columns\TextColumn
    {
        return
            Tables\Columns\TextColumn::make('translations.name')
                ->label(__('db.name'))
                ->formatStateUsing(function ($record) {
                    return $record->translations->first()->name ?? '---';
                })
                ->searchable();

    }

    protected static function getSlugColumnForMultipleLanguage($limit = 50): Tables\Columns\TextColumn
    {
        return
            Tables\Columns\TextColumn::make('translations.slug')
                ->label(__('db.slug'))
                ->formatStateUsing(function ($record) {
                    return $record->translations->first()->slug ?? '---';
                })
                ->limit($limit)
                ->searchable();

    }

    /**
     * Get the header actions for the Page.
     *
     * Đây là function mẫu cho việc sử dụng nút chuyển đổi ngôn ngữ
     * + reset trạng thái về draft
     * + tạo slug mới theo mẫu `slug-lang-{code}`
     *
     * @param string $routeName
     * @param array $paramsInitAfterClone
     * @return ActionGroup
     *
     * @example
     *     protected function getHeaderActions(): array
     *     {
     *         return [
     *             $this->getFormSwitchLanguageButtonHeaderAction(
     *                 self::getRouteName(),
     *                 [
     *                     // reset status về draft khi tạo ngôn ngữ mới
     *                     'status' => \App\Enums\PostStatus::Draft,
     *
     *                     // slug = slug cũ + '-lang-{code}'
     *                     'slug' => function ($record, array $lang) {
     *                         return $record->slug . '-lang-' . $lang['code'];
     *                     },
     *                 ]
     *             ),
     *
     *             DeleteAction::make(),
     *         ];
     *     }
     */

    protected function getFormSwitchLanguageButtonHeaderAction(string $routeName = '', $clone = false, array $paramsInitAfterClone = []): ActionGroup
    {
        // Lấy thông tin ngôn ngữ cùng nhóm cha
        $languageSameRecord = $this->record->getLanguagesSameParent();
        $label              = zi_language_name($this->record->language);

        return ActionGroup::make([
            ...array_map(function ($lang) use ($languageSameRecord, $routeName, $paramsInitAfterClone,$clone) {
                return Action::make('language_' . $lang['code'])
                    ->label($lang['name'])
                    ->icon(function () use ($lang, $languageSameRecord, ) {
                        $languageFromDb = $languageSameRecord->get($lang['code']);

                        return $languageFromDb?->id ? 'heroicon-c-pencil-square' : 'heroicon-c-plus-circle'; // Kiểm tra ngôn ngữ đã có chưa
                    })
                    ->action(function () use ($lang, $languageSameRecord, $routeName, $paramsInitAfterClone,$clone) {
                        $languageFromDb = $languageSameRecord->get($lang['code']);

                        if ($languageFromDb) {
                            return redirect()->route($routeName, ['record' => $languageFromDb->id]); // Đã có bản ghi, chuyển đến bản ghi đó
                        }

                        if ($clone) {
                            $newRecord = $this->record->replicate();
                        }else{
                            $modelClass = get_class($this->record);
                            $newRecord = new $modelClass;
                        }
                        $newRecord->language = $lang['code'];

                        // $paramsReset: gắn / reset các trường cho bản ghi mới
                        foreach ($paramsInitAfterClone as $field => $value) {
                            // Nếu key không phải string → bỏ qua
                            if (!is_string($field)) {
                                continue;
                            }
                            if (is_callable($value)) {
                                // Cho phép dùng callback để tuỳ biến theo record & lang
                                $newRecord->{$field} = $value($this->record, $lang, $newRecord);
                            } else {
                                $newRecord->{$field} = $value;
                            }
                        }
                        $newRecord->language_parent_id = $this->record->language_parent_id;
                        $newRecord->save();

                        return redirect()->route($routeName, ['record' => $newRecord->id]); // Chuyển đến bản ghi mới tạo
                    });
            }, config('lang.content_languages')), // Dùng array of objects mới
        ])
            ->label($label)
            ->outlined()
            ->iconPosition(IconPosition::After)
            ->button();
    }


    protected function getSwitchLanguageInTable(): ActionGroup
    {
        $trans = request()->get('translate', $this->translate ?? '_all');
        $label = zi_language_name($trans);

        return ActionGroup::make([
            Action::make('language_' . '_all')
                ->label(zi_language_name('_all')) // Dùng `code` và `name` từ object
                ->url(function () {
                    // Lấy URL hiện tại và thêm query string translate
                    $currentUrl = $this->getUrl();

                    return $currentUrl . (str_contains($currentUrl, '?') ? '&' : '?') . 'translate=_all'; // Sử dụng `code` thay vì `key`
                }),
            ...array_map(function ($lang) {
                return Action::make('language_' . $lang['code'])
                    ->label($lang['name']) // Dùng `code` và `name` từ object
                    ->url(function () use ($lang) {
                        // Lấy URL hiện tại và thêm query string translate
                        $currentUrl = $this->getUrl();

                        return $currentUrl . (str_contains($currentUrl, '?') ? '&' : '?') . 'translate=' . $lang['code']; // Sử dụng `code` thay vì `key`
                    });
            }, config('lang.content_languages')), // Dùng array object,

        ])
            ->label($label)
            ->color('gray')
            ->icon(LucideIcon::ChevronDown)
            ->iconPosition(IconPosition::After)
            ->button();
    }

    /**
     * @return mixed
     *               Gọi trong getTableQuery()
     */
    public function buildTableQuery(): mixed
    {
        return static::getResource()::getEloquentQuery()->when($this->translate != '_all', function ($q) {
            return $q->where('language', $this->translate);
        });
    }
}
