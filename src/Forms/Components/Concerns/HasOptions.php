<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Contracts\Support\Arrayable;

trait HasOptions
{
    protected array $options = [];

    public function options(array|Arrayable|Closure $options): static
    {
        $this->options = array_merge($this->getDefaultOptions(), $this->evaluate($options));

        return $this;
    }

    public function getOptions(): array
    {
        return array_merge($this->getDefaultOptions(), $this->evaluate($this->options));
    }

    protected function getDefaultOptions(): array
    {
        return [
            'responsive' => $this->responsive ?? true,
            'maintainAspectRatio' => $this->maintainAspectRatio ?? true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'intersect' => false,
                    'mode' => 'index',
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'grid' => [
                        'display' => true,
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
