<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\CoaController;

class CoaControllerOperator extends CoaController
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
