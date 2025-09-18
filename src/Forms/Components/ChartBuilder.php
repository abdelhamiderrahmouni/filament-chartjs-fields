<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components;

use AbdelhamidErrahmouni\ChartBuilder\DataObjects\ChartData;
use AbdelhamidErrahmouni\ChartBuilder\DataObjects\Dataset;
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

        $this->default(fn (): array => ChartData::make(
                type: $this->getDefaultChartType(),
                labels: [__("filament-chartjs-fields::common.label") . ' 1'],
                datasets: [
                    Dataset::make(
                        label: __("filament-chartjs-fields::common.dataset") . ' 1',
                        data: [0, 0],
                        backgroundColor: $this->defaultColors[0],
                        borderColor: $this->defaultColors[0],
                    ),
                ]
            )->toArray()
        );
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
