<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

trait CanBeResponsive
{
    protected bool $responsive = true;

    public function responsive(bool $responsive = true): static
    {
        $this->responsive = $responsive;

        return $this;
    }

    public function isResponsive(): bool
    {
        return $this->responsive;
    }
}
