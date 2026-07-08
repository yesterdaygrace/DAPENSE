<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\OtorisatorController;

class OtorisatorControllerOperator extends OtorisatorController
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
