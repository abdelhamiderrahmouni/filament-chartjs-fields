@props([
    'value' => null,
])

@php
    use Filament\Support\Facades\FilamentView;
@endphp

<x-filament::input.wrapper>
    <div
        @if (FilamentView::hasSpaMode())
            {{-- format-ignore-start --}}
            x-load="visible || event (ax-modal-opened)"
        {{-- format-ignore-end --}}
        @else
            x-load
        @endif
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('coloris-js', package: 'abdelhamiderrahmouni/filament-chartjs-fields'))]"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('coloris-css', package: 'abdelhamiderrahmouni/filament-chartjs-fields'))]"
        x-data="{ state: @js($value) }"
        x-modelable="state"
        {{ $attributes->class("flex") }}
        x-init="
            Coloris({
                el: $el.querySelector('[data-coloris]'),
                themeMode: 'auto',
                theme: 'large',
                swatches: [
                    '#264653',
                    '#2a9d8f',
                    '#e9c46a',
                    '#f4a261',
                    '#e76f51',
                    '#d62828',
                    '#f77f00',
                    '#fcbf49',
                    '#eae2b7',
                    '#8ac926',
                    '#1982c4',
                    '#6a4c93',
                ],
                onChange: (color) => {
                    value = color;
                },
            });"
    >
        <x-filament::input type="text" data-coloris />
    </div>
</x-filament::input.wrapper>