<?php

namespace AbdelhamidErrahmouni\ChartBuilder\DataObjects;

use Illuminate\Contracts\Support\Arrayable;

class ChartData implements Arrayable
{
    public function __construct(
        public string $type,
        public array $labels,
        /** @var Dataset[] */
        public array $datasets,
        public ?array $options = null,
    ) {
    }

    public static function make(
        string $type,
        array $labels,
        array $datasets,
        ?array $options = null,
    ): self
    {
        return new self($type, $labels, $datasets, $options);
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'labels' => $this->labels,
            'datasets' => array_map(
                fn($dataset) => $dataset instanceof Arrayable ? $dataset->toArray(): $dataset,
                $this->datasets
            ),
            'options' => $this->options,
        ];
    }
}
