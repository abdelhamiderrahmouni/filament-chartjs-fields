<?php

namespace AbdelhamidErrahmouni\ChartBuilder\Commands;

use Illuminate\Console\Command;

class ChartBuilderCommand extends Command
{
    public $signature = 'filament-chartjs-fields';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
