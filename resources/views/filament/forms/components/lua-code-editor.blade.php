@php
    use Filament\Support\Facades\FilamentAsset;

    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    label-tag="div"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class(['fi-fo-code-editor'])"
    >
        {{-- Must stay the Alpine element's previous sibling: observeFieldAccessibility() reads aria state from it. --}}
        <div hidden data-field-accessibility {{ $getExtraAlpineAttributeBag()->merge($field->getAccessibilityAttributes(), escape: false)->only(['aria-describedby', 'aria-invalid']) }}></div>
        <div
            aria-labelledby="{{ $id }}-label"
            id="{{ $id }}"
            role="group"
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('lua-code-editor') }}"
            x-data="luaCodeEditorFormComponent({
                        api: @js($getApiCompletions()),
                        canWrap: @js($canWrap()),
                        isDisabled: @js($isDisabled),
                        isLive: @js($isLive()),
                        isLiveDebounced: @js($isLiveDebounced()),
                        isLiveOnBlur: @js($isLiveOnBlur()),
                        label: @js($getLabel()),
                        liveDebounce: @js($getLiveDebounce()),
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
                    })"
            wire:ignore
            wire:key="{{ $getLivewireKey() }}.{{ $isDisabled ? 'disabled' : 'enabled' }}"
            {{ $getExtraAlpineAttributeBag() }}
        >
            <div x-ref="editor" x-cloak></div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
