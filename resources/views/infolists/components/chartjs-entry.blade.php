@php
    use Filament\Support\Facades\FilamentView;
	
    $data = $getState();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div @class(["border rounded-lg p-4 bg-white" => $isContained()])>
        <div
            wire:ignore
            @if (FilamentView::hasSpaMode())
                x-load="visible"
            @else
                x-load
            @endif
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
            x-data="chart({
                type: @js($data['type'] ?? 'line'),
                cachedData: @js(['labels' => $data['labels'] ?? [], 'datasets' => $data['datasets'] ?? []]),
                options: @js($getOptions())
            })"
        >
            <canvas x-ref="canvas" style="max-height: {{ $data['maxHeight'] ?? '300px' }}"></canvas>
            <span x-ref="backgroundColorElement" class="text-custom-50 dark:text-custom-400/10"></span>
            <span x-ref="borderColorElement" class="text-gray-400"></span>
            <span x-ref="gridColorElement" class="text-gray-200 dark:text-gray-800"></span>
            <span x-ref="textColorElement" class="text-gray-500 dark:text-gray-400"></span>
        </div>
    </div>
</x-dynamic-component>
