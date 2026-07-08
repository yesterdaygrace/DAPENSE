<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Http\Controllers\Base\SaldoAwalController;

class SaldoAwalControllerRootSuperuser extends SaldoAwalController
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
