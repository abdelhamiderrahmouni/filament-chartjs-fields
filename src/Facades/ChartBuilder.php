<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AbdelhamidErrahmouni\ChartBuilder\Forms\Components\ChartBuilder
 */
class ChartBuilder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \AbdelhamidErrahmouni\ChartBuilder\Forms\Components\ChartBuilder::class;
    }
}
