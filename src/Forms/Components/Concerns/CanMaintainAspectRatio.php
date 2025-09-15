<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

trait CanMaintainAspectRatio
{
    protected bool $maintainAspectRatio = true;

    public function maintainAspectRatio(bool $maintain = true): static
    {
        $this->maintainAspectRatio = $maintain;

        return $this;
    }

    public function shouldMaintainAspectRatio(): bool
    {
        return $this->maintainAspectRatio;
    }
}
