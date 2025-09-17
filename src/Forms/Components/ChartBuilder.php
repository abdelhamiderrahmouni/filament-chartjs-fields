<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class ChartBuilder extends Field
{
    use HasExtraAlpineAttributes;
    use Concerns\HasOptions;
    use Concerns\HasTypes;
    use Concerns\HasHeightControls;
    use Concerns\CanBeResponsive;
    use Concerns\CanMaintainAspectRatio;

    protected string $view = 'filament-chartjs-fields::forms.components.chart-builder';

    protected array $defaultColors = [
        '#3b82f6', '#ef4444', '#10b981', '#f59e0b',
        '#8b5cf6', '#06b6d4', '#84cc16', '#f97316',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(fn () => [
            'type' => $this->getDefaultChartType(),
            'labels' => ['Étiquette 1', 'Étiquette 1'],
            'datasets' => [
                [
                    'label' => 'Données 1',
                    'data' => [0, 0],
                    'backgroundColor' => $this->defaultColors[0],
                    'borderColor' => $this->defaultColors[0],
                ],
                [
                    'label' => 'Données 2',
                    'data' => [0, 0],
                    'backgroundColor' => $this->defaultColors[0],
                    'borderColor' => $this->defaultColors[0],
                ],
            ],
        ]);

        $this->afterStateHydrated(function (ChartBuilder $component, $state) {
            if (is_array($state)) {
                $component->state($this->normalizeState($state));
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            return $this->validateAndCleanState($state);
        });
    }

    public function getState(): array
    {
        return parent::getState() ?? self::getDefaultState() ?? [];
    }

    public function defaultColors(array $colors): static
    {
        $this->defaultColors = $colors;

        return $this;
    }

    // Getters
    public function getDefaultColors(): array
    {
        return $this->defaultColors;
    }

    // Protected helper methods
    protected function normalizeState(array $state): array
    {
        return [
            'type' => $state['type'] ?? $this->getDefaultChartType(),
            'labels' => $this->normalizeLabels($state['labels'] ?? []),
            'datasets' => $this->normalizeDatasets($state['datasets'] ?? []),
        ];
    }

    protected function normalizeLabels($labels): array
    {
        if (is_array($labels)) {

            return array_values(array_filter(array_map('trim', $labels), fn ($label) => ! empty($label)));
        }

        return [];
    }

    protected function validateAndCleanState($state): array
    {
        if (! is_array($state)) {
            return [];
        }

        $cleanState = [];

        // Validate chart type
        $chartTypes = $this->getChartTypes();
        $cleanState['type'] = in_array($state['type'] ?? null, $chartTypes)
            ? $state['type']
            : $this->getDefaultChartType();

        // Clean labels as array
        $cleanState['labels'] = $this->cleanLabelsArray($state['labels'] ?? []);

        // Clean datasets
        $cleanState['datasets'] = $this->cleanDatasets($state['datasets'] ?? []);

        return $cleanState;
    }

    protected function cleanLabelsArray($labels): array
    {
        if (! is_array($labels)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', $labels)));
    }

    protected function normalizeDatasets(array $datasets): array
    {
        if (empty($datasets)) {
            return [ // TODO: check if this should be empty
                [
                    'label' => 'Données 1',
                    'data' => [0],
                    'backgroundColor' => $this->defaultColors[0],
                    'borderColor' => $this->defaultColors[0],
                ],
            ];
        }

        return array_map(function ($dataset, $index) {
            return [
                'label' => $dataset['label'] ?? ('Dataset '.($index + 1)),
                'data' => $dataset['data'],
                'backgroundColor' => $dataset['backgroundColor'] ?? $this->defaultColors[$index % count($this->defaultColors)],
                'borderColor' => $dataset['borderColor'] ?? $dataset['backgroundColor'] ?? $this->defaultColors[$index % count($this->defaultColors)],
            ];
        }, $datasets, array_keys($datasets));
    }

    protected function cleanDatasets(array $datasets): array
    {
        return array_filter(array_map(function ($dataset) {
            if (! is_array($dataset)) {
                return null;
            }

            $cleanDataset = [
                'label' => trim($dataset['label'] ?? ''),
                'data' => $this->cleanDataString($dataset['data'] ?? ''),
                'backgroundColor' => $this->validateColor($dataset['backgroundColor'] ?? '#3b82f6'),
                'borderColor' => $this->validateColor($dataset['borderColor'] ?? $dataset['backgroundColor'] ?? '#3b82f6'),
            ];

            // Only include datasets with valid data
            return ! empty($cleanDataset['data']) ? $cleanDataset : null;
        }, $datasets));
    }

    protected function cleanDataString(string $data): array
    {
        if (empty(trim($data))) {
            return [];
        }

        return array_filter(array_map(function ($value) {
            $cleaned = trim($value);

            return is_numeric($cleaned) ? (float) $cleaned : null;
        }, explode(',', $data)), fn ($value) => $value !== null);
    }

    protected function validateColor(string $color): string
    {
        // Basic hex color validation
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color;
        }

        // Return default color if invalid
        return '#3b82f6';
    }

    // Convenience methods for common chart configurations
    public function enableAnimation(bool $enabled = true): static
    {
        $options = $this->options;
        $options['animation'] = ['duration' => $enabled ? 1000 : 0];

        return $this->options($options);
    }

    public function enableGrid(bool $enabled = true): static
    {
        $options = $this->options;
        $options['scales']['x']['grid']['display'] = $enabled;
        $options['scales']['y']['grid']['display'] = $enabled;

        return $this->options($options);
    }
}
