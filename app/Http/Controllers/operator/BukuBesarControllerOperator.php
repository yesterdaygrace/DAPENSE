<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Base\BukuBesarController;

class BukuBesarControllerOperator extends BukuBesarController
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
