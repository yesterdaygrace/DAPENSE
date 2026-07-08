<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\NeracaSaldoController;

class NeracaSaldoControllerOperator extends NeracaSaldoController
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
