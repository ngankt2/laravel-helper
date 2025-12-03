@php
    use Filament\Support\Enums\GridDirection;

    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $isHtmlAllowed = $isHtmlAllowed();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();
    $groupedOptions = $getGroupedOptions();
    $flatOptions = $getFlatOptions();
    $livewireKey = $getLivewireKey();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('checkbox-list', 'filament/forms') }}"
        x-data="checkboxListFormComponent({
                    livewireId: @js($this->getId()),
                })"
        {{ $getExtraAlpineAttributeBag()->class(['fi-fo-checkbox-list']) }}
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <x-filament::input.wrapper
                    inline-prefix
                    :prefix-icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
                    :prefix-icon-alias="\Filament\Forms\View\FormsIconAlias::COMPONENTS_CHECKBOX_LIST_SEARCH_FIELD"
                    class="fi-fo-checkbox-list-search-input-wrp"
                >
                    <input
                        placeholder="{{ $getSearchPrompt() }}"
                        type="search"
                        x-model.debounce.{{ $getSearchDebounce() }}="search"
                        class="fi-input fi-input-has-inline-prefix"
                    />
                </x-filament::input.wrapper>
            @endif

            @if ($isBulkToggleable && count($flatOptions))
                <div
                    x-cloak
                    class="fi-fo-checkbox-list-actions"
                    wire:key="{{ $livewireKey }}.actions"
                >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.select-all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                    <span
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.deselect-all"
                    >
                        {{ $getAction('deselectAll') }}
                    </span>
                </div>
            @endif
        @endif

        <div
            {{
                $getExtraAttributeBag()
                    ->merge([
                        'x-show' => $isSearchable ? 'visibleCheckboxListOptions.length' : null,
                    ], escape: false)
                    ->class([
                        'fi-fo-checkbox-list-groups',
                    ])
            }}
        >
            @forelse ($groupedOptions as $groupKey => $group)
                <div
                    wire:key="{{ $livewireKey }}.group.{{ $groupKey }}"
                    class="fi-fo-checkbox-list-group"
                >
                    @if ($group['label'])
                        <div class="fi-fo-checkbox-list-group-header">
                            @if ($group['icon'])
                                <span class="fi-fo-checkbox-list-group-icon">
                                    @if (str_starts_with($group['icon'], '<svg'))
                                        {!! $group['icon'] !!}
                                    @else
                                        <x-filament::icon
                                            :icon="$group['icon']"
                                            class="h-5 w-5"
                                        />
                                    @endif
                                </span>
                            @endif
                            <h4 class="fi-fo-checkbox-list-group-label">
                                {{ $group['label'] }}
                            </h4>
                        </div>
                    @endif

                    <div
                        {{
                            $getExtraAttributeBag()
                                ->grid($getColumns(), $gridDirection)
                                ->class([
                                    'fi-fo-checkbox-list-options',
                                    'fi-fo-checkbox-list-options-grouped' => $group['label'],
                                ])
                        }}
                    >
                        @forelse ($group['options'] as $value => $label)
                            <div
                                wire:key="{{ $livewireKey }}.options.{{ $value }}"
                                @if ($isSearchable)
                                    x-show="
                                        $el
                                            .querySelector('.fi-fo-checkbox-list-option-label')
                                            ?.innerText.toLowerCase()
                                            .includes(search.toLowerCase()) ||
                                            $el
                                                .querySelector('.fi-fo-checkbox-list-option-description')
                                                ?.innerText.toLowerCase()
                                                .includes(search.toLowerCase())
                                    "
                                @endif
                                class="fi-fo-checkbox-list-option-ctn"
                            >
                                <label class="fi-fo-checkbox-list-option">
                                    <input
                                        type="checkbox"
                                        {{
                                            $extraInputAttributeBag
                                                ->merge([
                                                    'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                                    'value' => $value,
                                                    'wire:loading.attr' => 'disabled',
                                                    $wireModelAttribute => $statePath,
                                                    'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                                ], escape: false)
                                                ->class([
                                                    'fi-checkbox-input',
                                                    'fi-valid' => ! $errors->has($statePath),
                                                    'fi-invalid' => $errors->has($statePath),
                                                ])
                                        }}
                                    />

                                    <div class="fi-fo-checkbox-list-option-text">
                                        <span class="fi-fo-checkbox-list-option-label">
                                            @if ($isHtmlAllowed)
                                                {!! $label !!}
                                            @else
                                                {{ $label }}
                                            @endif
                                        </span>

                                        @if ($hasDescription($value))
                                            <p
                                                class="fi-fo-checkbox-list-option-description"
                                            >
                                                {{ $getDescription($value) }}
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div wire:key="{{ $livewireKey }}.group.{{ $groupKey }}.empty"></div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div wire:key="{{ $livewireKey }}.empty"></div>
            @endforelse
        </div>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="search && ! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>

<style>
    .fi-fo-checkbox-list-groups {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .fi-fo-checkbox-list-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .fi-fo-checkbox-list-group-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgb(var(--gray-200));
        margin-bottom: 0.25rem;
    }

    .dark .fi-fo-checkbox-list-group-header {
        border-bottom-color: rgb(var(--gray-700));
    }

    .fi-fo-checkbox-list-group-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.25rem;
        height: 1.25rem;
        color: rgb(var(--gray-500));
    }

    .fi-fo-checkbox-list-group-icon svg {
        width: 100%;
        height: 100%;
    }

    .fi-fo-checkbox-list-group-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: rgb(var(--gray-700));
        margin: 0;
    }

    .dark .fi-fo-checkbox-list-group-label {
        color: rgb(var(--gray-300));
    }

    .fi-fo-checkbox-list-options-grouped {
        margin-left: 0;
    }
</style>
