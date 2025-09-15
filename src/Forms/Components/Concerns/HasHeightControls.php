<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Forms\Components\Concerns;

trait HasHeightControls
{
    protected ?string $maxHeight = '400px';

    protected ?string $minHeight = '200px';

    public function maxHeight(?string $height): static
    {
        $this->maxHeight = $height;

        return $this;
    }

    public function minHeight(?string $height): static
    {
        $this->minHeight = $height;

        return $this;
    }

    public function getMaxHeight(): ?string
    {
        return $this->maxHeight;
    }

    public function getMinHeight(): ?string
    {
        return $this->minHeight;
    }
}
