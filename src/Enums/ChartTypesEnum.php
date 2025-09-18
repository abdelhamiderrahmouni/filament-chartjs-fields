<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Enums;

use Filament\Support\Contracts\HasLabel;

enum ChartTypesEnum: string implements HasLabel
{
    case BAR = 'bar';
    case LINE = 'line';
    case PIE = 'pie';
    case DOUGHNUT = 'doughnut';
    case RADAR = 'radar';
    case POLAR_AREA = 'polarArea';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BAR => __("chartjs-fields.bar"),
            self::LINE => __("chartjs-fields.line"),
            self::PIE => __("chartjs-fields.pie"),
            self::DOUGHNUT => __("chartjs-fields.doughnut"),
            self::RADAR => __("chartjs-fields.radar"),
            self::POLAR_AREA => __("chartjs-fields.polarArea"),
        };
    }
}
