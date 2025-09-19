<?php

namespace AbdelhamidErrahmouni\ChartBuilder\DataObjects;

use Illuminate\Contracts\Support\Arrayable;

final class Dataset implements Arrayable
{
    public function __construct(
        public string $label,
        /** @var numeric[] */
        public array $data,
        public ?string $backgroundColor = null,
        public ?string $borderColor = null,
        // public ?int $borderWidth = null,
        // public ?bool $fill = null,
        // public ?string $tension = null,

        // public ?string $type = null,
        // public ?string $hoverBackgroundColor = null,
        // public ?string $hoverBorderColor = null,
        // public ?int $hoverBorderWidth = null,
        // public ?string $pointStyle = null,
        // public ?int $pointRadius = null,
        // public ?int $pointHoverRadius = null,
        // public ?string $yAxisID = null,
        // public ?string $xAxisID = null,
    ) {}

    public static function make(
        string $label,
        array $data,
        ?string $backgroundColor = null,
        ?string $borderColor = null,
        // ?int $borderWidth = null,
        // ?bool $fill = null,
        // ?string $tension = null,

        // ?string $type = null,
        // ?string $hoverBackgroundColor = null,
        // ?string $hoverBorderColor = null,
        // ?int $hoverBorderWidth = null,
        // ?string $pointStyle = null,
        // ?int $pointRadius = null,
        // ?int $pointHoverRadius = null,
        // ?string $yAxisID = null,
        // ?string $xAxisID = null,
    ): self {
        return new self($label, $data, $backgroundColor, $borderColor);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'data' => $this->data,
            'backgroundColor' => $this->backgroundColor,
            'borderColor' => $this->borderColor,
            // 'borderWidth' => $this->borderWidth,
            // 'fill' => $this->fill,
            // 'tension' => $this->tension,

            // 'type' => $this->type,
            // 'hoverBackgroundColor' => $this->hoverBackgroundColor,
            // 'hoverBorderColor' => $this->hoverBorderColor,
            // 'hoverBorderWidth' => $this->hoverBorderWidth,
            // 'pointStyle' => $this->pointStyle,
            // 'pointRadius' => $this->pointRadius,
            // 'pointHoverRadius' => $this->pointHoverRadius,
            // 'yAxisID' => $this->yAxisID,
            // 'xAxisID' => $this->xAxisID,
        ];
    }
}
