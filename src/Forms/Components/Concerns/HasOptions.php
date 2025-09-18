<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

use AbdelhamidErrahmouni\ChartBuilder\Services\PhpArrayTransformer;
use Closure;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Arrayable;

trait HasOptions
{
    protected array | Arrayable | RawJs | Closure $options = [];

    protected bool | Closure $mergeOptions = false;

    public function options(array | Arrayable | RawJs | Closure $options, bool | Closure $merge = false): static
    {
        $this->options = $options;
        $this->mergeOptions = $merge;

        return $this;
    }

    public function getOptions(): RawJs
    {
        $userOptions = $this->evaluate($this->options);

        if ((bool) $this->evaluate($this->mergeOptions)) {
            if ($userOptions instanceof Arrayable) {
                $userOptions = $userOptions->toArray();
            }

            $userOptions = is_array($userOptions) ? $userOptions : [];
            $options = array_replace_recursive($this->getDefaultOptions(), $userOptions);

            return RawJs::make(PhpArrayTransformer::toJsLiteral($options));
        }

        if ($userOptions instanceof RawJs) {
            return $userOptions;
        }

        $options = array_merge($this->getDefaultOptions(), $userOptions);

        return RawJs::make(PhpArrayTransformer::toJsLiteral($options));
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
                    'callback' => [
                        'label' => 'function(context) {
                            const longText = context.label + \': \' + context.raw;
                            let parts = longText.match(/.{1,20}/g);
                            return parts;
                        }',
                    ]
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'callback' => 'function(value) { const label = this.getLabelForValue(value); return label.length > 40 ? label.slice(0, 37) + \'…\' : label }',
                    ],
                    'display' => true,
                    'grid' => [
                        'display' => true,
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'callback' => 'function(value) { const label = this.getLabelForValue(value); return label.length > 30 ? label.slice(0, 27) + \'…\' : label }',
                    ],
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
