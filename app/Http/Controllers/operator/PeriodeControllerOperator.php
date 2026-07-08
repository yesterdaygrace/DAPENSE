<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\PeriodeController;

class PeriodeControllerOperator extends PeriodeController
{
    protected function viewPrefix(): string
    {
        return 'operator';
    }

    protected function routePrefix(): string
    {
        return 'operator';
    }
}
