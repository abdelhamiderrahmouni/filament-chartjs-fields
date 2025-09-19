<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Infolists\Components;

use AbdelhamidErrahmouni\ChartBuilder\Infolists\Components\Concerns\HasOptions;
use Filament\Infolists\Components\Entry;

class ChartJsEntry extends Entry
{
    use HasOptions;

    protected string $view = 'filament-chartjs-fields::infolists.components.chartjs-entry';

    protected bool | \Closure $contained = false;

    public function contained(bool | \Closure $condition = true): static
    {
        $this->contained = $condition;

        return $this;
    }

    public function isContained(): bool
    {
        return $this->evaluate($this->contained);
    }
}
