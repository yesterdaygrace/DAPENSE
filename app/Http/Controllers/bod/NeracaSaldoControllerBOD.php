<?php

namespace App\Http\Controllers\bod;

use App\Http\Controllers\Base\NeracaSaldoController;

class NeracaSaldoControllerBOD extends NeracaSaldoController
{
    protected function viewPrefix(): string
    {
        return 'bod';
    }

    protected function routePrefix(): string
    {
        return 'bod';
    }
}
