<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\HeaderCoaController;

class HeaderCoaControllerOperator extends HeaderCoaController
{
    protected function viewPrefix(): string
    {
        return 'operator';
    }
}
