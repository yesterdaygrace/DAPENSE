<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\HeaderController;

class HeaderControllerOperator extends HeaderController
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
