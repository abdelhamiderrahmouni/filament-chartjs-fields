<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

trait HasTypes
{
    protected array $chartTypes = ['bar', 'line', 'pie', 'doughnut', 'radar', 'polarArea'];

    public function chartTypes(array | Arrayable | Closure $types): static
    {
        $this->chartTypes = $this->evaluate($types);

        return $this;
    }

    public function getChartTypes(): array
    {
        return $this->evaluate($this->chartTypes);
    }

    protected function getDefaultChartType(): string
    {
        $types = $this->getChartTypes();

        return ! empty($types) ? $types[0] : 'line';
    }

    public function line(): static
    {
        return $this->chartTypes(['line']);
    }

    public function bar(): static
    {
        return $this->chartTypes(['bar']);
    }

    public function pie(): static
    {
        return $this->chartTypes(['pie', 'doughnut']);
    }
}
