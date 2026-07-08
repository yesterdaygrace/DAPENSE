<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\SaldoAwalController;

class SaldoAwalControllerOperator extends SaldoAwalController
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
