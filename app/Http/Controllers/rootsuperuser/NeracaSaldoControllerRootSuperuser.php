<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\NeracaSaldoController;

class NeracaSaldoControllerRootSuperuser extends NeracaSaldoController
{
    protected function viewPrefix(): string
    {
        return 'rootsuperuser';
    }

    protected function routePrefix(): string
    {
        return 'rootsuperuser';
    }
}
