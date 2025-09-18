<?php

it('will not use debugging functions')
    ->expect(['dd', 'ddd', 'die',  'dump', 'ray', 'sleep', 'usleep', 'exit', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();
